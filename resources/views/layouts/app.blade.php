<!DOCTYPE html>
<html lang="fr" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0f766e">
    <meta name="description" content="Pharma App — Gestion des ordonnances en ligne">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pharma App') — PharmaApp</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen" style="background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 50%, #f0f9ff 100%); min-height: 100vh;">

    <!-- ═══════════════════ NAVBAR ═══════════════════ -->
    <header style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); box-shadow: 0 2px 20px rgba(15,118,110,0.4);">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8">

            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 text-white no-underline">
                <div style="background: rgba(255,255,255,0.2); border-radius: 10px; padding: 6px; backdrop-filter: blur(8px);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <span style="font-size: 1.1rem; font-weight: 700; letter-spacing: -0.01em;">PharmaApp</span>
            </a>

            <!-- Desktop nav -->
            <div class="hidden items-center gap-4 md:flex">
                @auth
                    @php $user = auth()->user(); @endphp

                    {{-- Navigation contextuelle selon le rôle --}}
                    @if ($user->hasRole('client'))
                        <a href="{{ route('client.ordonnances.index') }}"
                           class="flex items-center gap-1.5 text-sm text-teal-100 hover:text-white transition-colors"
                           style="text-decoration:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Mes ordonnances
                        </a>
                    @endif

                    @if ($user->hasRole('pharmacien'))
                        <a href="{{ route('pharmacien.ordonnances.index') }}"
                           class="flex items-center gap-1.5 text-sm text-teal-100 hover:text-white transition-colors"
                           style="text-decoration:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Ordonnances
                        </a>
                        <a href="{{ route('pharmacien.commandes.index') }}"
                           class="flex items-center gap-1.5 text-sm text-teal-100 hover:text-white transition-colors"
                           style="text-decoration:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Commandes
                        </a>
                        <a href="{{ route('pharmacien.catalogue.index') }}"
                           class="flex items-center gap-1.5 text-sm text-teal-100 hover:text-white transition-colors"
                           style="text-decoration:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Catalogue
                        </a>
                        <a href="{{ route('pharmacien.factures.index') }}"
                           class="flex items-center gap-1.5 text-sm text-teal-100 hover:text-white transition-colors"
                           style="text-decoration:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Factures
                        </a>
                    @endif

                    @if ($user->hasRole('fournisseur'))
                        <a href="{{ route('fournisseur.commandes.index') }}"
                           class="flex items-center gap-1.5 text-sm text-teal-100 hover:text-white transition-colors"
                           style="text-decoration:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Commandes reçues
                        </a>
                        <a href="{{ route('fournisseur.produits.index') }}"
                           class="flex items-center gap-1.5 text-sm text-teal-100 hover:text-white transition-colors"
                           style="text-decoration:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Mon Catalogue
                        </a>
                        <a href="{{ route('fournisseur.factures.index') }}"
                           class="flex items-center gap-1.5 text-sm text-teal-100 hover:text-white transition-colors"
                           style="text-decoration:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Règlements
                        </a>
                    @endif

                    @if ($user->hasRole('admin'))
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center gap-1.5 text-sm text-teal-100 hover:text-white transition-colors"
                           style="text-decoration:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Utilisateurs
                        </a>
                    @endif

                    <!-- Séparateur -->
                    <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.25);"></div>

                    <!-- Panier (Pharmacien uniquement) -->
                    @if ($user->hasRole('pharmacien'))
                        @php
                            $panierCount = $user->commandesPassees()->where('status', 'brouillon')->count();
                        @endphp
                        <a href="{{ route('pharmacien.commandes.index', ['status' => 'brouillon']) }}" class="relative flex items-center justify-center text-teal-100 hover:text-white transition-colors" style="width: 34px; height: 34px;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            @if($panierCount > 0)
                                <span class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white border border-teal-800" style="transform: translate(25%, -25%);">
                                    {{ $panierCount }}
                                </span>
                            @endif
                        </a>
                    @endif

                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="relative flex items-center justify-center text-teal-100 hover:text-white transition-colors" style="width: 34px; height: 34px;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($user->unreadNotifications->count() > 0)
                                <span class="absolute top-1 right-1 flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border border-teal-800"></span>
                                </span>
                            @endif
                        </button>

                        <div x-show="open" x-cloak x-transition
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg overflow-hidden z-50 border border-slate-200"
                             style="display: none;">
                            <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                <span class="font-semibold text-slate-800 text-sm">Notifications</span>
                                @if($user->unreadNotifications->count() > 0)
                                    <form method="POST" action="{{ route('notifications.markAllAsRead') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="text-xs text-teal-600 hover:text-teal-800 font-medium">Tout marquer comme lu</button>
                                    </form>
                                @endif
                            </div>
                            
                            <div class="max-h-96 overflow-y-auto">
                                @forelse($user->unreadNotifications as $notification)
                                    <a href="{{ route('notifications.readAndRedirect', $notification->id) }}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100 transition-colors">
                                        <p class="text-sm text-slate-800 mb-1">{{ $notification->data['message'] }}</p>
                                        <span class="text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</span>
                                    </a>
                                @empty
                                    <div class="px-4 py-6 text-center text-sm text-slate-500">
                                        Aucune nouvelle notification.
                                    </div>
                                @endforelse
                                
                                @foreach($user->readNotifications->take(3) as $notification)
                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100 transition-colors opacity-60">
                                        <p class="text-sm text-slate-800 mb-1">{{ $notification->data['message'] }}</p>
                                        <span class="text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Profil -->
                    <div class="flex items-center gap-2.5">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: 700; color: #fff; border: 1.5px solid rgba(255,255,255,0.35);">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p style="font-size: 0.8125rem; font-weight: 600; color: #fff; line-height: 1.2;">{{ $user->name }}</p>
                            <p style="font-size: 0.7rem; color: rgba(255,255,255,0.65); line-height: 1;">{{ $user->role->name ?? '—' }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); color: #fff; border-radius: 8px; padding: 6px 14px; font-size: 0.8125rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.375rem;"
                                onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" style="color: rgba(255,255,255,0.85); font-size: 0.875rem; text-decoration: none; transition: color 0.2s;"
                       onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.85)'">Connexion</a>
                    <a href="{{ route('register') }}" style="background: #fff; color: #0f766e; border-radius: 8px; padding: 7px 16px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s;"
                       onmouseover="this.style.background='#f0fdfa'" onmouseout="this.style.background='#fff'">
                        Inscription
                    </a>
                @endauth
            </div>

            <!-- Burger (mobile) -->
            <button class="md:hidden text-white" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileMenuOpen"  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </nav>

        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" x-cloak x-transition class="border-t border-teal-600 px-4 py-3 space-y-2 md:hidden"
             style="background: rgba(7,89,81,0.95); backdrop-filter: blur(8px);">
            @auth
                @php $user = auth()->user(); @endphp
                @if ($user->hasRole('client'))
                    <a href="{{ route('client.ordonnances.index') }}" class="block py-2 text-teal-100 hover:text-white text-sm" style="text-decoration:none;">📋 Mes ordonnances</a>
                @endif
                @if ($user->hasRole('pharmacien'))
                    <a href="{{ route('pharmacien.ordonnances.index') }}" class="block py-2 text-teal-100 hover:text-white text-sm" style="text-decoration:none;">📋 Ordonnances</a>
                    <a href="{{ route('pharmacien.commandes.index') }}" class="block py-2 text-teal-100 hover:text-white text-sm" style="text-decoration:none;">📦 Commandes</a>
                    <a href="{{ route('pharmacien.catalogue.index') }}" class="block py-2 text-teal-100 hover:text-white text-sm" style="text-decoration:none;">🏪 Catalogue Fournisseurs</a>
                    <a href="{{ route('pharmacien.factures.index') }}" class="block py-2 text-teal-100 hover:text-white text-sm" style="text-decoration:none;">🧾 Mes Factures</a>
                @endif
                @if ($user->hasRole('fournisseur'))
                    <a href="{{ route('fournisseur.commandes.index') }}" class="block py-2 text-teal-100 hover:text-white text-sm" style="text-decoration:none;">📦 Commandes reçues</a>
                    <a href="{{ route('fournisseur.produits.index') }}" class="block py-2 text-teal-100 hover:text-white text-sm" style="text-decoration:none;">🗂️ Mon Catalogue</a>
                    <a href="{{ route('fournisseur.factures.index') }}" class="block py-2 text-teal-100 hover:text-white text-sm" style="text-decoration:none;">🧾 Règlements</a>
                @endif
                @if ($user->hasRole('admin'))
                    <a href="{{ route('admin.users.index') }}" class="block py-2 text-teal-100 hover:text-white text-sm" style="text-decoration:none;">👥 Utilisateurs</a>
                @endif
                <div style="height: 1px; background: rgba(255,255,255,0.15); margin: 0.5rem 0;"></div>
                <p class="text-xs text-teal-200">{{ auth()->user()->name }} · {{ auth()->user()->role->name ?? '—' }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left py-2 text-sm text-teal-100 hover:text-white">🚪 Déconnexion</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block py-2 text-teal-100 text-sm" style="text-decoration:none;">Connexion</a>
                <a href="{{ route('register') }}" class="block py-2 text-teal-100 font-medium text-sm" style="text-decoration:none;">Inscription</a>
            @endauth
        </div>
    </header>

    <!-- ═══════════════════ MAIN ═══════════════════ -->
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        @if (session('status'))
            <div class="alert alert-success mb-6 animate-fade-in">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer minimal -->
    <footer style="text-align: center; padding: 2rem; color: #94a3b8; font-size: 0.75rem; margin-top: 4rem;">
        © {{ date('Y') }} PharmaApp — Tous droits réservés
    </footer>

    @stack('scripts')
</body>
</html>
