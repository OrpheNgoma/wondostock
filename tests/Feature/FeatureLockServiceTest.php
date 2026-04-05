<?php

namespace Tests\Feature;

use App\Enums\FeatureEnum;
use App\Models\Company;
use App\Models\FeatureLock;
use App\Models\User;
use App\Services\FeatureLockService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FeatureLockServiceTest extends TestCase
{
    use RefreshDatabase;

    private FeatureLockService $service;

    private Company $company;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(FeatureLockService::class);
        $this->company = Company::factory()->create();
        $this->admin = User::factory()->create(['is_global_admin' => true]);

        // Nettoyer le cache avant chaque test
        Cache::flush();
    }

    public function test_can_check_if_feature_is_locked(): void
    {
        // Créer un verrouillage actif
        FeatureLock::factory()->create([
            'company_id' => $this->company->id,
            'feature_key' => FeatureEnum::PRODUCTS_MANAGE->value,
            'is_locked' => true,
            'expires_at' => null,
        ]);

        $this->assertTrue($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));
        $this->assertFalse($this->service->isFeatureLocked($this->company, FeatureEnum::SALES_CREATE));
    }

    public function test_can_check_if_feature_is_unlocked(): void
    {
        // Créer un verrouillage inactif
        FeatureLock::factory()->create([
            'company_id' => $this->company->id,
            'feature_key' => FeatureEnum::PRODUCTS_MANAGE->value,
            'is_locked' => false,
            'expires_at' => null,
        ]);

        $this->assertFalse($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));
    }

    public function test_expired_locks_are_not_active(): void
    {
        // Créer un verrouillage expiré
        FeatureLock::factory()->create([
            'company_id' => $this->company->id,
            'feature_key' => FeatureEnum::PRODUCTS_MANAGE->value,
            'is_locked' => true,
            'expires_at' => Carbon::now()->subHour(),
        ]);

        $this->assertFalse($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));
    }

    public function test_can_lock_feature(): void
    {
        $lock = $this->service->lockFeature(
            $this->company,
            FeatureEnum::PRODUCTS_MANAGE,
            'Test reason',
            $this->admin
        );

        $this->assertInstanceOf(FeatureLock::class, $lock);
        $this->assertTrue($lock->is_locked);
        $this->assertEquals('Test reason', $lock->reason);
        $this->assertEquals($this->admin->id, $lock->locked_by);
        $this->assertTrue($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));
    }

    public function test_can_lock_feature_with_expiration(): void
    {
        $expiresAt = Carbon::now()->addDays(7);

        $lock = $this->service->lockFeature(
            $this->company,
            FeatureEnum::PRODUCTS_MANAGE,
            'Test reason',
            $this->admin,
            $expiresAt
        );

        $this->assertEquals($expiresAt->toDateTimeString(), $lock->expires_at->toDateTimeString());
    }

    public function test_can_unlock_feature(): void
    {
        // Créer un verrouillage
        $lock = $this->service->lockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE);
        $this->assertTrue($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));

        // Déverrouiller
        $this->service->unlockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE, $this->admin);
        $this->assertFalse($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));
    }

    public function test_can_bulk_lock_features(): void
    {
        $features = [FeatureEnum::PRODUCTS_MANAGE, FeatureEnum::SALES_CREATE, FeatureEnum::STOCK_MANAGE];

        $locks = $this->service->bulkLockFeatures(
            $this->company,
            $features,
            'Bulk test reason',
            $this->admin
        );

        $this->assertCount(3, $locks);

        foreach ($features as $feature) {
            $this->assertTrue($this->service->isFeatureLocked($this->company, $feature));
        }
    }

    public function test_can_bulk_unlock_features(): void
    {
        $features = [FeatureEnum::PRODUCTS_MANAGE, FeatureEnum::SALES_CREATE, FeatureEnum::STOCK_MANAGE];

        // Verrouiller d'abord
        $this->service->bulkLockFeatures($this->company, $features);

        // Puis déverrouiller
        $unlockedCount = $this->service->bulkUnlockFeatures($this->company, $features, $this->admin);

        $this->assertEquals(3, $unlockedCount);

        foreach ($features as $feature) {
            $this->assertFalse($this->service->isFeatureLocked($this->company, $feature));
        }
    }

    public function test_can_get_company_locked_features(): void
    {
        // Verrouiller quelques fonctionnalités
        $this->service->lockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE);
        $this->service->lockFeature($this->company, FeatureEnum::SALES_CREATE);

        $lockedFeatures = $this->service->getCompanyLockedFeatures($this->company);

        $this->assertCount(2, $lockedFeatures);
        $this->assertContains(FeatureEnum::PRODUCTS_MANAGE->value, $lockedFeatures->pluck('feature_key')->toArray());
        $this->assertContains(FeatureEnum::SALES_CREATE->value, $lockedFeatures->pluck('feature_key')->toArray());
    }

    public function test_can_cleanup_expired_locks(): void
    {
        // Créer des verrouillages expirés et actifs
        FeatureLock::factory()->create([
            'company_id' => $this->company->id,
            'feature_key' => FeatureEnum::PRODUCTS_MANAGE->value,
            'is_locked' => true,
            'expires_at' => Carbon::now()->subHour(), // Expiré
        ]);

        FeatureLock::factory()->create([
            'company_id' => $this->company->id,
            'feature_key' => FeatureEnum::SALES_CREATE->value,
            'is_locked' => true,
            'expires_at' => Carbon::now()->addHour(), // Actif
        ]);

        $cleanedCount = $this->service->cleanupExpiredLocks();

        $this->assertEquals(1, $cleanedCount);
        $this->assertEquals(1, FeatureLock::active()->count());
    }

    public function test_can_get_global_lock_stats(): void
    {
        $company2 = Company::factory()->create();

        // Créer des verrouillages pour différentes entreprises
        $this->service->lockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE);
        $this->service->lockFeature($this->company, FeatureEnum::SALES_CREATE);
        $this->service->lockFeature($company2, FeatureEnum::PRODUCTS_MANAGE);

        $stats = $this->service->getGlobalLockStats();

        $this->assertEquals(2, $stats['total_companies']);
        $this->assertEquals(3, $stats['total_active_locks']);
        $this->assertEquals(2, $stats['companies_with_locks']);
        $this->assertEquals(1.5, $stats['average_locks_per_company']);
    }

    public function test_cache_is_used_for_performance(): void
    {
        // Premier appel - devrait mettre en cache
        $result1 = $this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE);

        // Vérifier que la clé de cache existe
        $cacheKey = "feature_lock:{$this->company->id}:".FeatureEnum::PRODUCTS_MANAGE->value;
        $this->assertTrue(Cache::has($cacheKey));

        // Deuxième appel - devrait utiliser le cache
        $result2 = $this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE);

        $this->assertEquals($result1, $result2);
    }

    public function test_can_handle_string_feature_keys(): void
    {
        // Tester avec une clé de fonctionnalité en string valide
        $lock = $this->service->lockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE->value);

        $this->assertInstanceOf(FeatureLock::class, $lock);
        $this->assertEquals(FeatureEnum::PRODUCTS_MANAGE->value, $lock->feature_key);
    }

    public function test_bulk_operations_handle_duplicates_gracefully(): void
    {
        // Verrouiller une fonctionnalité
        $this->service->lockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE);

        // Essayer de verrouiller en lot avec des doublons
        $features = [FeatureEnum::PRODUCTS_MANAGE, FeatureEnum::SALES_CREATE];
        $locks = $this->service->bulkLockFeatures($this->company, $features);

        // bulkLockFeatures utilise updateOrCreate : retourne toutes les fonctionnalités traitées
        $this->assertCount(2, $locks);
        $this->assertTrue($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));
        $this->assertTrue($this->service->isFeatureLocked($this->company, FeatureEnum::SALES_CREATE));
    }
}
