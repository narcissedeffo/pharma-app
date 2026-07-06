@extends('layouts.app')

@section('title', 'Détail Commande ' . $commande->reference)

@section('content')
<div class="animate-fade-in-up">
    <div class="page-header mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1>
                    @if($commande->status === 'brouillon')
                        Mon Panier
                    @else
                        Commande {{ $commande->reference }}
                    @endif
                </h1>
                <span class="badge {{ $commande->statusColor() }}">
                    {{ $commande->statusLabel() }}
                </span>
            </div>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Fournisseur: <strong>{{ $commande->fournisseur->name }}</strong> — 
                @if($commande->status === 'brouillon')
                    Créé le {{ $commande->created_at->format('d/m/Y H:i') }}
                @else
                    Envoyée le {{ $commande->sent_at ? $commande->sent_at->format('d/m/Y H:i') : 'N/A' }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($commande->status !== 'brouillon')
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="btn btn-outline-primary" style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Exporter
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg overflow-hidden z-50 border border-slate-200"
                         style="display: none;">
                        <a href="{{ route('pharmacien.commandes.export.pdf', $commande) }}" class="flex items-center gap-2 px-4 py-3 hover:bg-slate-50 border-b border-slate-100 text-sm text-slate-700 transition-colors">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Format PDF
                        </a>
                        <a href="{{ route('pharmacien.commandes.export.csv', $commande) }}" class="flex items-center gap-2 px-4 py-3 hover:bg-slate-50 text-sm text-slate-700 transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Format CSV (Excel)
                        </a>
                    </div>
                </div>
            @endif
            <a href="{{ route('pharmacien.commandes.index') }}" class="btn btn-ghost">Retour</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Items de la commande --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-0 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-slate-800">Articles ({{ $commande->items->count() }})</h2>
                    @if($commande->status === 'brouillon')
                        <a href="{{ route('pharmacien.catalogue.show', $commande->fournisseur_id) }}" class="text-sm text-teal-600 hover:text-teal-800 font-medium">+ Ajouter des articles</a>
                    @endif
                </div>

                <div class="table-wrap">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="py-3 px-6 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider">Produit</th>
                                <th class="py-3 px-6 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider text-center">Quantité</th>
                                <th class="py-3 px-6 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider text-right">Prix Unitaire</th>
                                <th class="py-3 px-6 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider text-right">Total</th>
                                @if($commande->status === 'brouillon')
                                    <th class="py-3 px-4 bg-slate-50"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($commande->items as $item)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-6">
                                        <p class="text-sm font-semibold text-slate-900">{{ $item->nom_medicament }}</p>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-block bg-slate-100 px-3 py-1 rounded text-sm font-semibold text-slate-700">
                                            {{ $item->quantite }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if($item->prix_unitaire !== null)
                                            <span class="text-sm text-slate-600">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} XAF</span>
                                        @else
                                            <span class="text-sm text-slate-400 italic">Sur devis</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if($item->prix_unitaire !== null)
                                            <span class="text-sm font-bold text-teal-700">{{ number_format($item->prix_unitaire * $item->quantite, 0, ',', ' ') }} XAF</span>
                                        @else
                                            <span class="text-sm text-slate-400 italic">—</span>
                                        @endif
                                    </td>
                                    @if($commande->status === 'brouillon')
                                        <td class="py-4 px-4 text-right">
                                            <form method="POST" action="{{ route('pharmacien.panier.remove', $item) }}" onsubmit="return confirm('Retirer cet article du panier ?');">
                                                @csrf
                                                <button type="submit" class="text-red-400 hover:text-red-600 p-1" title="Retirer">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $commande->status === 'brouillon' ? 5 : 4 }}" class="py-8 text-center text-slate-500">
                                        Aucun article dans cette commande.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                    <span class="text-slate-600 font-medium">Total Estimé</span>
                    <span class="text-xl font-bold text-teal-700">{{ number_format($commande->total(), 0, ',', ' ') }} XAF</span>
                </div>
            </div>
            
            @if($commande->status === 'brouillon')
                <div class="card p-6">
                    <form method="POST" action="{{ route('pharmacien.commandes.send', $commande) }}">
                        @csrf
                        {{-- On doit renvoyer des inputs cachés pour passer la validation existante, ou modifier la validation dans le controleur. Modifions le contrôleur pour ignorer 'items' si c'est un panier déjà rempli en DB --}}
                        <div class="mb-4">
                            <label class="form-label block mb-2 text-sm font-medium text-gray-700">Notes ou instructions pour le fournisseur</label>
                            <textarea name="notes" class="input w-full" rows="3" placeholder="Instruction de livraison, urgence, etc...">{{ $commande->notes }}</textarea>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit" class="btn btn-primary" {{ $commande->items->isEmpty() ? 'disabled' : '' }}>
                                Valider et Envoyer la commande
                            </button>
                        </div>
                    </form>
                </div>
            @else
                @if($commande->notes)
                <div class="card p-6 bg-amber-50 border-amber-100">
                    <h3 class="text-sm font-semibold text-amber-800 mb-2">Notes internes</h3>
                    <p class="text-sm text-amber-900">{{ $commande->notes }}</p>
                </div>
                @endif

                {{-- Documents (Facture & BL) --}}
                @if($commande->facture)
                    <div class="card p-6 border border-slate-200">
                        <h3 class="text-sm font-semibold text-slate-800 mb-4 uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Documents
                        </h3>

                        <div class="flex items-center gap-3 p-4 rounded-lg mb-4 {{ $commande->facture->status === 'payee' ? 'bg-green-50 border border-green-200' : ($commande->facture->isOverdue() ? 'bg-red-50 border border-red-200' : 'bg-amber-50 border border-amber-200') }}">
                            <div class="flex-1">
                                <p class="font-semibold text-slate-800">Facture {{ $commande->facture->reference }}</p>
                                <p class="text-xs text-slate-600 mt-1">
                                    Échéance : {{ $commande->facture->date_echeance->format('d/m/Y') }}
                                    @if($commande->facture->isOverdue())
                                        <span class="text-red-600 font-semibold">(Dépassée)</span>
                                    @else
                                        ({{ now()->diffInDays($commande->facture->date_echeance, false) > 0 ? 'dans ' . now()->diffInDays($commande->facture->date_echeance) . ' jours' : 'Aujourd\'hui' }})
                                    @endif
                                </p>
                                <p class="text-lg font-bold text-teal-700 mt-1">{{ number_format($commande->facture->montant_total, 0, ',', ' ') }} XAF</p>
                            </div>
                            <div>
                                @if($commande->facture->status === 'payee')
                                    <span class="badge badge-success">Payée</span>
                                @elseif($commande->facture->isOverdue())
                                    <span class="badge badge-danger">En retard</span>
                                @else
                                    <span class="badge badge-warning">À payer</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('pdf.facture', $commande->facture) }}" class="btn btn-outline-primary text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Télécharger Facture
                            </a>
                            <a href="{{ route('pdf.bl', $commande->facture) }}" class="btn btn-outline-primary text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Télécharger Bon de Livraison
                            </a>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- Chat --}}
        <div class="lg:col-span-1">
            @if($commande->status !== 'brouillon')
                <x-commande-chat-box :commande="$commande" />
            @else
                <div class="card p-6 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    La messagerie sera disponible une fois la commande envoyée au fournisseur.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
