@extends('layouts.app')

@section('title', 'Espace Fournisseur')

@section('content')
<div class="animate-fade-in-up">
    <div class="page-header mb-6">
        <div>
            <h1>Tableau de bord — {{ auth()->user()->name }}</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Supervisez vos commandes de réassort B2B.
            </p>
        </div>
        <a href="{{ route('fournisseur.commandes.index') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Toutes les commandes
        </a>
    </div>

    @if($nouvelles > 0)
        <div class="card p-4 mb-6 bg-yellow-50 border-yellow-200 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-100 p-2 rounded-full text-yellow-600">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-yellow-800">Nouvelles commandes reçues !</h3>
                    <p class="text-sm text-yellow-700">Vous avez {{ $nouvelles }} commande(s) en attente de préparation.</p>
                </div>
            </div>
            <a href="{{ route('fournisseur.commandes.index', ['status' => 'envoyee']) }}" class="btn bg-yellow-600 text-white hover:bg-yellow-700 shadow-sm shrink-0">
                Traiter maintenant
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 stagger-children">
        <div class="stat-card" style="border-bottom: 4px solid #eab308;">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">À traiter</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem; line-height: 1;">{{ $nouvelles }}</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #3b82f6;">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">En cours de livraison</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem; line-height: 1;">{{ $enCours }}</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #10b981;">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Livrées</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem; line-height: 1;">{{ $livrees }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Graphique Activité --}}
        <div class="lg:col-span-2 card p-6">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Volume de commandes (6 derniers mois)</h2>
            <div style="height: 300px; width: 100%;">
                <canvas id="monthlyActivityChart"></canvas>
            </div>
        </div>
        
        {{-- Liste à traiter --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #0f172a;">Dernières commandes</h2>
            </div>
            
            @php
                $recentes = auth()->user()->commandesRecues()
                    ->where('status', '!=', 'brouillon')
                    ->latest('sent_at')
                    ->take(5)
                    ->get();
            @endphp

            @if ($recentes->isEmpty())
                <div class="text-center py-8">
                    <p style="color: #64748b; font-size: 0.875rem;">Aucune commande pour le moment.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($recentes as $cmd)
                        <a href="{{ route('fournisseur.commandes.show', $cmd) }}" class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors" style="text-decoration: none;">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="min-w-0">
                                    <p style="font-size: 0.875rem; font-weight: 600; color: #0f172a;" class="truncate">{{ $cmd->pharmacien->name }}</p>
                                    <p style="font-size: 0.75rem; color: #64748b;">
                                        {{ $cmd->reference }}
                                    </p>
                                </div>
                            </div>
                            <span class="badge {{ $cmd->statusColor() }} ml-2 flex-shrink-0" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">
                                {{ $cmd->statusLabel() }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('monthlyActivityChart').getContext('2d');
        const monthlyData = @json($monthlyActivity);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.label),
                datasets: [{
                    label: 'Commandes reçues',
                    data: monthlyData.map(d => d.count),
                    backgroundColor: '#0d9488',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 13, family: "'Inter', sans-serif" },
                        bodyFont: { size: 13, family: "'Inter', sans-serif" }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#64748b' },
                        grid: { color: '#f1f5f9', drawBorder: false }
                    },
                    x: {
                        ticks: { color: '#64748b' },
                        grid: { display: false, drawBorder: false }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
