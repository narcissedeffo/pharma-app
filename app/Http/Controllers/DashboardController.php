<?php

namespace App\Http\Controllers;

use App\Models\Ordonnance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return match ($user->role?->slug) {
            'admin'      => $this->adminDashboard(),
            'pharmacien' => $this->pharmacienDashboard($user),
            'fournisseur' => $this->fournisseurDashboard($user),
            default      => $this->clientDashboard($user),
        };
    }

    private function clientDashboard(User $user): View
    {
        $total      = $user->ordonnancesClient()->count();
        $enAttente  = $user->ordonnancesClient()->where('status', 'en_attente')->count();
        $validees   = $user->ordonnancesClient()->where('status', 'validee')->count();
        $expiringSoon = $user->ordonnancesClient()
            ->where('status', 'brouillon')
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', now()->addDays(15))
            ->whereDate('expires_at', '>=', now())
            ->count();

        // Prochains créneaux confirmés
        $nextPickup = $user->ordonnancesClient()
            ->with('pickupSlot')
            ->whereHas('pickupSlot', fn($q) => $q->whereNotNull('confirmed_at')->where('proposed_at', '>=', now()))
            ->first();

        return view('dashboard.client', compact('total', 'enAttente', 'validees', 'expiringSoon', 'nextPickup'));
    }

    private function fournisseurDashboard(User $user): View
    {
        $nouvelles = \App\Models\CommandeFournisseur::where('fournisseur_id', $user->id)
            ->where('status', 'envoyee')
            ->count();
            
        $enCours = \App\Models\CommandeFournisseur::where('fournisseur_id', $user->id)
            ->whereIn('status', ['en_preparation', 'expediee'])
            ->count();
            
        $livrees = \App\Models\CommandeFournisseur::where('fournisseur_id', $user->id)
            ->where('status', 'livree')
            ->count();

        // Activité mensuelle (Commandes reçues)
        $monthlyActivity = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyActivity[] = [
                'label' => $month->translatedFormat('M'),
                'count' => \App\Models\CommandeFournisseur::where('fournisseur_id', $user->id)
                    ->whereYear('sent_at', $month->year)
                    ->whereMonth('sent_at', $month->month)
                    ->count(),
            ];
        }

        return view('dashboard.fournisseur', compact('nouvelles', 'enCours', 'livrees', 'monthlyActivity'));
    }

    private function pharmacienDashboard(User $user): View
    {
        $nouvelles  = $user->ordonnancesPharmacien()->where('status', 'en_attente')->count();
        $enCours    = $user->ordonnancesPharmacien()->where('status', 'en_cours')->count();
        $traitees   = $user->ordonnancesPharmacien()->whereIn('status', ['validee', 'refusee', 'retiree'])->count();
        $avgRating  = $user->ordonnancesPharmacien()->whereNotNull('rating')->avg('rating');

        // Activité des 7 derniers jours
        $weeklyActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $weeklyActivity[] = [
                'label' => $day->translatedFormat('D'),
                'count' => $user->ordonnancesPharmacien()
                    ->whereDate('created_at', $day->toDateString())
                    ->count(),
            ];
        }

        return view('dashboard.pharmacien', compact('nouvelles', 'enCours', 'traitees', 'avgRating', 'weeklyActivity'));
    }

    private function adminDashboard(): View
    {
        $nbUsers       = User::count();
        $nbPharmaciens = User::whereHas('role', fn($q) => $q->where('slug', 'pharmacien'))->count();
        $nbClients     = User::whereHas('role', fn($q) => $q->where('slug', 'client'))->count();
        $nbOrdonnances = Ordonnance::count();
        $nbEnAttente   = Ordonnance::where('status', 'en_attente')->count();

        // Taux de validation global
        $totalDecided  = Ordonnance::whereIn('status', ['validee', 'refusee'])->count();
        $totalValidees = Ordonnance::where('status', 'validee')->count();
        $tauxValidation = $totalDecided > 0 ? round(($totalValidees / $totalDecided) * 100) : 0;

        // Volume mensuel (6 derniers mois)
        $monthlyVolume = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyVolume[] = [
                'label'     => $month->translatedFormat('M'),
                'count'     => Ordonnance::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count(),
                'validated' => Ordonnance::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->where('status', 'validee')->count(),
            ];
        }

        // Top pharmacies par nombre d'ordonnances traitées
        $topPharmacies = User::whereHas('role', fn($q) => $q->where('slug', 'pharmacien'))
            ->withCount(['ordonnancesPharmacien as total' => fn($q) => $q->whereIn('status', ['validee', 'refusee', 'retiree'])])
            ->withAvg('ordonnancesPharmacien as avg_rating', 'rating')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'nbUsers', 'nbPharmaciens', 'nbClients', 'nbOrdonnances', 'nbEnAttente',
            'tauxValidation', 'monthlyVolume', 'topPharmacies'
        ));
    }
}
