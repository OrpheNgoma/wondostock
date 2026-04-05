<?php

namespace App\Livewire\GlobalAdmin;

use App\Enums\FeatureEnum;
use App\Models\Company;
use App\Models\FeatureLock;
use App\Services\FeatureLockService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class FeatureLockManager extends Component
{
    use WithPagination;

    // Filtres et recherche
    public string $search = '';

    public string $selectedCompany = '';

    public string $selectedCategory = '';

    public string $statusFilter = 'all'; // all, locked, unlocked

    // Modal de verrouillage
    public bool $showLockModal = false;

    public array $selectedCompanies = [];

    public array $selectedFeatures = [];

    public string $lockReason = '';

    public string $expiresAt = '';

    public bool $bulkMode = false;

    // Modal de détails
    public bool $showDetailsModal = false;

    public ?FeatureLock $selectedLock = null;

    // États
    public bool $loading = false;

    public function mount(): void
    {
        // Vérifier les permissions d'admin global
        if (! $this->canManage()) {
            abort(403, 'Accès non autorisé');
        }
    }

    public function render()
    {
        return view('livewire.global-admin.feature-lock-manager', [
            'companies' => $this->getFilteredCompanies(),
            'featureCategories' => FeatureEnum::getGroupedFeatures(),
            'globalStats' => $this->getGlobalStats(),
            'recentLocks' => $this->getRecentLocks(),
        ])->layout('components.layouts.admin');
    }

    #[Computed]
    public function getFilteredCompanies()
    {
        $query = Company::query()
            ->addSelect([
                'active_locks_count' => FeatureLock::selectRaw('COUNT(*)')
                    ->whereColumn('company_id', 'companies.id')
                    ->where('is_locked', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    }),
            ]);

        // Recherche par nom d'entreprise
        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        // Filtre par entreprise spécifique
        if ($this->selectedCompany) {
            $query->where('id', $this->selectedCompany);
        }

        // Filtre par statut de verrouillage
        if ($this->statusFilter === 'locked') {
            $query->whereExists(function ($q) {
                $q->select('id')
                    ->from('feature_locks')
                    ->whereColumn('company_id', 'companies.id')
                    ->where('is_locked', true)
                    ->where(function ($subQ) {
                        $subQ->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    });
            });
        } elseif ($this->statusFilter === 'unlocked') {
            $query->whereNotExists(function ($q) {
                $q->select('id')
                    ->from('feature_locks')
                    ->whereColumn('company_id', 'companies.id')
                    ->where('is_locked', true)
                    ->where(function ($subQ) {
                        $subQ->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    });
            });
        }

        return $query->paginate(10);
    }

    #[Computed]
    public function getGlobalStats(): array
    {
        $service = app(FeatureLockService::class);

        return $service->getGlobalLockStats();
    }

    #[Computed]
    public function getRecentLocks(): Collection
    {
        return FeatureLock::with(['company', 'lockedBy'])
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function getAllCompanies(): Collection
    {
        return Company::orderBy('name')->get();
    }

    public function getCompanyFeatureLocks(int $companyId)
    {
        return FeatureLock::where('company_id', $companyId)
            ->where('is_locked', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->get();
    }

    public function openLockModal(bool $bulk = false): void
    {
        $this->bulkMode = $bulk;
        $this->resetModalData();
        $this->showLockModal = true;
    }

    public function openLockModalForCompany(int $companyId): void
    {
        $this->bulkMode = false;
        $this->resetModalData();
        $this->selectedCompanies = [$companyId];
        $this->showLockModal = true;
    }

    public function openDetailsModal(int $lockId): void
    {
        $this->selectedLock = FeatureLock::with(['company', 'lockedBy'])->find($lockId);
        $this->showDetailsModal = true;
    }

    public function closeModals(): void
    {
        $this->showLockModal = false;
        $this->showDetailsModal = false;
        $this->resetModalData();
    }

    public function lockFeatures(): void
    {
        $this->validate([
            'selectedCompanies' => 'required|array|min:1',
            'selectedFeatures' => 'required|array|min:1',
            'lockReason' => 'required|string|max:500',
            'expiresAt' => 'nullable|date|after:now',
        ], [
            'selectedCompanies.required' => 'Veuillez sélectionner au moins une entreprise.',
            'selectedFeatures.required' => 'Veuillez sélectionner au moins une fonctionnalité.',
            'lockReason.required' => 'Veuillez indiquer la raison du verrouillage.',
        ]);

        $this->loading = true;

        try {
            $service = app(FeatureLockService::class);
            $admin = Auth::user();
            $expiresAt = $this->expiresAt ? Carbon::parse($this->expiresAt) : null;

            $totalLocked = 0;

            foreach ($this->selectedCompanies as $companyId) {
                $company = Company::find($companyId);
                if (! $company) {
                    continue;
                }

                $locks = $service->bulkLockFeatures(
                    $company,
                    $this->selectedFeatures,
                    $this->lockReason,
                    $admin,
                    $expiresAt
                );

                $totalLocked += $locks->count();
            }

            $this->dispatch('notify',
                "✅ {$totalLocked} fonctionnalité(s) verrouillée(s) avec succès.",
                'success'
            );

            $this->closeModals();
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('notify',
                "❌ Erreur lors du verrouillage: {$e->getMessage()}",
                'error'
            );
        } finally {
            $this->loading = false;
        }
    }

    public function unlockFeature(int $companyId, string $featureKey): void
    {
        try {
            $service = app(FeatureLockService::class);
            $company = Company::find($companyId);

            if (! $company) {
                throw new \Exception('Entreprise introuvable');
            }

            $service->unlockFeature($company, $featureKey, Auth::user());

            $featureEnum = FeatureEnum::tryFrom($featureKey);
            $featureName = $featureEnum ? $featureEnum->getDescription() : $featureKey;

            $this->dispatch('notify',
                "✅ Fonctionnalité '{$featureName}' déverrouillée pour {$company->name}.",
                'success'
            );

            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('notify',
                "❌ Erreur lors du déverrouillage: {$e->getMessage()}",
                'error'
            );
        }
    }

    public function bulkUnlock(): void
    {
        if (empty($this->selectedCompanies) || empty($this->selectedFeatures)) {
            $this->dispatch('notify',
                '⚠️ Veuillez sélectionner des entreprises et des fonctionnalités.',
                'warning'
            );

            return;
        }

        try {
            $service = app(FeatureLockService::class);
            $admin = Auth::user();
            $totalUnlocked = 0;

            foreach ($this->selectedCompanies as $companyId) {
                $company = Company::find($companyId);
                if (! $company) {
                    continue;
                }

                $unlocked = $service->bulkUnlockFeatures(
                    $company,
                    $this->selectedFeatures,
                    $admin
                );

                $totalUnlocked += $unlocked;
            }

            $this->dispatch('notify',
                "✅ {$totalUnlocked} fonctionnalité(s) déverrouillée(s) avec succès.",
                'success'
            );

            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('notify',
                "❌ Erreur lors du déverrouillage en lot: {$e->getMessage()}",
                'error'
            );
        }
    }

    public function clearExpiredLocks(): void
    {
        try {
            $service = app(FeatureLockService::class);
            $cleaned = $service->cleanupExpiredLocks();

            if ($cleaned > 0) {
                $this->dispatch('notify',
                    "🧹 {$cleaned} verrouillage(s) expiré(s) nettoyé(s).",
                    'success'
                );
                $this->resetPage();
            } else {
                $this->dispatch('notify',
                    'ℹ️ Aucun verrouillage expiré trouvé.',
                    'info'
                );
            }

        } catch (\Exception $e) {
            $this->dispatch('notify',
                "❌ Erreur lors du nettoyage: {$e->getMessage()}",
                'error'
            );
        }
    }

    public function getCompanyLockReport(int $companyId): array
    {
        $service = app(FeatureLockService::class);
        $company = Company::find($companyId);

        return $company ? $service->getCompanyLockReport($company) : [];
    }

    public function exportLockReport(): void
    {
        // TODO: Implémenter l'export CSV/Excel
        $this->dispatch('notify',
            'ℹ️ Fonctionnalité d\'export en cours de développement.',
            'info'
        );
    }

    #[On('refresh-locks')]
    public function refreshData(): void
    {
        $this->resetPage();
        $this->dispatch('notify', 'Données rafraîchies.', 'info');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCompany(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    private function resetModalData(): void
    {
        $this->selectedCompanies = [];
        $this->selectedFeatures = [];
        $this->lockReason = '';
        $this->expiresAt = '';
        $this->selectedLock = null;
    }

    private function canManage(): bool
    {
        $user = Auth::user();

        return $user && ($user->is_global_admin || $user->hasRole('Global-Admin'));
    }
}
