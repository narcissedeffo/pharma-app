@extends('layouts.app')

@section('title', 'Importer un catalogue')

@section('content')
<div class="animate-fade-in-up" style="max-width: 700px; margin: 0 auto;">

    {{-- Fil d'Ariane --}}
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #64748b; margin-bottom: 1.5rem;">
        <a href="{{ route('fournisseur.produits.index') }}" style="color: #0d9488; text-decoration: none; font-weight: 500;">Mon Catalogue</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        <span>Importer</span>
    </nav>

    {{-- Titre --}}
    <div class="page-header mb-6">
        <h1>Importer un catalogue</h1>
        <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
            Ajoutez plusieurs produits en une seule fois depuis un fichier CSV ou PDF.
        </p>
    </div>

    {{-- Erreurs globales --}}
    @if ($errors->any())
        <div class="alert alert-error mb-5">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Erreurs d'import ligne par ligne --}}
    @if (session('import_errors') && count(session('import_errors')) > 0)
        <div class="card mb-5" style="border-left: 4px solid #f59e0b; padding: 1rem 1.25rem; background: #fffbeb;">
            <p style="font-size: 0.875rem; font-weight: 600; color: #92400e; margin-bottom: 0.5rem;">⚠️ Certaines lignes n'ont pas pu être importées :</p>
            <ul style="font-size: 0.8rem; color: #78350f; list-style: disc; padding-left: 1.25rem; margin: 0;">
                @foreach (session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulaire principal --}}
    <div class="card" style="padding: 2rem;">
        <form method="POST" action="{{ route('fournisseur.produits.import.store') }}"
              enctype="multipart/form-data"
              id="form-import"
              x-data="importForm()">
            @csrf

            {{-- Zone de dépôt --}}
            <div class="mb-6">
                <label for="fichier" style="display: block; font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.75rem;">
                    Fichier à importer <span style="color: #dc2626;">*</span>
                </label>

                <div
                    style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 2.5rem 1.5rem; text-align: center; cursor: pointer; transition: all 0.25s; background: #f8fafc;"
                    :style="dragging ? 'border-color: #0d9488; background: #f0fdfa;' : (fileName ? 'border-color: #0d9488; background: #f0fdfa;' : '')"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="onDrop($event)"
                    @click="$refs.fileInput.click()"
                    id="drop-zone">

                    <input type="file" name="fichier" id="fichier" accept=".csv,.txt,.pdf"
                           x-ref="fileInput" class="hidden"
                           @change="onFileChange($event)">

                    <div x-show="!fileName">
                        <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #f0fdfa, #ccfbf1); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <svg class="w-7 h-7" style="color: #0d9488;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <p style="font-size: 0.9375rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">
                            Glissez-déposez votre fichier ici
                        </p>
                        <p style="font-size: 0.8125rem; color: #94a3b8;">
                            ou <span style="color: #0d9488; font-weight: 600;">cliquez pour parcourir</span>
                        </p>
                        <p style="font-size: 0.75rem; color: #cbd5e1; margin-top: 0.75rem;">CSV ou PDF — 5 Mo maximum</p>
                    </div>

                    <div x-show="fileName" style="display: none;">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: #0d9488; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                            <svg class="w-6 h-6" style="color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p style="font-size: 0.9375rem; font-weight: 600; color: #0f766e;" x-text="fileName"></p>
                        <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">Cliquez pour changer de fichier</p>
                    </div>
                </div>
            </div>

            {{-- Mode d'import --}}
            <div class="mb-6">
                <p style="font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.75rem;">Mode d'import</p>
                <div class="grid grid-cols-2 gap-3">
                    <label style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 1rem; border: 1.5px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.2s;"
                           :style="mode === 'ajouter' ? 'border-color: #0d9488; background: #f0fdfa;' : ''"
                           id="label-ajouter">
                        <input type="radio" name="mode_import" value="ajouter" x-model="mode"
                               style="width: 16px; height: 16px; accent-color: #0d9488; margin-top: 2px; flex-shrink: 0;">
                        <div>
                            <p style="font-size: 0.875rem; font-weight: 600; color: #1e293b; margin: 0;">Ajouter</p>
                            <p style="font-size: 0.775rem; color: #64748b; margin: 0.2rem 0 0;">Les produits existants sont conservés. Les nouveaux sont ajoutés.</p>
                        </div>
                    </label>
                    <label style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 1rem; border: 1.5px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.2s;"
                           :style="mode === 'remplacer' ? 'border-color: #dc2626; background: #fef2f2;' : ''"
                           id="label-remplacer">
                        <input type="radio" name="mode_import" value="remplacer" x-model="mode"
                               style="width: 16px; height: 16px; accent-color: #dc2626; margin-top: 2px; flex-shrink: 0;">
                        <div>
                            <p style="font-size: 0.875rem; font-weight: 600; color: #1e293b; margin: 0;">Remplacer</p>
                            <p style="font-size: 0.775rem; color: #64748b; margin: 0.2rem 0 0;">⚠️ Tous vos produits existants seront supprimés avant l'import.</p>
                        </div>
                    </label>
                </div>

                {{-- Alerte remplacement --}}
                <div x-show="mode === 'remplacer'" x-transition style="display: none; margin-top: 0.75rem; background: #fef2f2; border: 1px solid #fca5a5; border-radius: 8px; padding: 0.75rem 1rem;">
                    <p style="font-size: 0.8125rem; color: #b91c1c; font-weight: 500;">
                        🚨 Attention : cette action supprimera <strong>définitivement</strong> tous vos produits actuels avant d'importer le fichier. Elle est irréversible.
                    </p>
                </div>
            </div>

            {{-- Boutons --}}
            <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 1.25rem; border-top: 1px solid #f1f5f9;">
                <a href="{{ route('fournisseur.produits.index') }}"
                   style="font-size: 0.875rem; color: #64748b; text-decoration: none; font-weight: 500;"
                   onmouseover="this.style.color='#1e293b'" onmouseout="this.style.color='#64748b'">
                    ← Annuler
                </a>
                <button type="submit" :disabled="!fileName"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #0f766e, #0d9488); color: #fff; border: none; border-radius: 10px; padding: 11px 24px; font-size: 0.9rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 14px rgba(15,118,110,0.35); transition: all 0.2s; opacity: 1;"
                        :style="!fileName ? 'opacity: 0.45; cursor: not-allowed;' : ''"
                        onmouseover="if(this.style.opacity==='1')this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.transform=''">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Importer le catalogue
                </button>
            </div>
        </form>
    </div>

    {{-- Aide : format CSV --}}
    <div class="card mt-6" style="padding: 1.5rem; background: #f8fafc;">
        <div style="display: flex; align-items: flex-start; gap: 1rem;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #e0f2fe; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg class="w-5 h-5" style="color: #0284c7;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p style="font-size: 0.875rem; font-weight: 600; color: #0c4a6e; margin-bottom: 0.5rem;">Format CSV attendu</p>
                <p style="font-size: 0.8125rem; color: #475569; margin-bottom: 0.75rem;">
                    La première ligne doit être l'en-tête. Les colonnes <code style="background:#e2e8f0; padding: 1px 5px; border-radius: 4px; font-size:0.75rem;">cip</code>, <code style="background:#e2e8f0; padding: 1px 5px; border-radius: 4px; font-size:0.75rem;">nom</code>, <code style="background:#e2e8f0; padding: 1px 5px; border-radius: 4px; font-size:0.75rem;">description</code>, <code style="background:#e2e8f0; padding: 1px 5px; border-radius: 4px; font-size:0.75rem;">prix_ht</code>, <code style="background:#e2e8f0; padding: 1px 5px; border-radius: 4px; font-size:0.75rem;">date_peremption</code> et <code style="background:#e2e8f0; padding: 1px 5px; border-radius: 4px; font-size:0.75rem;">is_available</code> sont reconnues. Séparateur virgule (,) ou point-virgule (;).
                </p>
                <div style="background: #1e293b; border-radius: 8px; padding: 0.875rem 1rem; font-family: monospace; font-size: 0.78rem; color: #94a3b8; overflow-x: auto; white-space: pre;">cip;nom;description;prix_ht;date_peremption;is_available
3400930001018;Amoxicilline 500mg;Boite 30 gelules;12.50;2028-12-31;1
;Paracetamol 1g;;3.80;;1
;Seringues 5ml;Lot de 100;8.00;;0</div>
                <a href="{{ asset('templates/modele_import_produits.csv') }}"
                   download
                   style="display: inline-flex; align-items: center; gap: 0.4rem; margin-top: 0.875rem; font-size: 0.8125rem; font-weight: 600; color: #0d9488; text-decoration: none;"
                   onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger le modèle CSV
                </a>
            </div>
        </div>

        {{-- Section PDF --}}
        <div style="margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid #e2e8f0;">
            <p style="font-size: 0.875rem; font-weight: 600; color: #0c4a6e; margin-bottom: 0.35rem;">Import PDF</p>
            <p style="font-size: 0.8125rem; color: #64748b;">
                Pour les PDF, chaque ligne doit correspondre à un produit, avec les champs séparés par <code style="background:#e2e8f0; padding: 1px 5px; border-radius: 4px; font-size:0.75rem;">;</code> ou <code style="background:#e2e8f0; padding: 1px 5px; border-radius: 4px; font-size:0.75rem;">,</code> :<br>
                <span style="font-family: monospace; font-size: 0.78rem; color: #475569;">Nom du produit ; Prix ; Description</span><br>
                <span style="font-size: 0.775rem; color: #94a3b8;">⚠️ Les PDF scannés (image) ne peuvent pas être lus. Utilisez de préférence le format CSV.</span>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function importForm() {
    return {
        fileName: '',
        dragging: false,
        mode: 'ajouter',

        onFileChange(event) {
            const file = event.target.files[0];
            if (file) this.fileName = file.name;
        },

        onDrop(event) {
            this.dragging = false;
            const file = event.dataTransfer.files[0];
            if (!file) return;

            // Vérifie l'extension
            const ext = file.name.split('.').pop().toLowerCase();
            if (!['csv', 'txt', 'pdf'].includes(ext)) {
                alert('Format non supporté. Veuillez utiliser un fichier CSV ou PDF.');
                return;
            }

            // Assigne le fichier au champ input
            const dt = new DataTransfer();
            dt.items.add(file);
            this.$refs.fileInput.files = dt.files;
            this.fileName = file.name;
        }
    }
}
</script>
@endpush
@endsection
