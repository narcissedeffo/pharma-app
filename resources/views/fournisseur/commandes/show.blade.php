@extends('layouts.app')

@section('title', 'Traiter la Commande ' . $commande->reference)

@section('content')
<div class="animate-fade-in-up">
    <div class="page-header mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1>Commande {{ $commande->reference }}</h1>
                <span class="badge {{ $commande->statusColor() }}">
                    {{ $commande->statusLabel() }}
                </span>
            </div>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Reçue le {{ $commande->sent_at ? $commande->sent_at->format('d/m/Y H:i') : $commande->created_at->format('d/m/Y H:i') }} — Pharmacie: <strong>{{ $commande->pharmacien->name }}</strong>
            </p>
        </div>
        <a href="{{ route('fournisseur.commandes.index') }}" class="btn btn-ghost">Retour</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Items et Actions --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-slate-800">Contenu de la commande</h2>
                </div>

                <div class="space-y-3">
                    @foreach($commande->items as $item)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="font-medium text-slate-800">{{ $item->nom_medicament }}</span>
                            <span class="px-3 py-1 bg-white border border-slate-200 rounded-md text-sm font-semibold text-slate-700">
                                Qté: {{ $item->quantite }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($commande->notes)
            <div class="card p-6 bg-amber-50 border-amber-100">
                <h3 class="text-sm font-semibold text-amber-800 mb-2">Notes du pharmacien</h3>
                <p class="text-sm text-amber-900">{{ $commande->notes }}</p>
            </div>
            @endif

            {{-- Actions de statut --}}
            <div class="card p-6 bg-slate-50 border border-slate-200">
                <h3 class="text-sm font-semibold text-slate-800 mb-4 uppercase tracking-wide">Mettre à jour le statut</h3>
                
                <form method="POST" action="{{ route('fournisseur.commandes.status', $commande) }}" class="flex flex-wrap gap-3">
                    @csrf
                    
                    @if($commande->status === 'envoyee')
                        <button type="submit" name="status" value="en_preparation" class="btn bg-yellow-500 hover:bg-yellow-600 text-white">
                            Passer "En préparation"
                        </button>
                    @endif

                    @if($commande->status === 'en_preparation')
                        <button type="submit" name="status" value="expediee" class="btn bg-purple-600 hover:bg-purple-700 text-white">
                            Marquer "Expédiée"
                        </button>
                    @endif

                    @if($commande->status === 'expediee')
                        <button type="submit" name="status" value="livree" class="btn bg-green-600 hover:bg-green-700 text-white">
                            Marquer "Livrée"
                        </button>
                    @endif

                    @if($commande->status === 'livree')
                        <p class="text-sm text-green-700 font-medium flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Commande clôturée (Livrée).
                        </p>
                    @endif
                </form>
            </div>

            {{-- Facturation & Bon de Livraison --}}
            @if(in_array($commande->status, ['expediee', 'livree']))
                <div class="card p-6 border border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-800 mb-4 uppercase tracking-wide flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Facturation & Bon de Livraison
                    </h3>

                    @if($commande->facture)
                        {{-- La facture existe déjà --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 p-4 rounded-lg {{ $commande->facture->status === 'payee' ? 'bg-green-50 border border-green-200' : ($commande->facture->isOverdue() ? 'bg-red-50 border border-red-200' : 'bg-amber-50 border border-amber-200') }}">
                                <div class="flex-1">
                                    <p class="font-semibold text-slate-800">{{ $commande->facture->reference }}</p>
                                    <p class="text-xs text-slate-600 mt-1">
                                        Émise le {{ $commande->facture->date_emission->format('d/m/Y') }} — 
                                        Échéance : {{ $commande->facture->date_echeance->format('d/m/Y') }}
                                    </p>
                                    <p class="text-lg font-bold text-teal-700 mt-1">{{ number_format($commande->facture->montant_total, 0, ',', ' ') }} XAF</p>
                                </div>
                                <div>
                                    @if($commande->facture->status === 'payee')
                                        <span class="badge badge-success">Payée</span>
                                    @elseif($commande->facture->isOverdue())
                                        <span class="badge badge-danger">En retard</span>
                                    @else
                                        <span class="badge badge-warning">En attente</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('pdf.facture', $commande->facture) }}" class="btn btn-outline-primary text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Facture PDF
                                </a>
                                <a href="{{ route('pdf.bl', $commande->facture) }}" class="btn btn-outline-primary text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Bon de Livraison PDF
                                </a>

                                @if($commande->facture->status !== 'payee')
                                    <form method="POST" action="{{ route('fournisseur.factures.payee', $commande->facture) }}" onsubmit="return confirm('Confirmer la réception du paiement pour cette facture ?');">
                                        @csrf
                                        <button type="submit" class="btn bg-green-600 hover:bg-green-700 text-white text-sm flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Marquer Payée
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- Formulaire de génération --}}
                        <p class="text-sm text-slate-600 mb-4">
                            Générez la facture et le bon de livraison pour cette commande. Les documents seront téléchargeables en PDF.
                        </p>
                        <form method="POST" action="{{ route('fournisseur.factures.store', $commande) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Délai de paiement</label>
                                <div class="flex flex-wrap gap-2">
                                    <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">
                                        <input type="radio" name="delai_paiement" value="0" class="text-teal-600">
                                        <span class="text-sm">Comptant</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">
                                        <input type="radio" name="delai_paiement" value="30" checked class="text-teal-600">
                                        <span class="text-sm">30 jours</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">
                                        <input type="radio" name="delai_paiement" value="60" class="text-teal-600">
                                        <span class="text-sm">60 jours</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">
                                        <input type="radio" name="delai_paiement" value="90" class="text-teal-600">
                                        <span class="text-sm">90 jours</span>
                                    </label>
                                </div>
                                @error('delai_paiement')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <p class="text-sm text-slate-500">Montant : <strong class="text-teal-700">{{ number_format($commande->total(), 0, ',', ' ') }} XAF</strong></p>
                                <button type="submit" class="btn btn-primary flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Générer Facture & BL
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        {{-- Chat --}}
        <div class="lg:col-span-1">
            <x-commande-chat-box :commande="$commande" />
        </div>
    </div>
</div>
@endsection
