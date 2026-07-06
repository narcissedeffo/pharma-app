<?php

namespace App\Http\Controllers\Fournisseur;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProduitController extends Controller
{
    /**
     * Affiche la liste des produits du fournisseur connecté.
     */
    public function index()
    {
        $produits = Auth::user()
            ->produits()
            ->latest()
            ->paginate(15);

        return view('fournisseur.produits.index', compact('produits'));
    }

    /**
     * Affiche le formulaire de création d'un produit.
     */
    public function create()
    {
        return view('fournisseur.produits.create');
    }

    /**
     * Enregistre un nouveau produit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cip'             => 'nullable|string|max:50',
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string|max:2000',
            'price'           => 'nullable|numeric|min:0|max:99999.99',
            'date_peremption' => 'nullable|date',
            'is_available'    => 'boolean',
        ]);

        $validated['fournisseur_id'] = Auth::id();
        $validated['is_available']   = $request->boolean('is_available', true);

        Produit::create($validated);

        return redirect()
            ->route('fournisseur.produits.index')
            ->with('status', 'Produit ajouté au catalogue avec succès.');
    }

    /**
     * Affiche le formulaire de modification d'un produit.
     */
    public function edit(Produit $produit)
    {
        $this->authorizeProduct($produit);

        return view('fournisseur.produits.edit', compact('produit'));
    }

    /**
     * Met à jour un produit existant.
     */
    public function update(Request $request, Produit $produit)
    {
        $this->authorizeProduct($produit);

        $validated = $request->validate([
            'cip'             => 'nullable|string|max:50',
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string|max:2000',
            'price'           => 'nullable|numeric|min:0|max:99999.99',
            'date_peremption' => 'nullable|date',
            'is_available'    => 'boolean',
        ]);

        $validated['is_available'] = $request->boolean('is_available');

        $produit->update($validated);

        return redirect()
            ->route('fournisseur.produits.index')
            ->with('status', 'Produit mis à jour avec succès.');
    }

    /**
     * Supprime un produit.
     */
    public function destroy(Produit $produit)
    {
        $this->authorizeProduct($produit);

        $produit->delete();

        return redirect()
            ->route('fournisseur.produits.index')
            ->with('status', 'Produit supprimé du catalogue.');
    }

    /**
     * Vérifie que le produit appartient bien au fournisseur connecté.
     */
    private function authorizeProduct(Produit $produit): void
    {
        if ($produit->fournisseur_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }
    }

    /**
     * Supprime les produits sélectionnés.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:produits,id'],
        ]);

        Produit::whereIn('id', $validated['ids'])
            ->where('fournisseur_id', Auth::id())
            ->delete();

        return redirect()
            ->route('fournisseur.produits.index')
            ->with('status', count($validated['ids']) . ' produit(s) supprimé(s) du catalogue.');
    }
}
