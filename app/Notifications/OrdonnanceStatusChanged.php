<?php

namespace App\Notifications;

use App\Models\Ordonnance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrdonnanceStatusChanged extends Notification
{
    use Queueable;

    public $ordonnance;
    public $message;
    public $statusColor;

    public function __construct(Ordonnance $ordonnance, string $message, string $statusColor = 'blue')
    {
        $this->ordonnance = $ordonnance;
        $this->message = $message;
        $this->statusColor = $statusColor;
    }

    public function via(object $notifiable): array
    {
        // On ajoute le mail en plus de la database
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ordonnance_id' => $this->ordonnance->id,
            'message' => $this->message,
            'status' => $this->ordonnance->status,
            'color' => $this->statusColor,
            'url' => route($notifiable->role->slug === 'client' ? 'client.ordonnances.show' : 'pharmacien.ordonnances.show', $this->ordonnance)
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route($notifiable->role->slug === 'client' ? 'client.ordonnances.show' : 'pharmacien.ordonnances.show', $this->ordonnance);

        return (new MailMessage)
            ->subject("Mise à jour de votre ordonnance — PharmaApp")
            ->greeting("Bonjour {$notifiable->name},")
            ->line($this->message)
            ->line("Le statut de l'ordonnance **\"{$this->ordonnance->original_filename}\"** a changé pour : **{$this->ordonnance->statusLabel()}**.")
            ->action('Voir les détails', $url)
            ->salutation("L'équipe PharmaApp");
    }
}
