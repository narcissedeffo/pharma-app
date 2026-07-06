<?php

namespace App\Notifications;

use App\Models\Ordonnance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PickupReminder extends Notification
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
        $slot = $this->ordonnance->pickupSlot;
        $dt   = $slot?->proposed_at?->locale('fr')->isoFormat('HH[h]mm') ?? '?';

        return [
            'message' => "⏰ Rappel : votre retrait de médicaments est prévu aujourd'hui à {$dt}.",
            'url'     => route('client.ordonnances.show', $this->ordonnance),
            'color'   => 'blue',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $slot = $this->ordonnance->pickupSlot;
        $dt   = $slot?->proposed_at?->locale('fr')->isoFormat('HH[h]mm') ?? '?';

        return (new MailMessage)
            ->subject("⏰ Rappel retrait médicaments — PharmaApp")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("C'est votre rappel : votre retrait de médicaments est prévu aujourd'hui à **{$dt}**.")
            ->line("N'oubliez pas de vous présenter à la pharmacie à l'heure convenue.")
            ->action('Voir l\'ordonnance', route('client.ordonnances.show', $this->ordonnance))
            ->salutation("L'équipe PharmaApp");
    }
}
