<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Ordonnance;
use App\Models\PickupSlot;
use App\Notifications\OrdonnanceExpirationReminder;
use App\Notifications\PickupReminder;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Rappel d'expiration J-15
Schedule::call(function () {
    $ordonnances = Ordonnance::where('status', 'brouillon')
        ->whereNotNull('expires_at')
        ->whereDate('expires_at', now()->addDays(15)->toDateString())
        ->get();

    foreach ($ordonnances as $ordonnance) {
        if ($ordonnance->client) {
            $ordonnance->client->notify(new OrdonnanceExpirationReminder($ordonnance));
        }
    }
})->dailyAt('09:00')->name('check-expiring-ordonnances');

// Marquer comme expirées celles dont la date est dépassée
Schedule::call(function () {
    $ordonnances = Ordonnance::whereIn('status', ['brouillon', 'en_attente', 'en_cours', 'refusee'])
        ->whereNotNull('expires_at')
        ->whereDate('expires_at', '<', now()->toDateString())
        ->get();

    foreach ($ordonnances as $ordonnance) {
        $actor = $ordonnance->pharmacien ?? $ordonnance->client; // ou system
        if ($actor) {
            $ordonnance->moveTo('expiree', $actor, 'Expiration automatique de l\'ordonnance.');
        } else {
            $ordonnance->update(['status' => 'expiree']);
            $ordonnance->histories()->create([
                'user_id' => $ordonnance->client_id, // fallback
                'from_status' => $ordonnance->status,
                'to_status' => 'expiree',
                'comment' => 'Expiration automatique.'
            ]);
        }
    }
})->dailyAt('00:10')->name('mark-expired-ordonnances');

// Rappel de retrait 1 heure avant
Schedule::call(function () {
    $now = now();
    $slots = PickupSlot::whereNotNull('confirmed_at')
        ->whereNull('reminder_sent_at')
        ->whereBetween('proposed_at', [$now, $now->copy()->addMinutes(65)]) // Dans environ 1 heure
        ->get();

    foreach ($slots as $slot) {
        $client = $slot->ordonnance->client;
        if ($client) {
            $client->notify(new PickupReminder($slot->ordonnance));
            $slot->update(['reminder_sent_at' => now()]);
        }
    }
})->everyFifteenMinutes()->name('pickup-reminders');
