<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ordonnance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdonnanceController extends Controller
{
    /**
     * Vue globale de toutes les ordonnances (admin).
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');
        $search = $request->get('q', '');

        $query = Ordonnance::with(['client', 'pharmacien'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('client', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('pharmacien', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhere('original_filename', 'like', "%{$search}%");
        }

        $ordonnances = $query->paginate(20)->withQueryString();

        $counts = Ordonnance::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.ordonnances.index', compact('ordonnances', 'status', 'search', 'counts'));
    }

    /**
     * Données JSON pour graphiques Chart.js du dashboard admin.
     */
    public function statsJson(Request $request)
    {
        // Volume mensuel sur les 6 derniers mois
        $monthlyVolume = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyVolume[] = [
                'label' => $month->translatedFormat('M Y'),
                'count' => Ordonnance::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'validated' => Ordonnance::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->where('status', 'validee')
                    ->count(),
            ];
        }

        // Perf des pharmacies (top 10)
        $pharmacies = User::whereHas('role', fn($q) => $q->where('slug', 'pharmacien'))
            ->withCount(['ordonnancesPharmacien as total_ordonnances'])
            ->withCount(['ordonnancesPharmacien as total_validees' => fn($q) => $q->where('status', 'validee')])
            ->withAvg('ordonnancesPharmacien as avg_rating', 'rating')
            ->orderByDesc('total_ordonnances')
            ->take(10)
            ->get();

        return response()->json([
            'monthly_volume' => $monthlyVolume,
            'pharmacies'     => $pharmacies,
        ]);
    }
}
