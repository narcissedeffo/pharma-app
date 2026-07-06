<?php

use App\Http\Controllers\Admin\OrdonnanceController as AdminOrdonnanceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ActivateAccountController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Client\OrdonnanceController as ClientOrdonnanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Pharmacien\OrdonnanceController as PharmacienOrdonnanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authentification (rate-limited)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('activate/{token}', [ActivateAccountController::class, 'show'])->name('activate.show');
    Route::post('activate/{token}', [ActivateAccountController::class, 'store'])->name('activate.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |----------------------------------------------------------------
    | Espace Admin
    |----------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');

        // Vue globale des ordonnances
        Route::get('ordonnances', [AdminOrdonnanceController::class, 'index'])->name('ordonnances.index');
        Route::get('stats/json', [AdminOrdonnanceController::class, 'statsJson'])->name('stats.json');
    });

    /*
    |----------------------------------------------------------------
    | Espace Pharmacien
    |----------------------------------------------------------------
    */
    Route::middleware('role:pharmacien')->prefix('pharmacien')->name('pharmacien.')->group(function () {
        Route::get('ordonnances', [PharmacienOrdonnanceController::class, 'index'])->name('ordonnances.index');
        Route::get('ordonnances/{ordonnance}', [PharmacienOrdonnanceController::class, 'show'])->name('ordonnances.show');
        Route::post('ordonnances/{ordonnance}/prendre', [PharmacienOrdonnanceController::class, 'takeInCharge'])->name('ordonnances.take');
        Route::post('ordonnances/{ordonnance}/decision', [PharmacienOrdonnanceController::class, 'decide'])->name('ordonnances.decide');
        Route::post('ordonnances/{ordonnance}/creneau', [PharmacienOrdonnanceController::class, 'proposePickup'])->name('ordonnances.pickup.propose');
        Route::post('ordonnances/{ordonnance}/retrait', [PharmacienOrdonnanceController::class, 'markPickedUp'])->name('ordonnances.picked_up');
        
        // Commandes vers fournisseurs
        Route::get('commandes', [\App\Http\Controllers\Pharmacien\CommandeController::class, 'index'])->name('commandes.index');
        Route::get('commandes/creer', [\App\Http\Controllers\Pharmacien\CommandeController::class, 'create'])->name('commandes.create');
        Route::post('commandes', [\App\Http\Controllers\Pharmacien\CommandeController::class, 'store'])->name('commandes.store');
        Route::get('commandes/{commande}', [\App\Http\Controllers\Pharmacien\CommandeController::class, 'show'])->name('commandes.show');
        Route::post('commandes/{commande}/send', [\App\Http\Controllers\Pharmacien\CommandeController::class, 'send'])->name('commandes.send');

        // Export commande
        Route::get('commandes/{commande}/export/pdf', [\App\Http\Controllers\Pharmacien\ExportCommandeController::class, 'pdf'])->name('commandes.export.pdf');
        Route::get('commandes/{commande}/export/csv', [\App\Http\Controllers\Pharmacien\ExportCommandeController::class, 'csv'])->name('commandes.export.csv');

        // Panier (utilise les commandes en brouillon)
        Route::post('panier/ajouter', [\App\Http\Controllers\Pharmacien\CommandeController::class, 'addToCart'])->name('panier.add');
        Route::post('panier/{item}/supprimer', [\App\Http\Controllers\Pharmacien\CommandeController::class, 'removeFromCart'])->name('panier.remove');

        // Catalogue fournisseurs
        Route::get('catalogue', [\App\Http\Controllers\Pharmacien\CatalogueController::class, 'index'])->name('catalogue.index');
        Route::get('catalogue/{fournisseur}', [\App\Http\Controllers\Pharmacien\CatalogueController::class, 'show'])->name('catalogue.show');

        // Factures (Règlements)
        Route::get('factures', [\App\Http\Controllers\Pharmacien\FactureController::class, 'index'])->name('factures.index');
    });

    /*
    |----------------------------------------------------------------
    | Espace Fournisseur
    |----------------------------------------------------------------
    */
    Route::middleware('role:fournisseur')->prefix('fournisseur')->name('fournisseur.')->group(function () {
        Route::get('commandes', [\App\Http\Controllers\Fournisseur\CommandeController::class, 'index'])->name('commandes.index');
        Route::get('commandes/{commande}', [\App\Http\Controllers\Fournisseur\CommandeController::class, 'show'])->name('commandes.show');
        Route::post('commandes/{commande}/status', [\App\Http\Controllers\Fournisseur\CommandeController::class, 'updateStatus'])->name('commandes.status');

        // Catalogue produits
        Route::get('produits', [\App\Http\Controllers\Fournisseur\ProduitController::class, 'index'])->name('produits.index');
        Route::post('produits/bulk-destroy', [\App\Http\Controllers\Fournisseur\ProduitController::class, 'bulkDestroy'])->name('produits.bulk-destroy');
        Route::get('produits/creer', [\App\Http\Controllers\Fournisseur\ProduitController::class, 'create'])->name('produits.create');
        Route::post('produits', [\App\Http\Controllers\Fournisseur\ProduitController::class, 'store'])->name('produits.store');
        Route::get('produits/{produit}/modifier', [\App\Http\Controllers\Fournisseur\ProduitController::class, 'edit'])->name('produits.edit');
        Route::put('produits/{produit}', [\App\Http\Controllers\Fournisseur\ProduitController::class, 'update'])->name('produits.update');
        Route::delete('produits/{produit}', [\App\Http\Controllers\Fournisseur\ProduitController::class, 'destroy'])->name('produits.destroy');

        // Import catalogue
        Route::get('produits/importer', [\App\Http\Controllers\Fournisseur\ImportProduitController::class, 'create'])->name('produits.import.create');
        Route::post('produits/importer', [\App\Http\Controllers\Fournisseur\ImportProduitController::class, 'store'])->name('produits.import.store');

        // Factures (Règlements)
        Route::get('factures', [\App\Http\Controllers\Fournisseur\FactureController::class, 'index'])->name('factures.index');
        Route::post('commandes/{commande}/factures', [\App\Http\Controllers\Fournisseur\FactureController::class, 'store'])->name('factures.store');
        Route::post('factures/{facture}/payee', [\App\Http\Controllers\Fournisseur\FactureController::class, 'markAsPaid'])->name('factures.payee');
    });

    /*
    |----------------------------------------------------------------
    | Espace Client
    |----------------------------------------------------------------
    */
    Route::middleware('role:client')->prefix('client')->name('client.')->group(function () {
        Route::get('ordonnances', [ClientOrdonnanceController::class, 'index'])->name('ordonnances.index');
        Route::get('ordonnances/creer', [ClientOrdonnanceController::class, 'create'])->name('ordonnances.create');
        Route::post('ordonnances', [ClientOrdonnanceController::class, 'store'])->name('ordonnances.store');
        Route::get('ordonnances/{ordonnance}', [ClientOrdonnanceController::class, 'show'])->name('ordonnances.show');
        Route::post('ordonnances/{ordonnance}/publier', [ClientOrdonnanceController::class, 'publish'])->name('ordonnances.publish');
        Route::post('ordonnances/{ordonnance}/reaffecter', [ClientOrdonnanceController::class, 'reaffecter'])->name('ordonnances.reaffecter');
        Route::post('ordonnances/{ordonnance}/noter', [ClientOrdonnanceController::class, 'rate'])->name('ordonnances.rate');
        Route::post('ordonnances/{ordonnance}/confirmer-retrait', [ClientOrdonnanceController::class, 'confirmPickup'])->name('ordonnances.confirm_pickup');
    });

    /*
    |----------------------------------------------------------------
    | API interne (Géolocalisation)
    |----------------------------------------------------------------
    */
    Route::get('api/pharmacies', function () {
        return \App\Models\User::whereHas('role', function ($q) {
            $q->where('slug', 'pharmacien');
        })
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get(['id', 'name', 'address', 'latitude', 'longitude']);
    })->name('api.pharmacies');

    // Téléchargement & aperçu inline — accessible au client, pharmacien assigné, ou admin
    Route::get('ordonnances/{ordonnance}/telecharger', [ClientOrdonnanceController::class, 'download'])
        ->name('client.ordonnances.download');
    Route::get('ordonnances/{ordonnance}/apercu', [ClientOrdonnanceController::class, 'preview'])
        ->name('client.ordonnances.preview');

    /*
    |----------------------------------------------------------------
    | Notifications
    |----------------------------------------------------------------
    */
    Route::post('notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAllAsRead');
    Route::get('notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'readAndRedirect'])
        ->name('notifications.readAndRedirect');

    /*
    |----------------------------------------------------------------
    | Messagerie (Chat)
    |----------------------------------------------------------------
    */
    Route::get('ordonnances/{ordonnance}/messages', [\App\Http\Controllers\MessageController::class, 'index'])
        ->name('messages.index');
    Route::post('ordonnances/{ordonnance}/messages', [\App\Http\Controllers\MessageController::class, 'store'])
        ->name('messages.store');

    Route::get('commandes/{commande}/messages', [\App\Http\Controllers\CommandeMessageController::class, 'index'])
        ->name('commande_messages.index');
    Route::post('commandes/{commande}/messages', [\App\Http\Controllers\CommandeMessageController::class, 'store'])
        ->name('commande_messages.store');

    /*
    |----------------------------------------------------------------
    | Téléchargements PDF (Bons de livraison & Factures)
    |----------------------------------------------------------------
    */
    Route::get('factures/{facture}/pdf', [\App\Http\Controllers\PdfController::class, 'downloadFacture'])
        ->name('pdf.facture');
    Route::get('factures/{facture}/bl', [\App\Http\Controllers\PdfController::class, 'downloadBonLivraison'])
        ->name('pdf.bl');
});
