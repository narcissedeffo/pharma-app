@extends('layouts.app')

@section('title', 'Toutes les ordonnances')

@section('content')
<div class="animate-fade-in-up">

    <div class="page-header mb-6">
        <div>
            <h1>Toutes les ordonnances</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Vue globale de toutes les ordonnances du système
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">Retour au dashboard</a>
    </div>

    {{-- Filtres et Recherche --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <nav class="-mb-px flex space-x-6 overflow-x-auto border-b border-slate-200" aria-label="Tabs" style="flex: 1;">
            <a href="{{ route('admin.ordonnances.index', ['status' => 'all', 'q' => $search]) }}" 
               class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $status === 'all' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Toutes
            </a>
            @foreach(['brouillon', 'en_attente', 'en_cours', 'validee', 'refusee', 'retiree', 'expiree'] as $s)
                <a href="{{ route('admin.ordonnances.index', ['status' => $s, 'q' => $search]) }}" 
                   class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 {{ $status === $s ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                    @if(($counts[$s] ?? 0) > 0)
                        <span class="bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full text-xs">{{ $counts[$s] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <form method="GET" action="{{ route('admin.ordonnances.index') }}" class="relative pb-2 md:pb-0 shrink-0">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="q" value="{{ $search }}" placeholder="Rechercher..." class="input pl-9 w-full md:w-64 text-sm">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            @if($search)
                <a href="{{ route('admin.ordonnances.index', ['status' => $status]) }}" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="table-wrap">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">ID</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Patient</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Pharmacien</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Fichier</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Date</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ordonnances as $ordonnance)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 text-sm text-slate-500">#{{ $ordonnance->id }}</td>
                            <td class="py-3 px-4 text-sm font-medium text-slate-900">{{ $ordonnance->client->name }}</td>
                            <td class="py-3 px-4 text-sm text-slate-600">{{ $ordonnance->pharmacien ? $ordonnance->pharmacien->name : '-' }}</td>
                            <td class="py-3 px-4 text-sm text-slate-600 max-w-[200px] truncate" title="{{ $ordonnance->original_filename }}">
                                {{ $ordonnance->original_filename }}
                            </td>
                            <td class="py-3 px-4 text-sm text-slate-500">{{ $ordonnance->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 text-sm">
                                <span class="badge badge-{{ $ordonnance->status }}">
                                    <span class="badge-dot"></span>
                                    {{ $ordonnance->statusLabel() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">Aucune ordonnance trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if ($ordonnances->hasPages())
        <div class="mt-4">
            {{ $ordonnances->links() }}
        </div>
    @endif

</div>
@endsection
