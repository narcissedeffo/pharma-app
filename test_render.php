<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::where('email', 'pharmacien@test.com')->first();
auth()->login($user);

$request = Illuminate\Http\Request::create('/pharmacien/commandes', 'GET');
$response = app()->handle($request);

$html = $response->getContent();

file_put_contents('rendered_output.html', $html);
echo "Taille du HTML: " . strlen($html) . " octets\n";

// Extraire la div principale
preg_match('/<main[^>]*>(.*?)<\/main>/is', $html, $matches);
if (isset($matches[1])) {
    echo "Contenu de <main> extrait et sauvegardé.\n";
    file_put_contents('rendered_main.html', $matches[1]);
} else {
    echo "Balise <main> introuvable.\n";
}
