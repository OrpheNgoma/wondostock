<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invitation $invitation
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $companyName = $this->invitation->company->name;
        $inviterName = $this->invitation->inviter->name;
        $roleName = $this->invitation->role?->name ?? 'Utilisateur';
        $storeName = $this->invitation->store?->name;

        return (new MailMessage)
            ->subject("Invitation à rejoindre {$companyName} sur WondoStock")
            ->greeting('Bonjour !')
            ->line("{$inviterName} vous invite à rejoindre l'équipe de **{$companyName}** sur WondoStock.")
            ->line("**Votre rôle :** {$roleName}")
            ->when($storeName, function ($mail) use ($storeName) {
                return $mail->line("**Magasin assigné :** {$storeName}");
            })
            ->when($this->invitation->message, function ($mail) {
                return $mail->line('**Message personnel :**')
                    ->line("_{$this->invitation->message}_");
            })
            ->line("Cette invitation expire le **{$this->invitation->expires_at->format('d/m/Y à H:i')}**.")
            ->action('Accepter l\'invitation', $this->invitation->getInvitationUrl())
            ->line("Si vous n'avez pas demandé cette invitation, vous pouvez ignorer cet email.")
            ->salutation("Merci de nous rejoindre !\nL'équipe WondoStock");
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'company_name' => $this->invitation->company->name,
            'inviter_name' => $this->invitation->inviter->name,
            'role_name' => $this->invitation->role?->name,
            'expires_at' => $this->invitation->expires_at,
        ];
    }
}
