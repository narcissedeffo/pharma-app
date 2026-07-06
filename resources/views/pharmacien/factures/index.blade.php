@extends('layouts.app')

@section('title', 'Mes Factures')

@section('content')
<div class="animate-fade-in-up">
    <div class="page-header mb-6">
        <div>
            <h1>Mes Factures</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">Consultez vos factures fournisseurs et vos échéances de paiement.</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-6 p-4">
        <div class="flex gap-2 overflow-x-auto pb-2">
            <a href="{{ route('pharmacien.factures.index', ['status' => 'all']) }}" 
               class="btn {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }} text-sm whitespace-nowrap">
                Toutes les factures
            </a>
            <a href="{{ route('pharmacien.factures.index', ['status' => 'en_attente']) }}" 
               class="btn {{ $status === 'en_attente' ? 'btn-primary' : 'btn-outline-primary' }} text-sm whitespace-nowrap">
                À payer ({{ $counts['en_attente'] ?? 0 }})
            </a>
            <a href="{{ route('pharmacien.factures.index', ['status' => 'en_retard']) }}" 
               class="btn {{ $status === 'en_retard' ? 'btn-primary' : 'btn-outline-primary' }} text-sm whitespace-nowrap" style="{{ $status === 'en_retard' ? 'background:#dc2626;border-color:#dc2626' : 'color:#dc2626;border-color:#fca5a5' }}">
                En retard ({{ $counts['en_retard'] ?? 0 }})
            </a>
            <a href="{{ route('pharmacien.factures.index', ['status' => 'payee']) }}" 
               class="btn {{ $status === 'payee' ? 'btn-primary' : 'btn-outline-primary' }} text-sm whitespace-nowrap" style="{{ $status === 'payee' ? 'background:#16a34a;border-color:#16a34a' : 'color:#16a34a;border-color:#86efac' }}">
                Historique (Payées) ({{ $counts['payee'] ?? 0 }})
            </a>
        </div>
    </div>

    <!-- Liste des factures -->
    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Facture</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Fournisseur</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Montant</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Date & Échéance</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Statut</th>
                        <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($factures as $facture)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4">
                                <span class="font-semibold text-teal-700">{{ $facture->reference }}</span>
                                <p class="text-xs text-slate-500 mt-1">Réf Cmd: <a href="{{ route('pharmacien.commandes.show', $facture->commande) }}" class="hover:underline">{{ $facture->commande->reference }}</a></p>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-medium text-slate-800">{{ $facture->commande->fournisseur->name }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ number_format($facture->montant_total, 0, ',', ' ') }} XAF
                            </td>
                            <td class="py-3 px-4 text-sm">
                                Emise : {{ $facture->date_emission->format('d/m/Y') }}<br>
                                <span class="{{ $facture->isOverdue() ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                                    Échéance : {{ $facture->date_echeance->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($facture->status === 'payee')
                                    <span class="badge badge-success">Payée</span>
                                @elseif($facture->isOverdue())
                                    <span class="badge badge-danger">En retard</span>
                                @else
                                    <span class="badge badge-warning">À payer</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('pdf.facture', $facture) }}" class="text-teal-600 hover:text-teal-800 p-1" title="Télécharger Facture PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                Aucune facture trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">
        {{ $factures->links() }}
    </div>
</div>
@endsection
