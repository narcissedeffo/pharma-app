@extends('layouts.app')

@section('title', 'Ordonnances reçues')

@section('content')
<div class="animate-fade-in-up">

    {{-- En-tête --}}
    <div class="page-header mb-6">
        <div>
            <h1>Ordonnances reçues</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Gérez les demandes de vos patients
            </p>
        </div>
    </div>

    {{-- Filtres et Recherche --}}
    <div class="mb-6 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
        {{-- Onglets --}}
        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
            <a href="{{ route('pharmacien.ordonnances.index', ['status' => 'all', 'q' => $search]) }}" 
               class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $status === 'all' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Toutes
            </a>
            <a href="{{ route('pharmacien.ordonnances.index', ['status' => 'en_attente', 'q' => $search]) }}" 
               class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 {{ $status === 'en_attente' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Nouvelles
                @if(($counts['en_attente'] ?? 0) > 0)
                    <span class="bg-yellow-100 text-yellow-800 py-0.5 px-2 rounded-full text-xs">{{ $counts['en_attente'] }}</span>
                @endif
            </a>
            <a href="{{ route('pharmacien.ordonnances.index', ['status' => 'en_cours', 'q' => $search]) }}" 
               class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 {{ $status === 'en_cours' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                En cours
                @if(($counts['en_cours'] ?? 0) > 0)
                    <span class="bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full text-xs">{{ $counts['en_cours'] }}</span>
                @endif
            </a>
            <a href="{{ route('pharmacien.ordonnances.index', ['status' => 'validee', 'q' => $search]) }}" 
               class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 {{ $status === 'validee' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                À retirer
            </a>
            <a href="{{ route('pharmacien.ordonnances.index', ['status' => 'retiree', 'q' => $search]) }}" 
               class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 {{ $status === 'retiree' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Historique
            </a>
        </nav>

        {{-- Barre de recherche --}}
        <form method="GET" action="{{ route('pharmacien.ordonnances.index') }}" class="relative pb-2 md:pb-0">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="q" value="{{ $search }}" placeholder="Chercher un patient..." class="input pl-9 w-full md:w-64 text-sm">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            @if($search)
                <a href="{{ route('pharmacien.ordonnances.index', ['status' => $status]) }}" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </form>
    </div>

    @if ($ordonnances->isEmpty())
        <div class="card empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p style="font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Aucune ordonnance</p>
            <p style="font-size: 0.875rem;">Aucune ordonnance trouvée pour les critères sélectionnés.</p>
        </div>
    @else
        <div class="space-y-3 stagger-children">
            @foreach ($ordonnances as $ordonnance)
                <a href="{{ route('pharmacien.ordonnances.show', $ordonnance) }}"
                   class="card card-link block p-5 animate-fade-in-up relative overflow-hidden"
                   style="text-decoration: none; color: inherit;">
                   
                   @if($ordonnance->status === 'en_attente')
                       <div class="absolute top-0 left-0 w-1 h-full bg-yellow-400"></div>
                   @elseif($ordonnance->status === 'en_cours')
                       <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                   @elseif($ordonnance->status === 'validee')
                       <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
                   @endif

                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0 pl-2">
                            {{-- Avatar patient --}}
                            <div style="flex-shrink: 0; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; font-weight: 700; font-size: 1rem;">
                                {{ strtoupper(substr($ordonnance->client->name, 0, 1)) }}
                            </div>

                            {{-- Infos --}}
                            <div class="min-w-0">
                                <p style="font-weight: 600; color: #0f172a; font-size: 0.9375rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">
                                    {{ $ordonnance->client->name }}
                                </p>
                                <p style="font-size: 0.8125rem; color: #64748b; margin-top: 2px;">
                                    Reçue le {{ $ordonnance->published_at?->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 flex-shrink-0">
                            @if($ordonnance->pickupSlot && !$ordonnance->pickupSlot->isConfirmed() && in_array($ordonnance->status, ['validee', 'en_cours']))
                                <span class="hidden md:inline-flex items-center gap-1 text-xs text-yellow-600 bg-yellow-50 px-2 py-1 rounded">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Attente confirmation
                                </span>
                            @endif
                            <span class="badge badge-{{ $ordonnance->status }}">
                                <span class="badge-dot"></span>
                                {{ $ordonnance->statusLabel() }}
                            </span>
                            <svg class="w-4 h-4" style="color: #cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($ordonnances->hasPages())
            <div class="mt-6">
                {{ $ordonnances->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
