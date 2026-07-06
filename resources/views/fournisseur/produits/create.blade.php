@extends('layouts.app')

@section('title', 'Ajouter un produit')

@section('content')
<div class="animate-fade-in-up" style="max-width: 680px; margin: 0 auto;">

    {{-- Fil d'Ariane --}}
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #64748b; margin-bottom: 1.5rem;">
        <a href="{{ route('fournisseur.produits.index') }}" style="color: #0d9488; text-decoration: none; font-weight: 500;">Mon Catalogue</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        <span>Nouveau produit</span>
    </nav>

    {{-- Titre --}}
    <div class="page-header mb-6">
        <h1>Ajouter un produit</h1>
        <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
            Renseignez les informations du produit à ajouter à votre catalogue.
        </p>
    </div>

    {{-- Formulaire --}}
    <div class="card" style="padding: 2rem;">
        <form method="POST" action="{{ route('fournisseur.produits.store') }}" id="form-create-produit">
            @csrf

            <!-- CIP -->
            <div class="mb-5">
                <label for="cip" style="display: block; font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">
                    Code CIP <span style="font-weight: 400; color: #94a3b8;">(Optionnel)</span>
                </label>
                <input
                    type="text"
                    id="cip"
                    name="cip"
                    value="{{ old('cip') }}"
                    placeholder="Ex : 3400930001018"
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
                    value="{{ old('name') }}"
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
                >{{ old('description') }}</textarea>
                @error('description')
                    <p style="color: #dc2626; font-size: 0.8rem; margin-top: 0.35rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Prix et Date de péremption --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;" class="mb-5">
                {{-- Prix HT --}}
                <div>
                    <label for="price" style="display: block; font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">
                        Prix HT (XAF)
                    </label>
                    <div style="position: relative;">
                        <input
                            type="number"
                            id="price"
                            name="price"
                            value="{{ old('price') }}"
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
                        Date de péremption <span style="font-weight: 400; color: #94a3b8;">(Optionnel)</span>
                    </label>
                    <input
                        type="date"
                        id="date_peremption"
                        name="date_peremption"
                        value="{{ old('date_peremption') }}"
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
                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; padding: 1rem; border: 1.5px solid #e2e8f0; border-radius: 10px; transition: border-color 0.2s;"
                       id="label-disponible"
                       onmouseover="this.style.borderColor='#0d9488'"
                       onmouseout="this.style.borderColor=document.getElementById('is_available').checked ? '#0d9488' : '#e2e8f0'">
                    <input
                        type="checkbox"
                        id="is_available"
                        name="is_available"
                        value="1"
                        {{ old('is_available', '1') ? 'checked' : '' }}
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
                <button type="submit"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #0f766e, #0d9488); color: #fff; border: none; border-radius: 10px; padding: 11px 24px; font-size: 0.9rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 14px rgba(15,118,110,0.35); transition: all 0.2s;"
                        onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(15,118,110,0.45)'"
                        onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 14px rgba(15,118,110,0.35)'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter au catalogue
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
