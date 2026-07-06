@extends('layouts.app')

@section('title', 'Catalogue de ' . $fournisseur->name)

@section('content')
<div class="animate-fade-in-up">

    {{-- Fil d'Ariane --}}
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #64748b; margin-bottom: 1.5rem;">
        <a href="{{ route('pharmacien.catalogue.index') }}" style="color: #0d9488; text-decoration: none; font-weight: 500;">Catalogue Fournisseurs</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        <span>{{ $fournisseur->name }}</span>
    </nav>

    {{-- En-tête fournisseur --}}
    <div style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); border-radius: 16px; padding: 2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1.5rem; box-shadow: 0 8px 24px rgba(15,118,110,0.3);">
        <div style="width: 64px; height: 64px; border-radius: 16px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 2px solid rgba(255,255,255,0.35);">
            <span style="font-size: 1.75rem; font-weight: 700; color: #fff;">
                {{ strtoupper(substr($fournisseur->name, 0, 1)) }}
            </span>
        </div>
        <div>
            <h1 style="color: #fff; font-size: 1.375rem; font-weight: 700; margin: 0 0 0.25rem;">{{ $fournisseur->name }}</h1>
            <p style="color: rgba(255,255,255,0.75); font-size: 0.875rem; margin: 0;">
                {{ $fournisseur->email }}
                &nbsp;·&nbsp;
                <span style="font-weight: 600; color: #fff;">{{ $produits->total() }} produit{{ $produits->total() > 1 ? 's' : '' }} au catalogue</span>
            </p>
        </div>
    </div>

    {{-- Résumé disponibilité --}}
    @php
        $nbDispo     = $produits->getCollection()->where('is_available', true)->count();
        $nbIndispo   = $produits->getCollection()->where('is_available', false)->count();
    @endphp
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #dcfce7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg class="w-5 h-5" style="color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p style="font-size: 1.5rem; font-weight: 700; color: #16a34a; line-height: 1;">{{ $nbDispo }}</p>
                <p style="font-size: 0.8rem; color: #64748b;">Disponibles</p>
            </div>
        </div>
        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #fee2e2; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg class="w-5 h-5" style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p style="font-size: 1.5rem; font-weight: 700; color: #dc2626; line-height: 1;">{{ $nbIndispo }}</p>
                <p style="font-size: 0.8rem; color: #64748b;">Indisponibles</p>
            </div>
        </div>
    </div>

    {{-- Tableau produits --}}
    <div class="card overflow-hidden">
        @if ($produits->isEmpty())
            <div style="padding: 4rem 2rem; text-align: center;">
                <div style="width: 56px; height: 56px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <svg class="w-6 h-6" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <p style="font-size: 0.9375rem; font-weight: 600; color: #64748b;">Ce fournisseur n'a pas encore ajouté de produits.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="w-full text-left border-collapse">
                    <thead>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Produit</th>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Description</th>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Prix</th>
                            <th class="py-3 px-4 bg-slate-50 font-semibold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200 text-center">Disponibilité / Panier</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($produits as $produit)
                            <tr class="hover:bg-slate-50 transition-colors {{ !$produit->is_available ? 'opacity-60' : '' }}">
                                <td class="py-3.5 px-4">
                                    <p class="text-sm font-semibold text-slate-900">{{ $produit->name }}</p>
                                </td>
                                <td class="py-3.5 px-4">
                                    <p class="text-sm text-slate-500" style="max-width: 320px;">
                                        {{ $produit->description ?? '—' }}
                                    </p>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($produit->price !== null)
                                        <span style="font-size: 0.9rem; font-weight: 700; color: #0f766e;">
                                            {{ number_format($produit->price, 0, ',', ' ') }} XAF
                                        </span>
                                    @else
                                        <span class="text-sm" style="color: #94a3b8; font-style: italic;">Sur devis</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($produit->is_available)
                                        <form method="POST" action="{{ route('pharmacien.panier.add') }}" class="flex items-center justify-center gap-2">
                                            @csrf
                                            <input type="hidden" name="produit_id" value="{{ $produit->id }}">
                                            <input type="number" name="quantite" value="1" min="1" required class="input" style="width: 70px; padding: 0.35rem; height: auto;" title="Quantité">
                                            <button type="submit" class="btn btn-primary" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; display: flex; align-items: center; gap: 0.25rem;">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                Ajouter
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge badge-danger">✗ Indisponible</span>
                                    @endif
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

    {{-- Lien Passer une commande --}}
    <div class="mt-6" style="text-align: right;">
        <a href="{{ route('pharmacien.commandes.create', ['fournisseur_id' => $fournisseur->id]) }}"
           style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #0f766e, #0d9488); color: #fff; border-radius: 10px; padding: 11px 22px; font-size: 0.9rem; font-weight: 600; text-decoration: none; box-shadow: 0 4px 14px rgba(15,118,110,0.35); transition: all 0.2s;"
           onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(15,118,110,0.45)'"
           onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 14px rgba(15,118,110,0.35)'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Passer une commande à ce fournisseur
        </a>
    </div>
</div>
@endsection
