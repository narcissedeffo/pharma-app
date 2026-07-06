@extends('layouts.app')

@section('title', 'Traiter l\'ordonnance')

@section('content')
<div class="animate-fade-in-up mx-auto" style="max-width: 900px;">

    {{-- Back --}}
    <a href="{{ route('pharmacien.ordonnances.index') }}" class="btn btn-ghost mb-6" style="width: fit-content;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour aux ordonnances
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Colonne gauche: Infos et actions --}}
        <div>
            {{-- Header --}}
            <div class="card p-6 mb-6">
                <div class="flex items-start justify-between gap-4 flex-wrap mb-4">
                    <div class="flex items-center gap-4">
                        <div style="width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, #dbeafe, #bfdbfe); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; color: #1e40af; flex-shrink: 0;">
                            {{ strtoupper(substr($ordonnance->client->name, 0, 1)) }}
                        </div>
                        <div>
                            <h1 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 2px;">
                                {{ $ordonnance->client->name }}
                            </h1>
                            <p style="font-size: 0.8125rem; color: #64748b;">{{ $ordonnance->client->email }}</p>
                        </div>
                    </div>
                    <span class="badge badge-{{ $ordonnance->status }}" style="flex-shrink: 0;">
                        <span class="badge-dot"></span>
                        {{ $ordonnance->statusLabel() }}
                    </span>
                </div>

                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 flex items-center justify-between">
                    <div>
                        <p style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; text-transform: uppercase;">Fichier</p>
                        <p style="font-size: 0.875rem; font-weight: 500; color: #0f172a;" class="truncate w-48" title="{{ $ordonnance->original_filename }}">{{ $ordonnance->original_filename }}</p>
                    </div>
                    <p style="font-size: 0.75rem; color: #64748b; text-align: right;">
                        {{ round($ordonnance->file_size / 1024) }} Ko<br>
                        {{ $ordonnance->published_at?->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>

            {{-- Actions selon statut --}}
            @if ($ordonnance->status === 'en_attente')
                <div class="card p-6 mb-6" style="border-left: 4px solid #eab308;">
                    <h2 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">
                        ⏳ Ordonnance en attente
                    </h2>
                    <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 1.25rem;">
                        Prenez en charge cette ordonnance pour commencer son traitement.
                    </p>
                    <form method="POST" action="{{ route('pharmacien.ordonnances.take', $ordonnance) }}">
                        @csrf
                        <button type="submit" class="btn btn-blue w-full justify-center">
                            Prendre en charge
                        </button>
                    </form>
                </div>
            @endif

            @if ($ordonnance->status === 'en_cours')
                <div class="card p-6 mb-6" style="border-left: 4px solid #3b82f6;" x-data="medicationList()">
                    <h2 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">
                        🔵 Rendre votre décision
                    </h2>
                    <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 1.25rem;">
                        Renseignez la disponibilité des médicaments puis validez ou refusez l'ordonnance.
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-error mb-4">
                            <ul class="list-disc pl-4 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pharmacien.ordonnances.decide', $ordonnance) }}" class="space-y-4">
                        @csrf
                        
                        {{-- Gestion des médicaments dynamiques avec Alpine.js --}}
                        <div class="bg-white border border-slate-200 rounded-lg p-4 mb-4">
                            <h3 class="text-sm font-bold text-slate-700 mb-3">Liste des médicaments</h3>
                            
                            <template x-for="(item, index) in items" :key="index">
                                <div class="p-3 bg-slate-50 border border-slate-100 rounded-md mb-3 relative">
                                    <button type="button" @click="removeItem(index)" class="absolute top-2 right-2 text-slate-400 hover:text-red-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-2 pr-6">
                                        <div>
                                            <input type="text" :name="`items[${index}][nom]`" x-model="item.nom" placeholder="Nom du médicament" class="input w-full py-1.5 px-2 text-sm" required>
                                        </div>
                                        <div>
                                            <select :name="`items[${index}][statut]`" x-model="item.statut" class="input w-full py-1.5 px-2 text-sm" required>
                                                <option value="disponible">✅ Disponible</option>
                                                <option value="a_commander">⏳ À commander</option>
                                                <option value="indisponible">❌ Indisponible</option>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="text" :name="`items[${index}][commentaire]`" x-model="item.commentaire" placeholder="Commentaire optionnel (ex: dispo demain 14h)" class="input w-full py-1.5 px-2 text-xs">
                                </div>
                            </template>
                            
                            <button type="button" @click="addItem()" class="text-sm text-teal-600 font-medium hover:text-teal-800 flex items-center gap-1 mt-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Ajouter un médicament
                            </button>
                        </div>

                        <div>
                            <label class="label" for="note_pharmacien">Note globale pour le patient</label>
                            <textarea id="note_pharmacien" name="note_pharmacien" rows="2"
                                      class="input" placeholder="Ex : Ordonnance prête..."
                                      style="resize: vertical;">{{ old('note_pharmacien') }}</textarea>
                        </div>

                        <div class="flex gap-3 flex-wrap">
                            <button type="submit" name="decision" value="validee" class="btn btn-success flex-1 justify-center">
                                Valider
                            </button>
                            <button type="submit" name="decision" value="refusee" class="btn btn-danger flex-1 justify-center">
                                Refuser
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if ($ordonnance->status === 'validee')
                <div class="card p-6 mb-6 border-t-4 border-t-green-500">
                    <h2 class="text-lg font-bold text-slate-800 mb-2">Ordonnance validée</h2>
                    
                    {{-- Proposer un créneau --}}
                    @if(!$ordonnance->pickupSlot)
                        <div class="mt-4 p-4 bg-teal-50 rounded-lg border border-teal-100">
                            <h3 class="text-sm font-bold text-teal-900 mb-2">Proposer un créneau de retrait</h3>
                            <form method="POST" action="{{ route('pharmacien.ordonnances.pickup.propose', $ordonnance) }}" class="flex gap-2 items-end flex-wrap">
                                @csrf
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-xs font-medium text-teal-800 mb-1">Date et heure</label>
                                    <input type="datetime-local" name="proposed_at" class="input w-full py-2" required>
                                </div>
                                <button type="submit" class="btn btn-primary bg-teal-700">Proposer</button>
                            </form>
                        </div>
                    @else
                        <div class="mt-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
                            <h3 class="text-sm font-bold text-slate-700 mb-1">Créneau de retrait</h3>
                            <p class="text-slate-800 font-medium">{{ $ordonnance->pickupSlot->proposed_at->translatedFormat('l d F Y à H\hi') }}</p>
                            
                            @if($ordonnance->pickupSlot->isConfirmed())
                                <span class="inline-block mt-2 px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded">Confirmé par le patient</span>
                            @else
                                <span class="inline-block mt-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded">En attente de confirmation</span>
                            @endif
                        </div>
                    @endif
                    
                    {{-- Marquer comme retirée --}}
                    <div class="mt-6 border-t border-slate-100 pt-4">
                        <p class="text-sm text-slate-500 mb-3">Le patient a-t-il récupéré ses médicaments ?</p>
                        <form method="POST" action="{{ route('pharmacien.ordonnances.picked_up', $ordonnance) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-full justify-center">
                                Confirmer le retrait en pharmacie
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            
            @if($ordonnance->status === 'retiree')
                <div class="alert alert-success mb-6">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Cette ordonnance a été <strong>retirée</strong> par le patient.</span>
                </div>
            @endif

            {{-- Historique --}}
            <div class="card p-6 mb-6">
                <h2 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1.25rem;">
                    📋 Historique des statuts
                </h2>
                @if ($ordonnance->histories->isEmpty())
                    <p style="font-size: 0.875rem; color: #94a3b8;">Aucun historique.</p>
                @else
                    <div class="timeline">
                        @foreach ($ordonnance->histories as $history)
                            <div class="timeline-item">
                                <div class="timeline-dot"
                                     style="background: {{ match($history->to_status) {
                                         'brouillon'  => '#94a3b8',
                                         'en_attente' => '#eab308',
                                         'en_cours'   => '#3b82f6',
                                         'validee'    => '#22c55e',
                                         'refusee'    => '#ef4444',
                                         'retiree'    => '#0d9488',
                                         default      => '#0f766e',
                                     } }};"></div>
                                <div style="padding-left: 0.5rem;">
                                    <div class="flex items-center gap-2 flex-wrap" style="margin-bottom: 4px;">
                                        <span style="font-size: 0.8125rem; font-weight: 600; color: #0f172a;">{{ $history->user->name }}</span>
                                        <span style="font-size: 0.75rem; color: #94a3b8;">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if ($history->from_status)
                                        <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 2px;">
                                            <span style="color: #94a3b8;">{{ $history->from_status }}</span> → <span style="font-weight: 600; color: #0f172a;">{{ $history->to_status }}</span>
                                        </p>
                                    @else
                                        <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 2px;">
                                            Statut initial : <span style="font-weight: 600;">{{ $history->to_status }}</span>
                                        </p>
                                    @endif
                                    @if ($history->comment)
                                        <p style="font-size: 0.8125rem; color: #475569; font-style: italic;">"{{ $history->comment }}"</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            {{-- Chat --}}
            @if (!in_array($ordonnance->status, ['brouillon', 'expiree']))
                <x-chat-box :ordonnance="$ordonnance" :current-user="auth()->user()" />
            @endif
        </div>
        
        {{-- Colonne droite: Aperçu Document --}}
        <div>
            <div class="sticky top-6">
                <h2 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">📄 Document</h2>
                <x-ordonnance-viewer :ordonnance="$ordonnance" />
                
                @if($ordonnance->items->count() > 0 && in_array($ordonnance->status, ['validee', 'retiree']))
                    <div class="card p-4 mt-6">
                        <h3 class="font-bold text-slate-800 mb-3 text-sm">Médicaments renseignés</h3>
                        <ul class="space-y-2">
                            @foreach($ordonnance->items as $item)
                                <li class="text-sm flex justify-between items-center pb-2 border-b border-slate-100 last:border-0">
                                    <span>{{ $item->nom_medicament }}</span>
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs {{ $item->statutColor() }}">
                                        {{ $item->statutLabel() }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('medicationList', () => ({
            items: [{ nom: '', statut: 'disponible', commentaire: '' }],
            addItem() {
                this.items.push({ nom: '', statut: 'disponible', commentaire: '' });
            },
            removeItem(index) {
                this.items.splice(index, 1);
                if (this.items.length === 0) {
                    this.addItem(); // always keep at least one
                }
            }
        }));
    });
</script>
@endpush
@endsection
