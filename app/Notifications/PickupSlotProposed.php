<?php

namespace App\Notifications;

use App\Models\Ordonnance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PickupSlotProposed extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Ordonnance $ordonnance,
        private readonly string     $proposedAt
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $dt = \Carbon\Carbon::parse($this->proposedAt)->locale('fr')->isoFormat('dddd D MMMM [à] HH[h]mm');

        return [
            'message' => "📅 Créneau de retrait proposé : {$dt} pour votre ordonnance.",
            'url'     => route('client.ordonnances.show', $this->ordonnance),
            'color'   => 'teal',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $dt = \Carbon\Carbon::parse($this->proposedAt)->locale('fr')->isoFormat('dddd D MMMM [à] HH[h]mm');

        return (new MailMessage)
            ->subject("📅 Créneau de retrait disponible — PharmaApp")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre pharmacien vous propose un créneau de retrait pour votre ordonnance.")
            ->line("**Date et heure proposées :** {$dt}")
            ->line("Connectez-vous pour confirmer ce créneau avant qu'il ne soit annulé.")
            ->action('Confirmer le créneau', route('client.ordonnances.show', $this->ordonnance))
            ->salutation("L'équipe PharmaApp");
    }
}
