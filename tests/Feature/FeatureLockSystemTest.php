<?php

namespace Tests\Feature;

use App\Enums\FeatureEnum;
use App\Http\Middleware\FeatureGuard;
use App\Models\Company;
use App\Models\FeatureLock;
use App\Models\User;
use App\Services\FeatureLockService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FeatureLockSystemTest extends TestCase
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

    public function test_feature_lock_service_basic_functionality(): void
    {
        // Test: Vérifier qu'une fonctionnalité n'est pas verrouillée par défaut
        $this->assertFalse($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));

        // Test: Verrouiller une fonctionnalité
        $lock = $this->service->lockFeature(
            $this->company,
            FeatureEnum::PRODUCTS_MANAGE,
            'Test de verrouillage',
            $this->admin
        );

        $this->assertInstanceOf(FeatureLock::class, $lock);
        $this->assertTrue($lock->is_locked);
        $this->assertEquals('Test de verrouillage', $lock->reason);
        $this->assertTrue($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));

        // Test: Déverrouiller une fonctionnalité
        $this->service->unlockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE, $this->admin);
        $this->assertFalse($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));
    }

    public function test_feature_lock_with_expiration(): void
    {
        $expiresAt = Carbon::now()->addHours(1);

        // Verrouiller avec expiration
        $lock = $this->service->lockFeature(
            $this->company,
            FeatureEnum::PRODUCTS_MANAGE,
            'Test avec expiration',
            $this->admin,
            $expiresAt
        );

        $this->assertEquals($expiresAt->toDateTimeString(), $lock->expires_at->toDateTimeString());
        $this->assertTrue($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));

        // Simuler une expiration (mise à jour directe en DB, invalider le cache)
        $lock->update(['expires_at' => Carbon::now()->subHour()]);
        $this->service->clearFeatureCache($this->company);

        // Vérifier que le verrouillage est considéré comme expiré
        $this->assertFalse($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));
    }

    public function test_bulk_feature_operations(): void
    {
        $features = [
            FeatureEnum::PRODUCTS_MANAGE->value,
            FeatureEnum::SALES_CREATE->value,
            FeatureEnum::STOCK_MANAGE->value,
        ];

        // Test verrouillage en lot
        $locks = $this->service->bulkLockFeatures(
            $this->company,
            $features,
            'Verrouillage en lot',
            $this->admin
        );

        $this->assertCount(3, $locks);

        foreach ($features as $feature) {
            $this->assertTrue($this->service->isFeatureLocked($this->company, $feature));
        }

        // Test déverrouillage en lot
        $unlockedCount = $this->service->bulkUnlockFeatures($this->company, $features, $this->admin);

        $this->assertEquals(3, $unlockedCount);

        foreach ($features as $feature) {
            $this->assertFalse($this->service->isFeatureLocked($this->company, $feature));
        }
    }

    public function test_global_statistics(): void
    {
        $company2 = Company::factory()->create();

        // Créer des verrouillages
        $this->service->lockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE);
        $this->service->lockFeature($this->company, FeatureEnum::SALES_CREATE);
        $this->service->lockFeature($company2, FeatureEnum::PRODUCTS_MANAGE);

        $stats = $this->service->getGlobalLockStats();

        $this->assertEquals(2, $stats['total_companies']);
        $this->assertEquals(3, $stats['total_active_locks']);
        $this->assertEquals(2, $stats['companies_with_locks']);
        $this->assertEquals(1.5, $stats['average_locks_per_company']);
    }

    public function test_middleware_blocks_locked_features(): void
    {
        // Créer un utilisateur de l'entreprise
        $user = User::factory()->create(['company_id' => $this->company->id]);

        // Verrouiller une fonctionnalité
        $this->service->lockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE);

        // Enregistrer les chemins de vues d'erreurs (normalement fait par l'ExceptionHandler)
        (new \Illuminate\Foundation\Exceptions\RegisterErrorViewPaths)();

        // Authentifier l'utilisateur via Auth pour que le middleware le détecte
        Auth::login($user);

        $request = \Illuminate\Http\Request::create('/test', 'GET');

        $middleware = app(FeatureGuard::class);

        $response = $middleware->handle($request, function () {
            return response('Success');
        }, FeatureEnum::PRODUCTS_MANAGE->value);

        // Le middleware retourne une page d'erreur 403
        $this->assertEquals(403, $response->getStatusCode());

        Auth::logout();
    }

    public function test_cache_performance(): void
    {
        $cacheKey = "feature_lock:{$this->company->id}:".FeatureEnum::PRODUCTS_MANAGE->value;

        // Premier appel - devrait mettre en cache
        $result1 = $this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE);
        $this->assertTrue(Cache::has($cacheKey));

        // Deuxième appel - devrait utiliser le cache
        $result2 = $this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE);
        $this->assertEquals($result1, $result2);

        // Verrouiller la fonctionnalité - devrait invalider le cache
        $this->service->lockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE);

        // Vérifier que le nouveau statut est correct
        $this->assertTrue($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));
    }

    public function test_cleanup_expired_locks(): void
    {
        // Créer des verrouillages expirés et actifs
        FeatureLock::create([
            'company_id' => $this->company->id,
            'feature_key' => FeatureEnum::PRODUCTS_MANAGE->value,
            'is_locked' => true,
            'expires_at' => Carbon::now()->subHour(), // Expiré
            'reason' => 'Test expiré',
        ]);

        FeatureLock::create([
            'company_id' => $this->company->id,
            'feature_key' => FeatureEnum::SALES_CREATE->value,
            'is_locked' => true,
            'expires_at' => Carbon::now()->addHour(), // Actif
            'reason' => 'Test actif',
        ]);

        $cleanedCount = $this->service->cleanupExpiredLocks();

        $this->assertEquals(1, $cleanedCount);
        $this->assertEquals(1, FeatureLock::where('is_locked', true)->count());
    }

    public function test_company_lock_report(): void
    {
        // Créer différents types de verrouillages
        $this->service->lockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE, 'Verrouillage actif');

        FeatureLock::create([
            'company_id' => $this->company->id,
            'feature_key' => FeatureEnum::SALES_CREATE->value,
            'is_locked' => true,
            'expires_at' => Carbon::now()->subHour(), // Expiré
            'reason' => 'Verrouillage expiré',
        ]);

        $report = $this->service->getCompanyLockReport($this->company);

        $this->assertIsArray($report);
        $this->assertNotEmpty($report);

        // Vérifier la structure du rapport
        foreach ($report as $categoryData) {
            $this->assertArrayHasKey('category', $categoryData);
            $this->assertArrayHasKey('features', $categoryData);
            $this->assertArrayHasKey('locked_count', $categoryData);
            $this->assertArrayHasKey('total_count', $categoryData);
            $this->assertIsArray($categoryData['features']);
        }
    }

    public function test_feature_enum_integration(): void
    {
        // Tester avec différents types de paramètres

        // Test avec FeatureEnum
        $this->service->lockFeature($this->company, FeatureEnum::PRODUCTS_MANAGE);
        $this->assertTrue($this->service->isFeatureLocked($this->company, FeatureEnum::PRODUCTS_MANAGE));

        // Test avec string
        $this->service->lockFeature($this->company, FeatureEnum::SALES_CREATE->value);
        $this->assertTrue($this->service->isFeatureLocked($this->company, FeatureEnum::SALES_CREATE->value));

        // Vérifier la cohérence
        $this->assertTrue($this->service->isFeatureLocked($this->company, FeatureEnum::SALES_CREATE));
    }
}
