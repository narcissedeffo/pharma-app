@extends('layouts.app')

@section('title', 'Activation du compte')

@section('content')
<div class="animate-fade-in-up mx-auto" style="max-width: 600px;">

    <div class="card p-8">
        {{-- Header --}}
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #0f766e, #0d9488); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 8px 24px rgba(15,118,110,0.35);">
                <svg class="w-8 h-8" style="color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">Activer votre compte</h1>
            <p style="font-size: 0.875rem; color: #64748b;">
                Bonjour <strong>{{ $user->name }}</strong>, choisissez un mot de passe pour finaliser la création de votre compte {{ $user->role->name }}.
            </p>
        </div>

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

        <form method="POST" action="{{ route('activate.store', ['token' => $token]) }}" class="space-y-5" x-data="activationForm()">
            @csrf

            <div>
                <label class="label" for="password">Nouveau mot de passe</label>
                <input id="password" type="password" name="password" required autofocus
                       class="input" placeholder="••••••••">
            </div>

            <div>
                <label class="label" for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="input" placeholder="••••••••">
            </div>

            @if($user->role->slug === 'pharmacien')
                <div class="divider my-6"></div>
                <h3 class="font-bold text-slate-800 mb-2">📍 Coordonnées de l'officine</h3>
                <p class="text-sm text-slate-500 mb-4">Afin que les patients puissent vous trouver, merci de renseigner votre adresse et de placer le curseur sur la carte.</p>
                
                <div>
                    <label class="label" for="address">Adresse complète de la pharmacie</label>
                    <input id="address" type="text" name="address" value="{{ old('address') }}" required
                           class="input" placeholder="Ex: 12 Rue de la Santé, 75001 Paris">
                </div>

                <div class="mt-4 relative">
                    <label class="label">Localisation sur la carte (Cliquez pour placer le marqueur)</label>
                    <div id="activation-map" style="height: 250px; width: 100%; border-radius: 0.5rem; border: 1px solid #e2e8f0; z-index: 10;"></div>
                    
                    <input type="hidden" name="latitude" x-model="lat" required>
                    <input type="hidden" name="longitude" x-model="lng" required>
                    
                    <p class="text-xs mt-2" :class="lat ? 'text-teal-600' : 'text-amber-600 font-semibold'">
                        <span x-show="!lat">⚠️ Veuillez cliquer sur la carte pour définir votre position.</span>
                        <span x-show="lat">✅ Position enregistrée : <span x-text="lat"></span>, <span x-text="lng"></span></span>
                    </p>
                </div>

                @push('styles')
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
                @endpush

                @push('scripts')
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
                    <script>
                        document.addEventListener('alpine:init', () => {
                            Alpine.data('activationForm', () => ({
                                lat: '{{ old('latitude') }}',
                                lng: '{{ old('longitude') }}',
                                map: null,
                                marker: null,
                                init() {
                                    // Centre par défaut (France)
                                    let center = [46.603354, 1.888334];
                                    let zoom = 5;

                                    if(this.lat && this.lng) {
                                        center = [this.lat, this.lng];
                                        zoom = 15;
                                    }

                                    this.map = L.map('activation-map').setView(center, zoom);
                                    
                                    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                                        attribution: '&copy; OpenStreetMap contributors',
                                        subdomains: 'abcd',
                                        maxZoom: 19
                                    }).addTo(this.map);

                                    if(this.lat && this.lng) {
                                        this.marker = L.marker(center).addTo(this.map);
                                    }

                                    this.map.on('click', (e) => {
                                        this.lat = e.latlng.lat.toFixed(6);
                                        this.lng = e.latlng.lng.toFixed(6);
                                        
                                        if (this.marker) {
                                            this.marker.setLatLng(e.latlng);
                                        } else {
                                            this.marker = L.marker(e.latlng).addTo(this.map);
                                        }
                                    });
                                }
                            }));
                        });
                    </script>
                @endpush
            @endif

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.875rem; font-size: 1rem; margin-top: 2rem;">
                Activer mon compte
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        </form>
    </div>
</div>
@endsection
