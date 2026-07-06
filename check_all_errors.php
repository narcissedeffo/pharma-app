<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$logFile = storage_path('logs/laravel.log');
if (!file_exists($logFile)) {
    echo "Pas de fichier de log." . PHP_EOL;
    exit;
}

// Lire toutes les entrées d'erreurs
$content = file_get_contents($logFile);
$matches = [];
preg_match_all('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.ERROR: ([^\n{]+)/', $content, $matches, PREG_SET_ORDER);

echo "=== Toutes les erreurs Laravel ===" . PHP_EOL;
foreach ($matches as $m) {
    echo "[{$m[1]}] {$m[2]}" . PHP_EOL;
}

if (empty($matches)) {
    echo "Aucune erreur ERROR trouvée." . PHP_EOL;
}
