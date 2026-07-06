@extends('layouts.app')

@section('title', 'Administration')

@section('content')
<div class="animate-fade-in-up">
    <div class="page-header mb-6">
        <div>
            <h1>Administration Globale</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">Vue d'ensemble et statistiques du système</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.ordonnances.index') }}" class="btn btn-outline-primary">
                Toutes les ordonnances
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                Gérer les utilisateurs
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 stagger-children">
        <div class="stat-card">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Utilisateurs</h3>
            <div class="flex items-end justify-between mt-2">
                <p style="font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ $nbUsers }}</p>
                <div class="text-right">
                    <p style="font-size: 0.75rem; color: #64748b;">{{ $nbClients }} clients</p>
                    <p style="font-size: 0.75rem; color: #64748b;">{{ $nbPharmaciens }} pharmaciens</p>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Ordonnances (Total)</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem; line-height: 1;">{{ $nbOrdonnances }}</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #eab308;">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">En attente de traitement</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem; line-height: 1;">{{ $nbEnAttente }}</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #22c55e;">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Taux de Validation</h3>
            <div class="flex items-end gap-2 mt-2">
                <p style="font-size: 2rem; font-weight: 800; color: #16a34a; line-height: 1;">{{ $tauxValidation }}%</p>
                <span class="text-xs text-slate-500 mb-1">global</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Graphique Volume --}}
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #0f172a;">Volume d'ordonnances (6 derniers mois)</h2>
            </div>
            <div style="height: 300px; width: 100%;">
                <canvas id="monthlyVolumeChart"></canvas>
            </div>
        </div>
        
        {{-- Top Pharmacies --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #0f172a;">Top Pharmacies</h2>
            </div>
            
            @if ($topPharmacies->isEmpty())
                <p style="color: #64748b; font-size: 0.875rem;">Aucune donnée suffisante.</p>
            @else
                <div class="space-y-4 mt-2">
                    @foreach ($topPharmacies as $index => $pharmacy)
                        <div class="flex items-center gap-3">
                            <div style="width: 28px; height: 28px; border-radius: 6px; background: {{ $index === 0 ? '#fef08a' : ($index === 1 ? '#e2e8f0' : ($index === 2 ? '#ffedd5' : '#f8fafc')) }}; color: {{ $index === 0 ? '#a16207' : ($index === 1 ? '#475569' : ($index === 2 ? '#c2410c' : '#94a3b8')) }}; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.875rem; flex-shrink: 0;">
                                {{ $index + 1 }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p style="font-size: 0.875rem; font-weight: 600; color: #0f172a;" class="truncate">{{ $pharmacy->name }}</p>
                                <p style="font-size: 0.75rem; color: #64748b;">{{ $pharmacy->total }} ordonnances</p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <span class="text-xs font-bold text-slate-700">{{ $pharmacy->avg_rating ? number_format($pharmacy->avg_rating, 1) : '-' }}</span>
                                <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                        </div>
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
        const ctx = document.getElementById('monthlyVolumeChart').getContext('2d');
        const monthlyData = @json($monthlyVolume);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.label),
                datasets: [
                    {
                        label: 'Total reçues',
                        data: monthlyData.map(d => d.count),
                        backgroundColor: '#cbd5e1',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Validées',
                        data: monthlyData.map(d => d.validated),
                        backgroundColor: '#0ea5e9',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { boxWidth: 12, usePointStyle: true, font: { family: "'Inter', sans-serif" } }
                    },
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
                        ticks: { stepSize: 10, color: '#64748b' },
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
