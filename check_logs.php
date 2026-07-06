<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$logFile = storage_path('logs/laravel.log');
if (!file_exists($logFile)) {
    echo "Pas de fichier de log." . PHP_EOL;
    exit;
}

$content = file_get_contents($logFile);
// Trouver la dernière occurrence de "[production.ERROR]" ou "[local.ERROR]"
$parts = preg_split('/\[\d{4}-\d{2}-\d{2}.*?\] (?:local|production)\.ERROR:/i', $content);
if (count($parts) > 1) {
    $lastError = end($parts);
    // Prendre les 50 premières lignes
    $lines = explode("\n", $lastError);
    $head = array_slice($lines, 0, 15);
    echo "=== DERNIÈRE ERREUR ===" . PHP_EOL;
    echo implode("\n", $head) . PHP_EOL;
} else {
    echo "Pas d'erreur ERROR trouvée dans les logs." . PHP_EOL;
    // Afficher les 20 dernières lignes
    $lines = file($logFile);
    $lastLines = array_slice($lines, -20);
    echo implode('', $lastLines);
}
