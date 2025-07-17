<?php

namespace App\Listeners;

use App\Services\DashboardCacheService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;

class InvalidateDashboardCacheListener implements ShouldQueue
{
    private DashboardCacheService $dashboardCacheService;

    public function __construct(DashboardCacheService $dashboardCacheService)
    {
        $this->dashboardCacheService = $dashboardCacheService;
    }

    public function handle($event): void
    {
        $model = $this->extractModelFromEvent($event);
        
        if (!$model || !$this->shouldInvalidateCache($model)) {
            return;
        }

        $companyId = $this->getCompanyIdFromModel($model);
        
        if ($companyId) {
            $this->dashboardCacheService->invalidateKPIs($companyId);
        }
    }

    private function extractModelFromEvent($event): ?Model
    {
        // Pour les événements Eloquent standard
        if (isset($event->model)) {
            return $event->model;
        }

        // Pour les événements personnalisés qui peuvent contenir un model
        if (is_object($event) && property_exists($event, 'model')) {
            return $event->model;
        }

        return null;
    }

    private function shouldInvalidateCache(Model $model): bool
    {
        $relevantModels = [
            'App\Models\Document',
            'App\Models\DocumentItem',
            'App\Models\Product',
            'App\Models\Customer',
            'App\Models\StockMovement',
        ];

        return in_array(get_class($model), $relevantModels);
    }

    private function getCompanyIdFromModel(Model $model): ?int
    {
        // Directement si le modèle a company_id
        if (isset($model->company_id)) {
            return $model->company_id;
        }

        // Via des relations
        if (method_exists($model, 'company') && $model->company) {
            return $model->company->id;
        }

        if (method_exists($model, 'document') && $model->document && isset($model->document->company_id)) {
            return $model->document->company_id;
        }

        if (method_exists($model, 'product') && $model->product && isset($model->product->company_id)) {
            return $model->product->company_id;
        }

        return null;
    }
}