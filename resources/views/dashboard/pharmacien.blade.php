@extends('layouts.app')

@section('title', 'Espace Pharmacien')

@section('content')
<div class="animate-fade-in-up">
    <div class="page-header mb-6">
        <div>
            <h1>Tableau de bord — {{ auth()->user()->name }}</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Suivez votre activité et gérez les ordonnances de vos patients.
            </p>
        </div>
        <a href="{{ route('pharmacien.ordonnances.index') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Toutes les ordonnances
        </a>
    </div>

    {{-- Alertes Nouvelles Ordonnances --}}
    @if($nouvelles > 0)
        <div class="card p-4 mb-6 bg-yellow-50 border-yellow-200 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-100 p-2 rounded-full text-yellow-600">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-yellow-800">Nouvelles ordonnances !</h3>
                    <p class="text-sm text-yellow-700">Vous avez {{ $nouvelles }} ordonnance(s) en attente de prise en charge.</p>
                </div>
            </div>
            <a href="{{ route('pharmacien.ordonnances.index', ['status' => 'en_attente']) }}" class="btn bg-yellow-600 text-white hover:bg-yellow-700 shadow-sm shrink-0">
                Traiter maintenant
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 stagger-children">
        <div class="stat-card" style="border-bottom: 4px solid #eab308;">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">À traiter</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem; line-height: 1;">{{ $nouvelles }}</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #3b82f6;">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">En cours</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem; line-height: 1;">{{ $enCours }}</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #10b981;">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Traitées</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem; line-height: 1;">{{ $traitees }}</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #8b5cf6;">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Note Moyenne</h3>
            <div class="flex items-end gap-2 mt-2">
                <p style="font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1;">
                    {{ $avgRating ? number_format($avgRating, 1) : '-' }}
                </p>
                <span style="font-size: 1.25rem; color: #fbbf24; margin-bottom: 0.1rem;">★</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Graphique Activité --}}
        <div class="lg:col-span-2 card p-6">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Activité des 7 derniers jours</h2>
            <div style="height: 300px; width: 100%;">
                <canvas id="weeklyActivityChart"></canvas>
            </div>
        </div>
        
        {{-- Liste à traiter --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #0f172a;">À traiter en priorité</h2>
            </div>
            
            @php
                $urgentes = auth()->user()->ordonnancesPharmacien()->where('status', 'en_attente')->oldest()->take(5)->get();
            @endphp

            @if ($urgentes->isEmpty())
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p style="color: #64748b; font-size: 0.875rem;">Vous êtes à jour !</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($urgentes as $ord)
                        <a href="{{ route('pharmacien.ordonnances.show', $ord) }}" class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors" style="text-decoration: none;">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #fef08a, #fde047); color: #854d0e; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.875rem; flex-shrink: 0;">
                                    {{ strtoupper(substr($ord->client->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p style="font-size: 0.875rem; font-weight: 600; color: #0f172a;" class="truncate">{{ $ord->client->name }}</p>
                                    <p style="font-size: 0.75rem; color: #64748b;">
                                        Depuis {{ $ord->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
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
        const ctx = document.getElementById('weeklyActivityChart').getContext('2d');
        const weeklyData = @json($weeklyActivity);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: weeklyData.map(d => d.label),
                datasets: [{
                    label: 'Ordonnances reçues',
                    data: weeklyData.map(d => d.count),
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13, 148, 136, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0f766e',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
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
                        bodyFont: { size: 13, family: "'Inter', sans-serif" },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' ordonnance(s)';
                            }
                        }
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
