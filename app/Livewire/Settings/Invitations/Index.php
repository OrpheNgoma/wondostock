<?php

namespace App\Livewire\Settings\Invitations;

use App\Models\Invitation;
use App\Services\InvitationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.saas')]
#[Title('Gestion des Invitations - WondoStock')]
class Index extends Component
{
    use WithPagination;

    // --- UI State ---
    public bool $showInviteModal = false;
    public bool $showBulkInviteModal = false;
    public bool $showDetailsModal = false;
    public string $activeTab = 'pending';
    public string $search = '';
    public string $filterRole = '';
    public string $filterStore = '';

    // --- Single Invitation Form ---
    public string $email = '';
    public ?int $role_id = null;
    public ?int $store_id = null;
    public string $message = '';
    public bool $isSending = false;

    // --- Bulk Invitation Form ---
    public array $bulkInvitations = [];
    public bool $isSendingBulk = false;

    // --- Selected Invitation for Details ---
    public ?Invitation $selectedInvitation = null;

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'role_id' => 'nullable|exists:roles,id',
            'store_id' => 'nullable|exists:stores,id',
            'message' => 'nullable|string|max:1000',
        ];
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'pending'],
        'filterRole' => ['except' => ''],
        'filterStore' => ['except' => ''],
    ];

    public function mount()
    {
        $this->initializeBulkInvitations();
    }

    public function initializeBulkInvitations()
    {
        $this->bulkInvitations = [
            ['email' => '', 'role_id' => null, 'store_id' => null, 'message' => '']
        ];
    }

    // --- Single Invitation Actions ---

    public function openInviteModal()
    {
        $this->resetForm();
        $this->showInviteModal = true;
    }

    public function sendInvitation()
    {
        $this->isSending = true;
        
        try {
            $this->validate();
            
            $invitationService = app(InvitationService::class);
            $invitationService->createInvitation([
                'email' => $this->email,
                'role_id' => $this->role_id,
                'store_id' => $this->store_id,
                'message' => $this->message,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Invitation envoyée avec succès !'
            ]);

            $this->showInviteModal = false;
            $this->resetForm();

        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'envoi de l\'invitation.'
            ]);
        } finally {
            $this->isSending = false;
        }
    }

    // --- Bulk Invitation Actions ---

    public function openBulkInviteModal()
    {
        $this->initializeBulkInvitations();
        $this->showBulkInviteModal = true;
    }

    public function addBulkInvitation()
    {
        $this->bulkInvitations[] = [
            'email' => '',
            'role_id' => null,
            'store_id' => null,
            'message' => ''
        ];
    }

    public function removeBulkInvitation($index)
    {
        if (count($this->bulkInvitations) > 1) {
            unset($this->bulkInvitations[$index]);
            $this->bulkInvitations = array_values($this->bulkInvitations);
        }
    }

    public function sendBulkInvitations()
    {
        $this->isSendingBulk = true;
        
        try {
            // Filtrer les invitations vides
            $validInvitations = array_filter($this->bulkInvitations, function($invitation) {
                return !empty($invitation['email']);
            });

            if (empty($validInvitations)) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Veuillez remplir au moins une invitation.'
                ]);
                return;
            }

            $invitationService = app(InvitationService::class);
            $result = $invitationService->createBulkInvitations($validInvitations);

            $successCount = count($result['invitations']);
            $errorCount = count($result['errors']);

            if ($successCount > 0) {
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => "{$successCount} invitation(s) envoyée(s) avec succès !"
                ]);
            }

            if ($errorCount > 0) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => "{$errorCount} invitation(s) ont échoué."
                ]);
            }

            $this->showBulkInviteModal = false;
            $this->initializeBulkInvitations();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'envoi des invitations.'
            ]);
        } finally {
            $this->isSendingBulk = false;
        }
    }

    // --- Invitation Management Actions ---

    public function showInvitationDetails(Invitation $invitation)
    {
        $this->selectedInvitation = $invitation;
        $this->showDetailsModal = true;
    }

    public function resendInvitation(Invitation $invitation)
    {
        try {
            $invitationService = app(InvitationService::class);
            $invitationService->resendInvitation($invitation);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Invitation renvoyée avec succès !'
            ]);

        } catch (ValidationException $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function cancelInvitation(Invitation $invitation)
    {
        try {
            $invitationService = app(InvitationService::class);
            $invitationService->cancelInvitation($invitation);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Invitation annulée avec succès !'
            ]);

        } catch (ValidationException $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteInvitation(Invitation $invitation)
    {
        $invitation->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Invitation supprimée avec succès !'
        ]);
    }

    // --- UI Actions ---

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterRole()
    {
        $this->resetPage();
    }

    public function updatedFilterStore()
    {
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->email = '';
        $this->role_id = null;
        $this->store_id = null;
        $this->message = '';
        $this->resetErrorBag();
    }

    // --- Computed Properties ---

    public function getInvitationsProperty()
    {
        $query = Invitation::with(['inviter', 'role', 'store', 'invitedUser'])
            ->where('company_id', Auth::user()->company_id);

        // Filter by status/tab
        switch ($this->activeTab) {
            case 'pending':
                $query->where('status', 'pending')->where('expires_at', '>', now());
                break;
            case 'expired':
                $query->where('status', 'pending')->where('expires_at', '<=', now());
                break;
            case 'accepted':
                $query->where('status', 'accepted');
                break;
            case 'cancelled':
                $query->where('status', 'cancelled');
                break;
            case 'all':
                // No additional filter
                break;
        }

        // Search filter
        if ($this->search) {
            $query->where('email', 'like', '%' . $this->search . '%');
        }

        // Role filter
        if ($this->filterRole) {
            $query->where('role_id', $this->filterRole);
        }

        // Store filter
        if ($this->filterStore) {
            $query->where('store_id', $this->filterStore);
        }

        return $query->latest()->paginate(10);
    }

    public function getRolesProperty()
    {
        return Role::where('name', '!=', 'Global-Admin')->get();
    }

    public function getStoresProperty()
    {
        return Auth::user()->company->stores;
    }

    public function getStatsProperty()
    {
        $invitationService = app(InvitationService::class);
        return $invitationService->getInvitationStats(Auth::user()->company_id);
    }

    public function render()
    {
        return view('livewire.saas.settings.invitations.index', [
            'invitations' => $this->invitations,
            'roles' => $this->roles,
            'stores' => $this->stores,
            'stats' => $this->stats,
        ]);
    }
}