<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory, SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'invited_by',
        'email',
        'role_id',
        'store_id',
        'token',
        'expires_at',
        'accepted_at',
        'invited_user_id',
        'message',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            $invitation->token = Str::random(64);
            $invitation->expires_at = now()->addDays(7); // 7 jours d'expiration par défaut
            $invitation->status = 'pending';
        });
    }

    // --- Relations ---

    /**
     * L'utilisateur qui a envoyé l'invitation
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * L'utilisateur qui a accepté l'invitation
     */
    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    /**
     * Le rôle assigné dans l'invitation
     */
    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

    /**
     * Le magasin assigné dans l'invitation
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * L'entreprise propriétaire de l'invitation
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // --- Scopes ---

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                    ->where('status', 'pending');
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'pending')
                    ->where('expires_at', '>', now());
    }

    // --- Methods ---

    /**
     * Vérifie si l'invitation est encore valide
     */
    public function isValid(): bool
    {
        return $this->status === 'pending' && $this->expires_at > now();
    }

    /**
     * Vérifie si l'invitation a expiré
     */
    public function isExpired(): bool
    {
        return $this->expires_at < now() && $this->status === 'pending';
    }

    /**
     * Marque l'invitation comme acceptée
     */
    public function markAsAccepted(User $user): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'invited_user_id' => $user->id,
        ]);
    }

    /**
     * Marque l'invitation comme annulée
     */
    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Régénère le token d'invitation et prolonge l'expiration
     */
    public function regenerateToken(): void
    {
        $this->update([
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
            'status' => 'pending',
        ]);
    }

    /**
     * Génère l'URL d'invitation
     */
    public function getInvitationUrl(): string
    {
        return route('invitation.show', $this->token);
    }

    /**
     * Formate le temps restant avant expiration
     */
    public function getTimeUntilExpiration(): string
    {
        if ($this->isExpired()) {
            return 'Expirée';
        }

        return $this->expires_at->diffForHumans();
    }

    /**
     * Retourne la couleur du badge de statut
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => $this->isExpired() ? 'red' : 'yellow',
            'accepted' => 'green',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Retourne le libellé du statut
     */
    public function getStatusLabel(): string
    {
        if ($this->status === 'pending' && $this->isExpired()) {
            return 'Expirée';
        }

        return match ($this->status) {
            'pending' => 'En attente',
            'accepted' => 'Acceptée',
            'cancelled' => 'Annulée',
            default => 'Inconnu',
        };
    }
}