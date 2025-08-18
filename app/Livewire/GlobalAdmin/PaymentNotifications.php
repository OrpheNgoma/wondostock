<?php

namespace App\Livewire\GlobalAdmin;

use App\Models\PaymentNotification;
use App\Services\PaymentNotificationService;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentNotifications extends Component
{
    use WithPagination;

    public $search = '';

    public $typeFilter = '';

    public $statusFilter = '';

    public $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function generateNotifications(PaymentNotificationService $notificationService)
    {
        $stats = $notificationService->generateNotificationsForExistingInvoices();

        $total = array_sum($stats);

        if ($total > 0) {
            session()->flash('success', "✅ {$total} nouvelle(s) notification(s) générée(s) avec succès.");
        } else {
            session()->flash('info', 'ℹ️ Aucune nouvelle notification à générer.');
        }
    }

    public function processNotifications(PaymentNotificationService $notificationService)
    {
        $sentCount = $notificationService->processPendingNotifications();

        if ($sentCount > 0) {
            session()->flash('success', "✅ {$sentCount} notification(s) envoyée(s) avec succès.");
        } else {
            session()->flash('info', 'ℹ️ Aucune notification en attente d\'envoi.');
        }
    }

    public function resendNotification($notificationId, PaymentNotificationService $notificationService)
    {
        $notification = PaymentNotification::findOrFail($notificationId);

        if ($notification->status === PaymentNotification::STATUS_FAILED) {
            $notification->update(['status' => PaymentNotification::STATUS_PENDING]);
            session()->flash('success', 'La notification a été reprogrammée pour un nouvel envoi.');
        }
    }

    public function cancelNotification($notificationId)
    {
        $notification = PaymentNotification::findOrFail($notificationId);

        if ($notification->isPending()) {
            $notification->update(['status' => PaymentNotification::STATUS_CANCELLED]);
            session()->flash('success', 'La notification a été annulée.');
        }
    }

    public function render()
    {
        $query = PaymentNotification::with(['company', 'invoice'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('message', 'like', '%'.$this->search.'%')
                    ->orWhereHas('company', function ($companyQuery) {
                        $companyQuery->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $notifications = $query->paginate($this->perPage);

        return view('livewire.global-admin.payment-notifications', [
            'notifications' => $notifications,
            'totalPending' => PaymentNotification::where('status', PaymentNotification::STATUS_PENDING)->count(),
            'totalSent' => PaymentNotification::where('status', PaymentNotification::STATUS_SENT)->count(),
            'totalFailed' => PaymentNotification::where('status', PaymentNotification::STATUS_FAILED)->count(),
        ]);
    }
}
