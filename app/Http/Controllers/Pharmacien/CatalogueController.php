<?php

namespace App\Http\Controllers\Pharmacien;

use App\Http\Controllers\Controller;
use App\Models\User;

class CatalogueController extends Controller
{
    /**
     * Affiche la liste de tous les fournisseurs inscrits.
     */
    public function index()
    {
        $fournisseurs = User::whereHas('role', function ($q) {
                $q->where('slug', 'fournisseur');
            })
            ->where('status', 'active')
            ->withCount('produits')
            ->orderBy('name')
            ->paginate(12);

        return view('pharmacien.catalogue.index', compact('fournisseurs'));
    }

    /**
     * Affiche le catalogue de produits d'un fournisseur spécifique.
     */
    public function show(User $fournisseur)
    {
        // Vérifie que l'utilisateur est bien un fournisseur
        if (! $fournisseur->hasRole('fournisseur')) {
            abort(404);
        }

        $produits = $fournisseur->produits()
            ->orderByRaw('is_available DESC')
            ->orderBy('name')
            ->paginate(20);

        return view('pharmacien.catalogue.show', compact('fournisseur', 'produits'));
    }
}
