<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\CommandeFournisseur;
use Illuminate\Http\Request;

$user = User::where('email', 'pharmacien@test.com')->first();
auth()->login($user);

$fournisseur = User::where('email', 'fournisseur@test.com')->first();

$request = Request::create('/pharmacien/commandes', 'POST', [
    'fournisseur_id' => $fournisseur->id,
    'notes' => 'Test creation via script'
]);
$request->setUserResolver(function () use ($user) {
    return $user;
});
$request->setSession($app['session']->driver());

$response = app()->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Headers: \n" . print_r($response->headers->all(), true) . "\n";
if ($response->isRedirect()) {
    echo "Redirect URL: " . $response->headers->get('Location') . "\n";
} else {
    echo "Body: " . substr($response->getContent(), 0, 500) . "\n";
}
