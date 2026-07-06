<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Fournisseurs en DB ===" . PHP_EOL;
$all = \App\Models\User::whereHas('role', function($q) {
    $q->where('slug', 'fournisseur');
})->get();

if ($all->isEmpty()) {
    echo "AUCUN fournisseur trouvé en base !" . PHP_EOL;
} else {
    foreach ($all as $u) {
        echo "  - [{$u->id}] {$u->name} | status: {$u->status}" . PHP_EOL;
    }
}

$active = $all->where('status', 'active')->count();
echo PHP_EOL . "Total fournisseurs: " . $all->count() . PHP_EOL;
echo "Fournisseurs actifs: " . $active . PHP_EOL;

echo PHP_EOL . "=== Roles en DB ===" . PHP_EOL;
$roles = \App\Models\Role::all();
foreach ($roles as $r) {
    echo "  - [{$r->id}] {$r->name} | slug: {$r->slug}" . PHP_EOL;
}

echo PHP_EOL . "=== Pharmaciens en DB ===" . PHP_EOL;
$pharm = \App\Models\User::whereHas('role', function($q) {
    $q->where('slug', 'pharmacien');
})->get();
foreach ($pharm as $u) {
    echo "  - [{$u->id}] {$u->name} | status: {$u->status}" . PHP_EOL;
}

echo PHP_EOL . "=== Dernière erreur Laravel (storage/logs) ===" . PHP_EOL;
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -30);
    foreach ($lastLines as $line) {
        echo $line;
    }
} else {
    echo "Aucun fichier de log trouvé." . PHP_EOL;
}
