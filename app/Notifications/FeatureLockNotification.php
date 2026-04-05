<?php

namespace App\Notifications;

use App\Models\FeatureLock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeatureLockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private FeatureLock $featureLock,
        private string $action // 'locked' ou 'unlocked'
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $isLocked = $this->action === 'locked';
        $feature = $this->featureLock->getFeatureDescription();
        $company = $this->featureLock->company->name;

        $mail = (new MailMessage)
            ->subject($isLocked ? 'Fonctionnalité Verrouillée' : 'Fonctionnalité Déverrouillée')
            ->greeting('Bonjour,')
            ->line($isLocked
                ? "Une fonctionnalité de votre entreprise {$company} a été verrouillée."
                : "Une fonctionnalité de votre entreprise {$company} a été déverrouillée."
            )
            ->line("**Fonctionnalité concernée:** {$feature}");

        if ($isLocked && $this->featureLock->reason) {
            $mail->line("**Raison:** {$this->featureLock->reason}");
        }

        if ($isLocked && $this->featureLock->expires_at) {
            $mail->line("**Expire le:** {$this->featureLock->expires_at->format('d/m/Y H:i')}");
        }

        $mail->line($isLocked
            ? 'Cette fonctionnalité n\'est plus accessible dans votre interface.'
            : 'Cette fonctionnalité est de nouveau accessible dans votre interface.'
        );

        if (! $isLocked) {
            $mail->action('Accéder à l\'interface', route('dashboard'));
        }

        $mail->line('Merci de votre compréhension.');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'action' => $this->action,
            'feature_key' => $this->featureLock->feature_key,
            'feature_description' => $this->featureLock->getFeatureDescription(),
            'company_name' => $this->featureLock->company->name,
            'reason' => $this->featureLock->reason,
            'expires_at' => $this->featureLock->expires_at?->toISOString(),
            'locked_by' => $this->featureLock->lockedBy?->name,
        ];
    }
}
