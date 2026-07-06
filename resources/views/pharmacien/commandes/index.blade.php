@extends('layouts.app')

@section('title', 'Mes Commandes Fournisseur')

@section('content')
<div class="animate-fade-in-up">
    <div class="page-header mb-6 flex justify-between items-end">
        <div>
            <h1>Commandes Fournisseur</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Gérez vos réassorts et commandes de médicaments manquants.
            </p>
        </div>
        <a href="{{ route('pharmacien.commandes.create') }}" class="btn btn-primary">
            + Nouvelle Commande
        </a>
    </div>

    {{-- Filtres --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <nav class="-mb-px flex space-x-6 overflow-x-auto border-b border-slate-200" aria-label="Tabs" style="flex: 1;">
            <a href="{{ route('pharmacien.commandes.index', ['status' => 'all']) }}" 
               class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $status === 'all' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                Toutes
            </a>
            @foreach(['brouillon', 'envoyee', 'en_preparation', 'expediee', 'livree'] as $s)
                <a href="{{ route('pharmacien.commandes.index', ['status' => $s]) }}" 
                   class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 {{ $status === $s ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                    @if(($counts[$s] ?? 0) > 0)
                        <span class="bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full text-xs">{{ $counts[$s] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>
    </div>

    <div class="card overflow-hidden">
        <div class="table-wrap">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Référence</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Fournisseur</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Date d'envoi</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Statut</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($commandes as $commande)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 text-sm font-medium text-slate-900">{{ $commande->reference }}</td>
                            <td class="py-3 px-4 text-sm text-slate-600">{{ $commande->fournisseur->name }}</td>
                            <td class="py-3 px-4 text-sm text-slate-500">
                                {{ $commande->sent_at ? $commande->sent_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-sm">
                                <span class="badge {{ $commande->statusColor() }}">
                                    {{ $commande->statusLabel() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm">
                                <a href="{{ route('pharmacien.commandes.show', $commande) }}" class="text-teal-600 hover:text-teal-900 font-medium">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">Aucune commande trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if ($commandes->hasPages())
        <div class="mt-4">
            {{ $commandes->links() }}
        </div>
    @endif
</div>
@endsection
