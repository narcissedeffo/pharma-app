@extends('layouts.app')

@section('title', 'Déposer une ordonnance')

@section('content')
<div class="animate-fade-in-up mx-auto" style="max-width: 560px;">

    {{-- Back --}}
    <a href="{{ route('client.ordonnances.index') }}" class="btn btn-ghost mb-6" style="width: fit-content;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour à mes ordonnances
    </a>

    <div class="card p-8">
        {{-- Header --}}
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #ccfbf1, #99f6e4); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <svg class="w-7 h-7" style="color: #0f766e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">Déposer une ordonnance</h1>
            <p style="font-size: 0.875rem; color: #64748b;">Formats acceptés : PDF, JPG, PNG — 10 Mo maximum</p>
        </div>

        {{-- Erreurs --}}
        @if ($errors->any())
            <div class="alert alert-error mb-6">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('client.ordonnances.store') }}"
              enctype="multipart/form-data"
              x-data="{
                  fileName: '',
                  isDragging: false,
                  handleFile(e) {
                      const f = e.dataTransfer ? e.dataTransfer.files[0] : e.target.files[0];
                      if (f) this.fileName = f.name;
                  }
              }">
            @csrf

            {{-- Zone drag & drop --}}
            <div class="upload-zone mb-6"
                 :class="{ 'drag-over': isDragging }"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="isDragging = false; handleFile($event)">

                <input type="file" name="fichier" accept=".pdf,.jpg,.jpeg,.png" required
                       @change="handleFile($event)" id="fichier">

                <div x-show="!fileName">
                    <div style="margin-bottom: 1rem;">
                        <svg class="w-12 h-12 mx-auto" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <p style="font-weight: 600; color: #475569; margin-bottom: 0.25rem;">
                        Glissez votre fichier ici
                    </p>
                    <p style="font-size: 0.8125rem; color: #94a3b8;">ou cliquez pour sélectionner</p>
                    <p style="font-size: 0.75rem; color: #cbd5e1; margin-top: 0.75rem;">PDF · JPG · PNG · max 10 Mo</p>
                </div>

                <div x-show="fileName" x-cloak>
                    <div style="margin-bottom: 0.75rem;">
                        <svg class="w-10 h-10 mx-auto" style="color: #0f766e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p style="font-weight: 600; color: #0f766e;" x-text="fileName"></p>
                    <p style="font-size: 0.8125rem; color: #94a3b8; margin-top: 0.25rem;">Fichier sélectionné ✓</p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full" style="width: 100%; justify-content: center; padding: 0.875rem;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Déposer l'ordonnance
            </button>
        </form>
    </div>

    {{-- Info --}}
    <div class="alert alert-info mt-4">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Une fois déposée, vous pourrez choisir la pharmacie à laquelle envoyer votre ordonnance.</span>
    </div>
</div>
@endsection
