<?php

namespace App\Http\Controllers\Pharmacien;

use App\Http\Controllers\Controller;
use App\Models\CommandeFournisseur;
use App\Models\CommandeItem;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommandeController extends Controller
{
    /**
     * Liste des commandes du pharmacien.
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');

        $query = CommandeFournisseur::where('pharmacien_id', $request->user()->id)
            ->with('fournisseur')
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $commandes = $query->paginate(10)->withQueryString();

        $counts = CommandeFournisseur::where('pharmacien_id', $request->user()->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('pharmacien.commandes.index', compact('commandes', 'status', 'counts'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle commande.
     */
    public function create(): View
    {
        $fournisseurs = User::whereHas('role', fn($q) => $q->where('slug', 'fournisseur'))
            ->where('status', 'active')
            ->get();

        return view('pharmacien.commandes.create', compact('fournisseurs'));
    }

    /**
     * Crée une nouvelle commande (brouillon) et redirige vers l'édition.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fournisseur_id' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $commande = CommandeFournisseur::firstOrCreate(
            [
                'pharmacien_id' => $request->user()->id,
                'fournisseur_id' => $validated['fournisseur_id'],
                'status' => 'brouillon',
            ]
        );

        if (!empty($validated['notes'])) {
            $commande->update(['notes' => $validated['notes']]);
        }

        return redirect()->route('pharmacien.commandes.show', $commande)
            ->with('status', 'Brouillon de commande créé. Vous pouvez maintenant y ajouter des produits.');
    }

    /**
     * Ajoute un produit au panier (commande brouillon).
     */
    public function addToCart(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'produit_id' => ['required', 'exists:produits,id'],
            'quantite' => ['required', 'integer', 'min:1'],
        ]);

        $produit = Produit::findOrFail($validated['produit_id']);
        $fournisseur_id = $produit->fournisseur_id;

        // Trouver ou créer le brouillon pour ce fournisseur
        $commande = CommandeFournisseur::firstOrCreate(
            [
                'pharmacien_id' => $request->user()->id,
                'fournisseur_id' => $fournisseur_id,
                'status' => 'brouillon',
            ]
        );

        // Vérifier si le produit est déjà dans la commande
        $item = $commande->items()->where('produit_id', $produit->id)->first();

        if ($item) {
            $item->quantite += $validated['quantite'];
            $item->prix_unitaire = $produit->price; // met à jour le prix au cas où
            $item->save();
        } else {
            $commande->items()->create([
                'produit_id' => $produit->id,
                'nom_medicament' => $produit->name,
                'quantite' => $validated['quantite'],
                'prix_unitaire' => $produit->price,
            ]);
        }

        return back()->with('status', $produit->name . ' a été ajouté au panier.');
    }

    /**
     * Supprime un article du panier.
     */
    public function removeFromCart(Request $request, CommandeItem $item): RedirectResponse
    {
        $commande = $item->commande;
        
        abort_unless($commande->pharmacien_id === $request->user()->id, 403);
        abort_unless($commande->status === 'brouillon', 400);

        $item->delete();

        return back()->with('status', 'Article supprimé du panier.');
    }

    /**
     * Détail d'une commande (ajout d'items, chat, etc.).
     */
    public function show(Request $request, CommandeFournisseur $commande): View
    {
        abort_unless($commande->pharmacien_id === $request->user()->id, 403);

        $commande->load('fournisseur', 'items', 'facture');

        return view('pharmacien.commandes.show', compact('commande'));
    }

    /**
     * Valide la commande, enregistre les items et l'envoie au fournisseur.
     */
    public function send(Request $request, CommandeFournisseur $commande): RedirectResponse
    {
        abort_unless($commande->pharmacien_id === $request->user()->id, 403);
        abort_unless($commande->status === 'brouillon', 400);

        // Vérifier que le panier n'est pas vide
        if ($commande->items()->count() === 0) {
            return back()->withErrors(['error' => 'Votre panier est vide.']);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $commande->update([
            'status' => 'envoyee',
            'sent_at' => now(),
            'notes' => $validated['notes'] ?? $commande->notes,
        ]);

        return redirect()->route('pharmacien.commandes.index')
            ->with('status', 'Commande envoyée au fournisseur avec succès.');
    }
}
