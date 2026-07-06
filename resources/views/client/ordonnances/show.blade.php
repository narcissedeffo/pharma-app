@extends('layouts.app')

@section('title', 'Détail ordonnance')

@section('content')
<div class="animate-fade-in-up mx-auto" style="max-width: 800px;">

    {{-- Back --}}
    <a href="{{ route('client.ordonnances.index') }}" class="btn btn-ghost mb-6" style="width: fit-content;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour à mes ordonnances
    </a>

    {{-- Header --}}
    <div class="card p-6 mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4">
                <div style="width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
                    background: {{ in_array($ordonnance->mime_type, ['image/jpeg','image/png']) ? 'linear-gradient(135deg,#ede9fe,#ddd6fe)' : 'linear-gradient(135deg,#fee2e2,#fecaca)' }};">
                    @if (in_array($ordonnance->mime_type, ['image/jpeg','image/png']))
                        <svg class="w-6 h-6" style="color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6" style="color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 4px;">
                        {{ $ordonnance->original_filename }}
                    </h1>
                    <p style="font-size: 0.8125rem; color: #64748b;">
                        Déposée le {{ $ordonnance->created_at->format('d/m/Y à H:i') }}
                        · {{ round($ordonnance->file_size / 1024) }} Ko
                    </p>
                </div>
            </div>
            
            <div class="flex flex-col items-end gap-2">
                <span class="badge badge-{{ $ordonnance->status }}" style="flex-shrink: 0;">
                    <span class="badge-dot"></span>
                    {{ $ordonnance->statusLabel() }}
                </span>
                
                @if($ordonnance->isExpiringSoon() && $ordonnance->status === 'brouillon')
                    <span class="badge" style="background: #fffbeb; color: #b45309; border: 1px solid #fef3c7;">
                        ⚠️ Expire dans {{ $ordonnance->daysUntilExpiry() }} jour(s)
                    </span>
                @elseif($ordonnance->status === 'expiree')
                    <span class="badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;">
                        Expirée le {{ $ordonnance->expires_at->format('d/m/Y') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="divider"></div>

        {{-- Infos pharmacien --}}
        @if ($ordonnance->pharmacien)
            <div class="flex items-center gap-3" style="margin-bottom: 1rem;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #ccfbf1, #99f6e4); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #0f766e; font-size: 0.875rem;">
                    {{ strtoupper(substr($ordonnance->pharmacien->name, 0, 1)) }}
                </div>
                <div>
                    <p style="font-size: 0.8125rem; font-weight: 600; color: #0f172a;">{{ $ordonnance->pharmacien->name }}</p>
                    <p style="font-size: 0.75rem; color: #64748b;">Pharmacie assignée</p>
                </div>
            </div>
        @endif

        @if ($ordonnance->note_pharmacien)
            <div class="alert alert-info" style="margin-bottom: 1rem;">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <div>
                    <p style="font-weight: 600; margin-bottom: 2px;">Note du pharmacien :</p>
                    <p>{{ $ordonnance->note_pharmacien }}</p>
                </div>
            </div>
        @endif

        {{-- Médicaments (Items) --}}
        @if($ordonnance->items->count() > 0)
            <div class="mt-4 mb-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Disponibilité des médicaments</h3>
                <ul class="space-y-2">
                    @foreach($ordonnance->items as $item)
                        <li class="flex items-start justify-between p-3 rounded-lg border border-slate-100 bg-slate-50">
                            <div>
                                <span class="font-medium text-slate-800 text-sm">{{ $item->nom_medicament }}</span>
                                @if($item->commentaire)
                                    <p class="text-xs text-slate-500 mt-1 italic">{{ $item->commentaire }}</p>
                                @endif
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $item->statutColor() }}">
                                {{ $item->statutIcon() }} {{ $item->statutLabel() }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        {{-- Créneau de retrait --}}
        @if($ordonnance->pickupSlot)
            <div class="mt-4 mb-4 p-4 rounded-xl border border-teal-100 bg-teal-50" style="border-left: 4px solid #0d9488;">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <h3 class="text-sm font-bold text-teal-900 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Créneau de retrait
                        </h3>
                        <p class="text-sm text-teal-800">
                            Votre pharmacien propose un retrait le :<br>
                            <strong class="text-base">{{ $ordonnance->pickupSlot->proposed_at->translatedFormat('l d F Y à H\hi') }}</strong>
                        </p>
                    </div>
                    
                    @if(!$ordonnance->pickupSlot->isConfirmed())
                        <form method="POST" action="{{ route('client.ordonnances.confirm_pickup', $ordonnance) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="background: #0f766e;">
                                Confirmer ce créneau
                            </button>
                        </form>
                    @else
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-800 rounded-lg text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Créneau confirmé
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Réaffecter si refusée --}}
        @if($ordonnance->status === 'refusee')
            <div class="mt-4 flex">
                <form method="POST" action="{{ route('client.ordonnances.reaffecter', $ordonnance) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Renvoyer vers une autre pharmacie
                    </button>
                </form>
            </div>
        @endif
        
    </div>

    {{-- Aperçu Document --}}
    <div class="mb-6">
        <h2 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">📄 Document</h2>
        <x-ordonnance-viewer :ordonnance="$ordonnance" />
    </div>
    
    {{-- Notation (Évaluation) --}}
    @if($ordonnance->canBeRated())
        <div class="card p-6 mb-6 text-center bg-gradient-to-br from-indigo-50 to-white" x-data="{ rating: 0, hoverRating: 0 }">
            <h2 class="text-lg font-bold text-slate-800 mb-2">Évaluez votre expérience</h2>
            <p class="text-sm text-slate-500 mb-6">Comment s'est passé votre retrait à la pharmacie {{ $ordonnance->pharmacien->name }} ?</p>
            
            <form method="POST" action="{{ route('client.ordonnances.rate', $ordonnance) }}" class="max-w-md mx-auto">
                @csrf
                <input type="hidden" name="rating" x-model="rating" required>
                
                <div class="flex justify-center gap-2 mb-6">
                    <template x-for="i in 5">
                        <button type="button" 
                                @click="rating = i" 
                                @mouseenter="hoverRating = i" 
                                @mouseleave="hoverRating = 0"
                                class="focus:outline-none transition-transform hover:scale-110">
                            <svg class="w-10 h-10 transition-colors" 
                                 :class="(hoverRating >= i || (!hoverRating && rating >= i)) ? 'text-yellow-400' : 'text-slate-200'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                    </template>
                </div>
                
                <div x-show="rating > 0" x-transition.opacity>
                    <textarea name="rating_comment" rows="2" class="input mb-4" placeholder="Un commentaire (optionnel) ?"></textarea>
                    <button type="submit" class="btn btn-primary w-full justify-center">Envoyer mon avis</button>
                </div>
            </form>
        </div>
    @elseif($ordonnance->isRated())
        <div class="card p-6 mb-6 bg-slate-50 flex items-start gap-4">
            <div class="bg-yellow-100 p-3 rounded-full text-yellow-600">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Vous avez noté cette expérience : {{ $ordonnance->rating }}/5</h3>
                @if($ordonnance->rating_comment)
                    <p class="text-sm text-slate-600 mt-1 italic">"{{ $ordonnance->rating_comment }}"</p>
                @endif
                <p class="text-xs text-slate-400 mt-2">Le {{ $ordonnance->rated_at->format('d/m/Y') }}</p>
            </div>
        </div>
    @endif

    {{-- Publication --}}
    @if ($ordonnance->status === 'brouillon')
        @push('styles')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
            <style>
                #pharmacy-map { height: 300px; width: 100%; border-radius: 0.5rem; z-index: 10; margin-bottom: 1rem; border: 1px solid #e2e8f0; }
                .leaflet-popup-content-wrapper { border-radius: 0.5rem; }
                .leaflet-popup-content { margin: 12px 16px; line-height: 1.4; }
                .leaflet-popup-content h3 { font-weight: 700; color: #0f172a; margin-bottom: 4px; font-size: 14px; }
                .leaflet-popup-content p { color: #64748b; margin-bottom: 8px; font-size: 12px; }
                .leaflet-popup-content button { background: #0f766e; color: white; border-radius: 4px; padding: 4px 8px; font-size: 12px; width: 100%; cursor: pointer; border: none; }
                .leaflet-popup-content button:hover { background: #0d9488; }
            </style>
        @endpush

        <div class="card p-6 mb-6" style="border-left: 4px solid #0f766e;" x-data="pharmacyMap()">
            <h2 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">📍 Choisir une pharmacie</h2>
            <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 1.25rem;">
                Sélectionnez la pharmacie la plus proche sur la carte pour lui envoyer votre ordonnance.
            </p>

            @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div id="pharmacy-map"></div>

            <form method="POST" action="{{ route('client.ordonnances.publish', $ordonnance) }}" class="flex gap-3 flex-wrap items-end bg-slate-50 p-4 rounded-lg border border-slate-200">
                @csrf
                <div style="flex: 1; min-width: 200px;">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Pharmacie sélectionnée :</label>
                    <select id="pharmacien_select" name="pharmacien_id" required class="input bg-white w-full" x-model="selectedId">
                        <option value="">— Veuillez sélectionner sur la carte —</option>
                        @foreach ($pharmaciens as $pharmacien)
                            <option value="{{ $pharmacien->id }}">{{ $pharmacien->name }} {{ $pharmacien->address ? ' - '.$pharmacien->address : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary h-11" :disabled="!selectedId">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Envoyer l'ordonnance
                </button>
            </form>
        </div>

        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('pharmacyMap', () => ({
                        selectedId: '',
                        init() {
                            // Initialisation de la carte (Centrée sur la France par défaut, ajustez si besoin)
                            const map = L.map('pharmacy-map').setView([46.603354, 1.888334], 5);
                            
                            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                                subdomains: 'abcd',
                                maxZoom: 19
                            }).addTo(map);

                            // Icône personnalisée pour les pharmacies
                            const pharmacyIcon = L.divIcon({
                                className: 'custom-div-icon',
                                html: `<div style="background-color: #0f766e; width: 30px; height: 30px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(0,0,0,0.3); border: 2px solid white;">
                                         <span style="transform: rotate(45deg); color: white; font-weight: bold; font-size: 16px;">+</span>
                                       </div>`,
                                iconSize: [30, 30],
                                iconAnchor: [15, 30],
                                popupAnchor: [0, -30]
                            });

                            // Récupération des pharmacies depuis l'API
                            fetch("{{ route('api.pharmacies') }}")
                                .then(response => response.json())
                                .then(data => {
                                    if(data.length > 0) {
                                        const bounds = [];
                                        data.forEach(pharmacy => {
                                            if (pharmacy.latitude && pharmacy.longitude) {
                                                const marker = L.marker([pharmacy.latitude, pharmacy.longitude], {icon: pharmacyIcon}).addTo(map);
                                                
                                                // Contenu de la popup
                                                const popupContent = `
                                                    <div>
                                                        <h3>${pharmacy.name}</h3>
                                                        <p>${pharmacy.address || 'Adresse non renseignée'}</p>
                                                        <button onclick="document.querySelector('#pharmacien_select').value = '${pharmacy.id}'; document.querySelector('#pharmacien_select').dispatchEvent(new Event('change'));">
                                                            Sélectionner
                                                        </button>
                                                    </div>
                                                `;
                                                marker.bindPopup(popupContent);
                                                bounds.push([pharmacy.latitude, pharmacy.longitude]);
                                            }
                                        });

                                        // Centrer la carte sur les points
                                        if (bounds.length > 0) {
                                            map.fitBounds(bounds, { padding: [50, 50] });
                                        }
                                    }
                                })
                                .catch(err => console.error("Erreur lors du chargement des pharmacies:", err));
                        }
                    }))
                })
            </script>
        @endpush
    @endif

    {{-- Historique (timeline) --}}
    <div class="card p-6 mt-6">
        <h2 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1.25rem;">
            📋 Historique des statuts
        </h2>

        @if ($ordonnance->histories->isEmpty())
            <p style="font-size: 0.875rem; color: #94a3b8;">Aucun historique pour le moment.</p>
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
                                 'expiree'    => '#64748b',
                                 default      => '#0f766e',
                             } }};"></div>

                        <div style="padding-left: 0.5rem;">
                            <div class="flex items-center gap-2 flex-wrap" style="margin-bottom: 4px;">
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #0f172a;">{{ $history->user->name }}</span>
                                <span style="font-size: 0.75rem; color: #94a3b8;">{{ $history->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            @if ($history->from_status)
                                <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 2px;">
                                    <span style="color: #94a3b8;">{{ $history->from_status }}</span>
                                    →
                                    <span style="font-weight: 600; color: #0f172a;">{{ $history->to_status }}</span>
                                </p>
                            @else
                                <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 2px;">
                                    Statut initial : <span style="font-weight: 600;">{{ $history->to_status }}</span>
                                </p>
                            @endif
                            @if ($history->comment)
                                <p style="font-size: 0.8125rem; color: #475569; font-style: italic;">
                                    "{{ $history->comment }}"
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Chat --}}
    @if (!in_array($ordonnance->status, ['brouillon', 'expiree']) && $ordonnance->pharmacien_id)
        <x-chat-box :ordonnance="$ordonnance" :current-user="auth()->user()" />
    @endif

</div>
@endsection
