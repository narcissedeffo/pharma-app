<?php

namespace App\Http\Controllers\Fournisseur;

use App\Http\Controllers\Controller;
use App\Models\CommandeFournisseur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommandeController extends Controller
{
    /**
     * Liste des commandes reçues.
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');

        $query = CommandeFournisseur::where('fournisseur_id', $request->user()->id)
            ->where('status', '!=', 'brouillon')
            ->with('pharmacien')
            ->latest('sent_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $commandes = $query->paginate(15)->withQueryString();

        $counts = CommandeFournisseur::where('fournisseur_id', $request->user()->id)
            ->where('status', '!=', 'brouillon')
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('fournisseur.commandes.index', compact('commandes', 'status', 'counts'));
    }

    /**
     * Détail d'une commande reçue (pour traiter et chatter).
     */
    public function show(Request $request, CommandeFournisseur $commande): View
    {
        abort_unless($commande->fournisseur_id === $request->user()->id, 403);
        abort_if($commande->status === 'brouillon', 404);

        $commande->load('pharmacien', 'items', 'facture');

        return view('fournisseur.commandes.show', compact('commande'));
    }

    /**
     * Mise à jour du statut de la commande par le fournisseur.
     */
    public function updateStatus(Request $request, CommandeFournisseur $commande): RedirectResponse
    {
        abort_unless($commande->fournisseur_id === $request->user()->id, 403);
        abort_if($commande->status === 'brouillon', 400);

        $validated = $request->validate([
            'status' => ['required', 'in:en_preparation,expediee,livree'],
        ]);

        // Simple state machine logic
        $allowedTransitions = [
            'envoyee' => ['en_preparation'],
            'en_preparation' => ['expediee'],
            'expediee' => ['livree'],
        ];

        if (!in_array($validated['status'], $allowedTransitions[$commande->status] ?? [])) {
            return back()->withErrors(['status' => 'Transition de statut invalide.']);
        }

        $commande->update(['status' => $validated['status']]);

        // Optionnel : Notifier le pharmacien du changement de statut

        return back()->with('status', 'Le statut de la commande a été mis à jour.');
    }
}
