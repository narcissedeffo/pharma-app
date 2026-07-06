<?php

namespace App\Http\Controllers\Pharmacien;

use App\Http\Controllers\Controller;
use App\Models\Ordonnance;
use App\Models\OrdonnanceItem;
use App\Models\PickupSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdonnanceController extends Controller
{
    /**
     * Liste des ordonnances avec filtres par statut et recherche.
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');
        $search = $request->get('q', '');

        $query = $request->user()
            ->ordonnancesPharmacien()
            ->with('client')
            ->whereIn('status', ['en_attente', 'en_cours', 'validee', 'refusee', 'retiree']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('original_filename', 'like', "%{$search}%");
        }

        $ordonnances = $query->latest('published_at')->paginate(10)->withQueryString();

        // Compteurs par statut pour les onglets
        $counts = $request->user()->ordonnancesPharmacien()
            ->whereIn('status', ['en_attente', 'en_cours', 'validee', 'refusee', 'retiree'])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('pharmacien.ordonnances.index', compact('ordonnances', 'status', 'search', 'counts'));
    }

    /**
     * Détail d'une ordonnance.
     */
    public function show(Request $request, Ordonnance $ordonnance): View
    {
        abort_unless($ordonnance->isAssignedTo($request->user()), 403);

        $ordonnance->load('histories.user', 'client', 'items', 'pickupSlot');

        return view('pharmacien.ordonnances.show', compact('ordonnance'));
    }

    /**
     * Le pharmacien prend en charge l'ordonnance (en_attente → en_cours).
     */
    public function takeInCharge(Request $request, Ordonnance $ordonnance): RedirectResponse
    {
        abort_unless($ordonnance->isAssignedTo($request->user()), 403);
        abort_unless($ordonnance->status === 'en_attente', 400);

        $ordonnance->moveTo('en_cours', $request->user(), 'Prise en charge par le pharmacien.');

        return back()->with('status', 'Ordonnance prise en charge.');
    }

    /**
     * Le pharmacien valide ou refuse l'ordonnance, avec liste de médicaments.
     */
    public function decide(Request $request, Ordonnance $ordonnance): RedirectResponse
    {
        abort_unless($ordonnance->isAssignedTo($request->user()), 403);
        abort_unless($ordonnance->status === 'en_cours', 400);

        $validated = $request->validate([
            'decision'              => ['required', 'in:validee,refusee'],
            'note_pharmacien'       => ['nullable', 'string', 'max:2000'],
            'items'                 => ['nullable', 'array'],
            'items.*.nom'           => ['required_with:items', 'string', 'max:255'],
            'items.*.statut'        => ['required_with:items', 'in:disponible,a_commander,indisponible'],
            'items.*.commentaire'   => ['nullable', 'string', 'max:255'],
        ]);

        $ordonnance->update(['note_pharmacien' => $validated['note_pharmacien'] ?? null]);

        // Enregistrer les items de médicaments
        if (!empty($validated['items'])) {
            $ordonnance->items()->delete(); // supprimer anciens
            foreach ($validated['items'] as $item) {
                OrdonnanceItem::create([
                    'ordonnance_id'       => $ordonnance->id,
                    'nom_medicament'      => $item['nom'],
                    'statut_disponibilite' => $item['statut'],
                    'commentaire'         => $item['commentaire'] ?? null,
                ]);
            }
        }

        $ordonnance->moveTo($validated['decision'], $request->user(), $validated['note_pharmacien'] ?? null);

        return redirect()->route('pharmacien.ordonnances.index')
            ->with('status', 'Décision enregistrée.');
    }

    /**
     * Le pharmacien propose un créneau de retrait après validation.
     */
    public function proposePickup(Request $request, Ordonnance $ordonnance): RedirectResponse
    {
        abort_unless($ordonnance->isAssignedTo($request->user()), 403);
        abort_unless($ordonnance->status === 'validee', 400);

        $validated = $request->validate([
            'proposed_at' => ['required', 'date', 'after:now'],
        ]);

        // Mettre à jour ou créer le créneau
        PickupSlot::updateOrCreate(
            ['ordonnance_id' => $ordonnance->id],
            ['proposed_at' => $validated['proposed_at'], 'confirmed_at' => null]
        );

        // Notifier le client
        $ordonnance->client->notify(
            new \App\Notifications\PickupSlotProposed($ordonnance, $validated['proposed_at'])
        );

        return back()->with('status', 'Créneau de retrait proposé au patient.');
    }

    /**
     * Marquer l'ordonnance comme retirée (après le passage du patient).
     */
    public function markPickedUp(Request $request, Ordonnance $ordonnance): RedirectResponse
    {
        abort_unless($ordonnance->isAssignedTo($request->user()), 403);
        abort_unless($ordonnance->status === 'validee', 400);

        $ordonnance->moveTo('retiree', $request->user(), 'Médicaments retirés en pharmacie.');

        return back()->with('status', 'Retrait confirmé. Le patient peut maintenant noter son expérience.');
    }
}
