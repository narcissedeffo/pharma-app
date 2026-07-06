@extends('layouts.app')

@section('title', 'Modifier ' . $produit->name)

@section('content')
<div class="animate-fade-in-up" style="max-width: 680px; margin: 0 auto;">

    {{-- Fil d'Ariane --}}
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #64748b; margin-bottom: 1.5rem;">
        <a href="{{ route('fournisseur.produits.index') }}" style="color: #0d9488; text-decoration: none; font-weight: 500;">Mon Catalogue</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        <span>Modifier</span>
    </nav>

    {{-- Titre --}}
    <div class="page-header mb-6">
        <h1>Modifier le produit</h1>
        <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
            Mettez à jour les informations de <strong>{{ $produit->name }}</strong>.
        </p>
    </div>

    {{-- Formulaire --}}
    <div class="card" style="padding: 2rem;">
        <form method="POST" action="{{ route('fournisseur.produits.update', $produit) }}" id="form-edit-produit">
            @csrf
            @method('PUT')

            {{-- CIP --}}
            <div class="mb-5">
                <label for="cip" style="display: block; font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">
                    Code CIP <span style="font-weight: 400; color: #94a3b8;">(optionnel)</span>
                </label>
                <input
                    type="text"
                    id="cip"
                    name="cip"
                    value="{{ old('cip', $produit->cip) }}"
                    placeholder="Ex: 3400930001018"
                    class="form-input w-full @error('cip') border-red-400 @enderror"
                    style="width: 100%;"
                >
                @error('cip')
                    <p style="color: #dc2626; font-size: 0.8rem; margin-top: 0.35rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nom --}}
            <div class="mb-5">
                <label for="name" style="display: block; font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">
                    Nom du produit <span style="color: #dc2626;">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $produit->name) }}"
                    placeholder="Ex : Amoxicilline 500mg, Pansements stériles…"
                    required
                    class="form-input w-full @error('name') border-red-400 @enderror"
                    style="width: 100%;"
                >
                @error('name')
                    <p style="color: #dc2626; font-size: 0.8rem; margin-top: 0.35rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-5">
                <label for="description" style="display: block; font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">
                    Description
                    <span style="font-weight: 400; color: #94a3b8;">(optionnel)</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Dosage, conditionnement, référence fabricant…"
                    class="form-input w-full @error('description') border-red-400 @enderror"
                    style="width: 100%; resize: vertical;"
                >{{ old('description', $produit->description) }}</textarea>
                @error('description')
                    <p style="color: #dc2626; font-size: 0.8rem; margin-top: 0.35rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Prix et Date de péremption --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                {{-- Prix --}}
                <div>
                    <label for="price" style="display: block; font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">
                        Prix HT (XAF)
                    </label>
                    <div style="position: relative;">
                        <input
                            type="number"
                            id="price"
                            name="price"
                            value="{{ old('price', $produit->price) }}"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                            class="form-input @error('price') border-red-400 @enderror"
                            style="width: 100%; padding-right: 3.5rem;"
                        >
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.875rem;">XAF</span>
                    </div>
                    @error('price')
                        <p style="color: #dc2626; font-size: 0.8rem; margin-top: 0.35rem;">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Date de péremption --}}
                <div>
                    <label for="date_peremption" style="display: block; font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">
                        Date de péremption <span style="font-weight: 400; color: #94a3b8;">(optionnel)</span>
                    </label>
                    <input
                        type="date"
                        id="date_peremption"
                        name="date_peremption"
                        value="{{ old('date_peremption', $produit->date_peremption ? $produit->date_peremption->format('Y-m-d') : '') }}"
                        class="form-input w-full @error('date_peremption') border-red-400 @enderror"
                        style="width: 100%;"
                    >
                    @error('date_peremption')
                        <p style="color: #dc2626; font-size: 0.8rem; margin-top: 0.35rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Disponibilité --}}
            <div class="mb-6">
                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; padding: 1rem; border: 1.5px solid {{ old('is_available', $produit->is_available) ? '#0d9488' : '#e2e8f0' }}; border-radius: 10px; transition: border-color 0.2s;"
                       id="label-disponible"
                       onmouseover="this.style.borderColor='#0d9488'"
                       onmouseout="this.style.borderColor=document.getElementById('is_available').checked ? '#0d9488' : '#e2e8f0'">
                    <input
                        type="checkbox"
                        id="is_available"
                        name="is_available"
                        value="1"
                        {{ old('is_available', $produit->is_available) ? 'checked' : '' }}
                        style="width: 18px; height: 18px; accent-color: #0d9488; cursor: pointer;"
                        onchange="this.closest('label').style.borderColor = this.checked ? '#0d9488' : '#e2e8f0'"
                    >
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 600; color: #1e293b; margin: 0;">Produit disponible en stock</p>
                        <p style="font-size: 0.8rem; color: #64748b; margin: 0.15rem 0 0;">Décochez si le produit est temporairement indisponible.</p>
                    </div>
                </label>
            </div>

            {{-- Boutons --}}
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('fournisseur.produits.index') }}"
                   style="font-size: 0.875rem; color: #64748b; text-decoration: none; font-weight: 500; transition: color 0.2s;"
                   onmouseover="this.style.color='#1e293b'" onmouseout="this.style.color='#64748b'">
                    ← Annuler
                </a>
                <div class="flex items-center gap-3">
                    {{-- Supprimer --}}
                    <form method="POST" action="{{ route('fournisseur.produits.destroy', $produit) }}"
                          onsubmit="return confirm('Supprimer définitivement ce produit ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                style="display: inline-flex; align-items: center; gap: 0.4rem; border: 1.5px solid #fca5a5; color: #dc2626; background: #fff; border-radius: 10px; padding: 10px 18px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.background='#fef2f2'"
                                onmouseout="this.style.background='#fff'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </button>
                    </form>

                    {{-- Sauvegarder --}}
                    <button type="submit" form="form-edit-produit"
                            style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #0f766e, #0d9488); color: #fff; border: none; border-radius: 10px; padding: 11px 24px; font-size: 0.9rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 14px rgba(15,118,110,0.35); transition: all 0.2s;"
                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(15,118,110,0.45)'"
                            onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 14px rgba(15,118,110,0.35)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
