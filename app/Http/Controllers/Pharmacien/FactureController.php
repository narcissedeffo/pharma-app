<?php

namespace App\Http\Controllers\Pharmacien;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Facture::whereHas('commande', function ($q) use ($request) {
            $q->where('pharmacien_id', $request->user()->id);
        })->with('commande.fournisseur')->latest('date_emission');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $factures = $query->paginate(15)->withQueryString();

        $counts = Facture::whereHas('commande', function ($q) use ($request) {
            $q->where('pharmacien_id', $request->user()->id);
        })
        ->selectRaw('status, count(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');

        return view('pharmacien.factures.index', compact('factures', 'status', 'counts'));
    }
}
