@extends('layouts.app')

@section('title', 'Catalogue Fournisseurs')

@section('content')
<div class="animate-fade-in-up">

    {{-- En-tête --}}
    <div class="page-header mb-6">
        <div>
            <h1>Catalogue Fournisseurs</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Consultez les catalogues de vos fournisseurs partenaires.
            </p>
        </div>
    </div>

    @if ($fournisseurs->isEmpty())
        {{-- État vide --}}
        <div class="card" style="padding: 5rem 2rem; text-align: center;">
            <div style="width: 72px; height: 72px; background: linear-gradient(135deg, #f0fdfa, #ccfbf1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                <svg class="w-8 h-8" style="color: #0d9488;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p style="font-size: 1.0625rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Aucun fournisseur actif</p>
            <p style="font-size: 0.875rem; color: #64748b;">Les fournisseurs inscrits sur la plateforme apparaîtront ici.</p>
        </div>
    @else
        {{-- Grille des fournisseurs --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
            @foreach ($fournisseurs as $fournisseur)
                <a href="{{ route('pharmacien.catalogue.show', $fournisseur) }}"
                   style="display: block; text-decoration: none; transition: transform 0.2s, box-shadow 0.2s;"
                   onmouseover="this.style.transform='translateY(-3px)'; this.querySelector('.supplier-card').style.boxShadow='0 12px 32px rgba(15,118,110,0.18)'"
                   onmouseout="this.style.transform=''; this.querySelector('.supplier-card').style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                    <div class="card supplier-card" style="padding: 1.75rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                        {{-- Avatar initiales --}}
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #0f766e, #0d9488); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; box-shadow: 0 4px 12px rgba(15,118,110,0.3);">
                            <span style="font-size: 1.375rem; font-weight: 700; color: #fff;">
                                {{ strtoupper(substr($fournisseur->name, 0, 1)) }}
                            </span>
                        </div>

                        {{-- Nom --}}
                        <p style="font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 0.25rem; line-height: 1.3;">
                            {{ $fournisseur->name }}
                        </p>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 1.25rem;">
                            {{ $fournisseur->email }}
                        </p>

                        {{-- Badge nombre de produits --}}
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="display: inline-flex; align-items: center; gap: 0.4rem; background: #f0fdfa; color: #0f766e; border-radius: 99px; padding: 4px 12px; font-size: 0.8125rem; font-weight: 600;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                {{ $fournisseur->produits_count }} produit{{ $fournisseur->produits_count > 1 ? 's' : '' }}
                            </span>
                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.8125rem; font-weight: 500; color: #0d9488;">
                                Voir le catalogue
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($fournisseurs->hasPages())
            <div class="mt-6">
                {{ $fournisseurs->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
