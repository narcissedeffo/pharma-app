<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountInvitation extends Notification
{
    use Queueable;

    public function __construct(protected string $activationUrl, protected string $roleName)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Activation de votre compte - Pharma App')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line("Un compte {$this->roleName} a été créé pour vous sur Pharma App.")
            ->line('Cliquez sur le bouton ci-dessous pour définir votre mot de passe et activer votre compte.')
            ->action('Activer mon compte', $this->activationUrl)
            ->line('Ce lien expire dans 48 heures.')
            ->line('Si vous n\'êtes pas à l\'origine de cette demande, ignorez cet email.');
    }
}
