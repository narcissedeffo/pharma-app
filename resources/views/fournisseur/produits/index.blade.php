@extends('layouts.app')

@section('title', 'Mon Catalogue Produits')

@section('content')
<div class="animate-fade-in-up">

    {{-- En-tête de page --}}
    <div class="page-header mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1>Mon Catalogue</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Gérez les médicaments et matériels que vous proposez aux pharmacies.
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('fournisseur.produits.import.create') }}"
               class="btn btn-outline-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #fff; border: 1.5px solid #cbd5e1; color: #475569; border-radius: 10px; padding: 10px 16px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s;"
               onmouseover="this.style.borderColor='#94a3b8'; this.style.color='#1e293b'"
               onmouseout="this.style.borderColor='#cbd5e1'; this.style.color='#475569'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Importer CSV/PDF
            </a>
            <a href="{{ route('fournisseur.produits.create') }}"
               style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #0f766e, #0d9488); color: #fff; border-radius: 10px; padding: 10px 20px; font-size: 0.875rem; font-weight: 600; text-decoration: none; box-shadow: 0 4px 14px rgba(15,118,110,0.35); transition: all 0.2s;"
               onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(15,118,110,0.45)'"
               onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 14px rgba(15,118,110,0.35)'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter un produit
            </a>
        </div>
    </div>

    {{-- Stats rapides --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        @php
            $total     = $produits->total();
            $disponibles = $produits->getCollection()->where('is_available', true)->count();
        @endphp
        <div class="card" style="padding: 1.25rem; text-align: center;">
            <p style="font-size: 2rem; font-weight: 700; color: #0f766e; line-height: 1;">{{ $total }}</p>
            <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">Produits au total</p>
        </div>
        <div class="card" style="padding: 1.25rem; text-align: center;">
            <p style="font-size: 2rem; font-weight: 700; color: #16a34a; line-height: 1;">{{ $produits->getCollection()->where('is_available', true)->count() }}</p>
            <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">Disponibles (page courante)</p>
        </div>
        <div class="card" style="padding: 1.25rem; text-align: center;">
            <p style="font-size: 2rem; font-weight: 700; color: #dc2626; line-height: 1;">{{ $produits->getCollection()->where('is_available', false)->count() }}</p>
            <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">Indisponibles (page courante)</p>
        </div>
    </div>

    {{-- Table produits --}}
    <div class="card overflow-hidden" x-data="{ 
        selected: [], 
        selectAll: false,
        toggleAll() {
            if (this.selectAll) {
                this.selected = Array.from(document.querySelectorAll('.item-checkbox')).map(cb => cb.value);
            } else {
                this.selected = [];
            }
        }
    }">
        @if ($produits->isEmpty())
            <div style="padding: 4rem 2rem; text-align: center;">
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #f0fdfa, #ccfbf1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <svg class="w-7 h-7" style="color: #0d9488;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Votre catalogue est vide</p>
                <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 1.5rem;">Commencez par ajouter vos premiers produits.</p>
                <a href="{{ route('fournisseur.produits.create') }}"
                   style="display: inline-flex; align-items: center; gap: 0.5rem; background: #0f766e; color: #fff; border-radius: 8px; padding: 9px 18px; font-size: 0.875rem; font-weight: 500; text-decoration: none;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Ajouter un produit
                </a>
            </div>
        @else
            <form id="bulk-delete-form" method="POST" action="{{ route('fournisseur.produits.bulk-destroy') }}" style="display: none;">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
            </form>

            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between" x-show="selected.length > 0" x-cloak style="display: none;">
                <span class="text-sm font-medium text-slate-700"><span x-text="selected.length"></span> élément(s) sélectionné(s)</span>
                <button form="bulk-delete-form" type="submit" class="btn btn-sm bg-red-600 hover:bg-red-700 text-white flex items-center gap-1.5" onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ces produits ?')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Supprimer
                </button>
            </div>
            <div class="table-wrap">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200" style="width: 40px;">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
                            </th>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">CIP</th>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Produit</th>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Description</th>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Date de Péremption</th>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Prix HT</th>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Statut</th>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($produits as $produit)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4">
                                    <input type="checkbox" name="ids[]" value="{{ $produit->id }}" x-model="selected" class="item-checkbox rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-sm font-medium text-slate-600">{{ $produit->cip ?? '—' }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-sm font-semibold text-slate-900">{{ $produit->name }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-sm text-slate-500" style="max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $produit->description ?? '—' }}
                                    </p>
                                </td>
                                <td class="py-3 px-4">
                                    @if ($produit->date_peremption)
                                        <span class="text-sm {{ $produit->date_peremption->isPast() ? 'text-red-500 font-medium' : 'text-slate-600' }}">
                                            {{ $produit->date_peremption->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($produit->price !== null)
                                        <span class="text-sm font-medium text-slate-800">{{ number_format($produit->price, 0, ',', ' ') }} XAF</span>
                                    @else
                                        <span class="text-sm text-slate-400">Sur devis</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($produit->is_available)
                                        <span class="badge badge-success">✓ Disponible</span>
                                    @else
                                        <span class="badge badge-danger">✗ Indisponible</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('fournisseur.produits.edit', $produit) }}"
                                           class="text-sm font-medium text-teal-600 hover:text-teal-900 transition-colors">
                                            Modifier
                                        </a>
                                        <span style="color: #cbd5e1;">|</span>
                                        <form method="POST" action="{{ route('fournisseur.produits.destroy', $produit) }}"
                                              onsubmit="return confirm('Supprimer « {{ addslashes($produit->name) }} » du catalogue ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700 transition-colors"
                                                    style="background: none; border: none; cursor: pointer; padding: 0;">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($produits->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $produits->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
