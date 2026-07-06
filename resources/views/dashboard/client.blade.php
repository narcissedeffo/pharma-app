@extends('layouts.app')

@section('title', 'Mon espace')

@section('content')
<div class="animate-fade-in-up">
    <div class="page-header">
        <div>
            <h1>Bonjour, {{ auth()->user()->name }} 👋</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">Bienvenue sur votre espace patient.</p>
        </div>
        <a href="{{ route('client.ordonnances.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle ordonnance
        </a>
    </div>

    @if($nextPickup)
        <div class="card p-6 mb-6 bg-gradient-to-br from-teal-500 to-teal-700 text-white border-0 shadow-lg relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H14a1 1 0 001-1v-2.828a1 1 0 00-.293-.707l-2.828-2.828A1 1 0 0011.172 8H9V5a1 1 0 00-1-1H3z" /></svg>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-teal-100 text-sm font-semibold mb-1 uppercase tracking-wider">Prochain Retrait Prévu</h2>
                    <p class="text-2xl font-bold mb-1">{{ $nextPickup->pickupSlot->proposed_at->translatedFormat('l d F Y à H\hi') }}</p>
                    <p class="text-teal-100 text-sm">Pharmacie : {{ $nextPickup->pharmacien->name }}</p>
                </div>
                <a href="{{ route('client.ordonnances.show', $nextPickup) }}" class="btn bg-white text-teal-700 hover:bg-teal-50 shadow-sm self-start md:self-auto">
                    Voir l'ordonnance
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 stagger-children">
        <div class="stat-card">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Total déposées</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem; line-height: 1;">{{ $total }}</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #eab308;">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">En attente</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem; line-height: 1;">{{ $enAttente }}</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #22c55e;">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Validées</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem; line-height: 1;">{{ $validees }}</p>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid {{ $expiringSoon > 0 ? '#f59e0b' : '#94a3b8' }};">
            <h3 style="font-size: 0.8125rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Expirent bientôt</h3>
            <div class="flex items-end gap-3 mt-2">
                <p style="font-size: 2rem; font-weight: 800; color: {{ $expiringSoon > 0 ? '#b45309' : '#0f172a' }}; line-height: 1;">{{ $expiringSoon }}</p>
                @if($expiringSoon > 0)
                    <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full mb-1">Attention</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #0f172a;">Mes ordonnances récentes</h2>
                <a href="{{ route('client.ordonnances.index') }}" class="btn btn-ghost" style="font-size: 0.75rem; padding: 0.4rem 0.8rem;">Voir tout</a>
            </div>
            
            @php
                $recentes = auth()->user()->ordonnancesClient()->with('pharmacien')->latest()->take(3)->get();
            @endphp
            
            @if ($recentes->isEmpty())
                <p style="color: #64748b; font-size: 0.875rem;">Vous n'avez pas encore déposé d'ordonnance.</p>
            @else
                <div class="space-y-3">
                    @foreach ($recentes as $ord)
                        <a href="{{ route('client.ordonnances.show', $ord) }}" class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors" style="text-decoration: none;">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div style="width: 36px; height: 36px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p style="font-size: 0.875rem; font-weight: 600; color: #0f172a;" class="truncate">{{ $ord->original_filename }}</p>
                                    <p style="font-size: 0.75rem; color: #64748b;">{{ $ord->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <span class="badge badge-{{ $ord->status }} ml-2 flex-shrink-0" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">
                                {{ $ord->statusLabel() }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
