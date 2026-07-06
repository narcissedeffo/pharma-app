<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test du flux de création de commande ===" . PHP_EOL;

// Charger un pharmacien actif
$pharmacien = \App\Models\User::whereHas('role', function($q) {
    $q->where('slug', 'pharmacien');
})->where('status', 'active')->first();

if (!$pharmacien) {
    echo "ERREUR: Pas de pharmacien actif en base." . PHP_EOL;
    exit(1);
}
echo "Pharmacien: {$pharmacien->name} [ID: {$pharmacien->id}]" . PHP_EOL;

// Charger un fournisseur actif
$fournisseur = \App\Models\User::whereHas('role', function($q) {
    $q->where('slug', 'fournisseur');
})->where('status', 'active')->first();

if (!$fournisseur) {
    echo "ERREUR: Pas de fournisseur actif en base." . PHP_EOL;
    exit(1);
}
echo "Fournisseur: {$fournisseur->name} [ID: {$fournisseur->id}]" . PHP_EOL;

// Tenter de créer une commande
try {
    $commande = \App\Models\CommandeFournisseur::create([
        'pharmacien_id' => $pharmacien->id,
        'fournisseur_id' => $fournisseur->id,
        'status' => 'brouillon',
        'notes' => 'Test de création',
    ]);
    echo PHP_EOL . "✅ CommandeFournisseur créée avec succès !" . PHP_EOL;
    echo "   ID: {$commande->id}" . PHP_EOL;
    echo "   Référence: {$commande->reference}" . PHP_EOL;
    echo "   Status: {$commande->status}" . PHP_EOL;

    // Nettoyage
    $commande->delete();
    echo "   (Supprimée après test)" . PHP_EOL;
} catch (\Exception $e) {
    echo PHP_EOL . "❌ ERREUR lors de la création: " . PHP_EOL;
    echo "   " . $e->getMessage() . PHP_EOL;
    echo "   Fichier: " . $e->getFile() . " ligne " . $e->getLine() . PHP_EOL;
}

// Vérification de la table
echo PHP_EOL . "=== Structure de la table commandes_fournisseurs ===" . PHP_EOL;
$columns = \Illuminate\Support\Facades\DB::select("PRAGMA table_info(commandes_fournisseurs)");
foreach ($columns as $col) {
    echo "  - {$col->name} ({$col->type})" . ($col->notnull ? ' NOT NULL' : '') . ($col->dflt_value ? " DEFAULT {$col->dflt_value}" : '') . PHP_EOL;
}
