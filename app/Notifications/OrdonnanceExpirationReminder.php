<?php

namespace App\Notifications;

use App\Models\Ordonnance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrdonnanceExpirationReminder extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Ordonnance $ordonnance
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "⚠️ Votre ordonnance \"{$this->ordonnance->original_filename}\" expire dans {$this->ordonnance->daysUntilExpiry()} jour(s).",
            'url'     => route('client.ordonnances.show', $this->ordonnance),
            'color'   => 'orange',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $days = $this->ordonnance->daysUntilExpiry();

        return (new MailMessage)
            ->subject("⚠️ Votre ordonnance expire bientôt — PharmaApp")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre ordonnance **\"{$this->ordonnance->original_filename}\"** expire dans **{$days} jour(s)**.")
            ->line("Les ordonnances médicales sont valables 3 mois en France. Passé ce délai, vous ne pourrez plus soumettre ce document.")
            ->action('Voir mon ordonnance', route('client.ordonnances.show', $this->ordonnance))
            ->line("Si vous avez déjà retiré vos médicaments, vous pouvez ignorer ce message.")
            ->salutation("L'équipe PharmaApp");
    }
}
