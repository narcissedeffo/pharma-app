@extends('layouts.app')

@section('title', 'Nouvelle Commande Fournisseur')

@section('content')
<div class="animate-fade-in-up max-w-2xl mx-auto">
    <div class="page-header mb-6">
        <div>
            <a href="{{ route('pharmacien.commandes.index') }}" class="text-sm text-teal-600 hover:text-teal-800 mb-2 inline-block">&larr; Retour aux commandes</a>
            <h1>Créer une nouvelle commande</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Sélectionnez un fournisseur pour démarrer un nouveau brouillon de commande.
            </p>
        </div>
    </div>

    <div class="card p-6">
        <form method="POST" action="{{ route('pharmacien.commandes.store') }}">
            @csrf
            
            @if ($errors->any())
                <div class="alert alert-error mb-6">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-5">
                <label class="form-label block mb-2 text-sm font-medium text-gray-700">Fournisseur</label>
                <select name="fournisseur_id" class="input w-full" required>
                    <option value="">-- Sélectionner un fournisseur --</option>
                    @foreach($fournisseurs as $fournisseur)
                        <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                            {{ $fournisseur->name }}
                        </option>
                    @endforeach
                </select>
                @error('fournisseur_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="form-label block mb-2 text-sm font-medium text-gray-700">Notes internes (optionnel)</label>
                <textarea name="notes" class="input w-full" rows="4" placeholder="Informations complémentaires, urgence...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('pharmacien.commandes.index') }}" class="btn btn-ghost">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    Créer le brouillon
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
