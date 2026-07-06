@extends('layouts.app')

@section('title', 'Mes ordonnances')

@section('content')
<div class="animate-fade-in-up">

    {{-- En-tête --}}
    <div class="page-header mb-6">
        <div>
            <h1>Mes ordonnances</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Gérez et suivez toutes vos ordonnances
            </p>
        </div>
        <a href="{{ route('client.ordonnances.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Déposer une ordonnance
        </a>
    </div>

    {{-- Filtres (Onglets) --}}
    <div class="mb-6 border-b border-slate-200">
        <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
            <a href="{{ route('client.ordonnances.index', ['status' => 'all']) }}" 
               class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $status === 'all' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Toutes
            </a>
            <a href="{{ route('client.ordonnances.index', ['status' => 'en_attente']) }}" 
               class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 {{ $status === 'en_attente' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                En attente
                @if(($counts['en_attente'] ?? 0) > 0)
                    <span class="bg-yellow-100 text-yellow-800 py-0.5 px-2 rounded-full text-xs">{{ $counts['en_attente'] }}</span>
                @endif
            </a>
            <a href="{{ route('client.ordonnances.index', ['status' => 'en_cours']) }}" 
               class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 {{ $status === 'en_cours' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                En cours
                @if(($counts['en_cours'] ?? 0) > 0)
                    <span class="bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full text-xs">{{ $counts['en_cours'] }}</span>
                @endif
            </a>
            <a href="{{ route('client.ordonnances.index', ['status' => 'validee']) }}" 
               class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 {{ $status === 'validee' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Validées
            </a>
        </nav>
    </div>

    @if ($ordonnances->isEmpty())
        <div class="card empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p style="font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Aucune ordonnance trouvée</p>
            <p style="font-size: 0.875rem;">Vous n'avez pas d'ordonnances correspondant à ce filtre.</p>
            @if($status === 'all')
                <div style="margin-top: 1.5rem;">
                    <a href="{{ route('client.ordonnances.create') }}" class="btn btn-primary">
                        Déposer une ordonnance
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="space-y-3 stagger-children">
            @foreach ($ordonnances as $ordonnance)
                <a href="{{ route('client.ordonnances.show', $ordonnance) }}"
                   class="card card-link block p-5 animate-fade-in-up relative overflow-hidden"
                   style="text-decoration: none; color: inherit;">
                   
                   @if($ordonnance->isExpiringSoon() && $ordonnance->status === 'brouillon')
                       <div class="absolute top-0 right-0 w-16 h-16 pointer-events-none">
                           <div class="absolute transform rotate-45 bg-amber-500 text-white text-[10px] font-bold py-1 right-[-35px] top-[15px] w-[100px] text-center shadow-sm">
                               Bientôt<br>expirée
                           </div>
                       </div>
                   @endif

                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            {{-- Icône fichier --}}
                            <div style="flex-shrink: 0; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
                                        background: {{ in_array($ordonnance->mime_type, ['image/jpeg','image/png']) ? 'linear-gradient(135deg, #ede9fe, #ddd6fe)' : 'linear-gradient(135deg, #fee2e2, #fecaca)' }};">
                                @if (in_array($ordonnance->mime_type, ['image/jpeg','image/png']))
                                    <svg class="w-5 h-5" style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>

                            {{-- Infos --}}
                            <div class="min-w-0">
                                <p style="font-weight: 600; color: #0f172a; font-size: 0.9375rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 280px;">
                                    {{ $ordonnance->original_filename }}
                                </p>
                                <p style="font-size: 0.8125rem; color: #64748b; margin-top: 2px;">
                                    Déposée le {{ $ordonnance->created_at->format('d/m/Y') }}
                                    @if ($ordonnance->pharmacien)
                                        · <span style="color: #0f766e; font-weight: 500;">{{ $ordonnance->pharmacien->name }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
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
