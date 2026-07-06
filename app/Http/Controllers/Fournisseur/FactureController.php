<?php

namespace App\Http\Controllers\Fournisseur;

use App\Http\Controllers\Controller;
use App\Models\CommandeFournisseur;
use App\Models\Facture;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Facture::whereHas('commande', function ($q) use ($request) {
            $q->where('fournisseur_id', $request->user()->id);
        })->with('commande.pharmacien')->latest('date_emission');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $factures = $query->paginate(15)->withQueryString();

        $counts = Facture::whereHas('commande', function ($q) use ($request) {
            $q->where('fournisseur_id', $request->user()->id);
        })
        ->selectRaw('status, count(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');

        return view('fournisseur.factures.index', compact('factures', 'status', 'counts'));
    }

    public function store(Request $request, CommandeFournisseur $commande)
    {
        abort_unless($commande->fournisseur_id === $request->user()->id, 403);
        
        // Facture possible uniquement si expédiée ou livrée
        abort_unless(in_array($commande->status, ['expediee', 'livree']), 400);

        if ($commande->facture) {
            return back()->with('error', 'Une facture existe déjà pour cette commande.');
        }

        $request->validate([
            'delai_paiement' => ['required', 'integer', 'min:0', 'max:90'],
        ]);

        $annee = now()->format('Y');
        $mois = now()->format('m');
        $random = strtoupper(Str::random(4));
        
        $reference = "FAC-{$annee}{$mois}-{$random}";
        $bl_reference = "BL-{$annee}{$mois}-{$random}";

        $facture = Facture::create([
            'commande_id' => $commande->id,
            'reference' => $reference,
            'bl_reference' => $bl_reference,
            'montant_total' => $commande->total(),
            'date_emission' => now(),
            'date_echeance' => now()->addDays($request->input('delai_paiement')),
            'status' => 'en_attente',
        ]);

        return back()->with('status', 'Facture et Bon de Livraison générés avec succès.');
    }

    public function markAsPaid(Request $request, Facture $facture)
    {
        $commande = $facture->commande;
        abort_unless($commande->fournisseur_id === $request->user()->id, 403);

        $facture->update(['status' => 'payee']);

        return back()->with('status', 'La facture a été marquée comme payée.');
    }
}
