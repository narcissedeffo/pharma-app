<!DOCTYPE html>
<html lang="fr" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0f766e">
    <meta name="description" content="Pharma App — Votre partenaire santé en ligne. Gérez vos ordonnances facilement.">
    <title>Accueil — PharmaApp</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col" style="background: #f8fafc;">

    <!-- ═══════════════════ NAVBAR ═══════════════════ -->
    <header style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); box-shadow: 0 2px 20px rgba(15,118,110,0.4);">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-white no-underline">
                <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 8px; backdrop-filter: blur(8px);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <span style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.02em;">PharmaApp</span>
            </a>

            <!-- Desktop nav -->
            <div class="hidden md:flex items-center gap-6">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-white hover:text-teal-100 font-medium transition-colors" style="text-decoration:none;">
                        Mon Espace
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-teal-50 hover:text-white font-medium transition-colors" style="text-decoration:none;">
                        Connexion
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-white text-teal-700 font-bold hover:bg-teal-50 transition-colors shadow-sm" style="text-decoration:none;">
                        S'inscrire
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="text-teal-50 hover:text-white" @click="mobileMenuOpen = !mobileMenuOpen">
                    <span class="sr-only">Ouvrir le menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>
        
        <!-- Mobile menu panel -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-teal-800 border-t border-teal-700">
            <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-teal-700">Mon Espace</a>
                @else
                    <a href="{{ route('login') }}" class="block rounded-md px-3 py-2 text-base font-medium text-teal-100 hover:bg-teal-700 hover:text-white">Connexion</a>
                    <a href="{{ route('register') }}" class="block rounded-md px-3 py-2 text-base font-medium text-teal-100 hover:bg-teal-700 hover:text-white">S'inscrire</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ═══════════════════ HERO SECTION ═══════════════════ -->
    <main class="flex-grow">
        <div class="relative overflow-hidden bg-white">
            <!-- Decorative background elements -->
            <div class="absolute inset-0 z-0">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-teal-50 rounded-full blur-3xl opacity-60"></div>
                <div class="absolute top-1/2 right-0 w-80 h-80 bg-blue-50 rounded-full blur-3xl opacity-60 transform -translate-y-1/2"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-20 pb-24 lg:pt-32 lg:pb-32">
                <div class="text-center max-w-3xl mx-auto animate-fade-in-up">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-100 text-teal-800 text-sm font-semibold mb-6">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                        </span>
                        Nouveau : Gestion 100% en ligne
                    </span>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 mb-8 leading-tight">
                        Votre santé, <br>
                        <span class="gradient-text">simplifiée et connectée.</span>
                    </h1>
                    
                    <p class="mt-4 text-lg sm:text-xl text-slate-600 mb-10 leading-relaxed max-w-2xl mx-auto">
                        PharmaApp révolutionne votre relation avec votre pharmacie. Déposez vos ordonnances en un clic, suivez leur préparation en temps réel et gagnez un temps précieux.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary text-base px-8 py-3.5 w-full sm:w-auto justify-center">
                                Accéder à mon espace
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary text-base px-8 py-3.5 w-full sm:w-auto justify-center">
                                Créer un compte gratuit
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary text-base px-8 py-3.5 w-full sm:w-auto justify-center bg-white">
                                J'ai déjà un compte
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════════════ FEATURES SECTION ═══════════════════ -->
        <div class="bg-slate-50 py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 animate-fade-in-up" style="animation-delay: 0.1s;">
                    <h2 class="text-3xl font-bold text-slate-900">Comment ça marche ?</h2>
                    <p class="mt-4 text-lg text-slate-600">Un processus simple, rapide et sécurisé en 3 étapes.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 stagger-children">
                    <!-- Feature 1 -->
                    <div class="card p-8 text-center animate-fade-in-up">
                        <div class="mx-auto w-16 h-16 bg-teal-100 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">1. Déposez</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Prenez en photo ou scannez votre ordonnance et envoyez-la de manière sécurisée sur notre plateforme.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="card p-8 text-center animate-fade-in-up">
                        <div class="mx-auto w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">2. Choisissez</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Sélectionnez la pharmacie partenaire de votre choix pour préparer votre commande.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="card p-8 text-center animate-fade-in-up">
                        <div class="mx-auto w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">3. Suivez</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Soyez notifié en temps réel de l'état d'avancement (en cours, validée) par votre pharmacien.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ═══════════════════ FOOTER ═══════════════════ -->
    <footer class="bg-white border-t border-slate-200">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex justify-center items-center gap-2 text-teal-700 font-bold text-xl mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                PharmaApp
            </div>
            <p class="text-center text-sm text-slate-500 mb-6 max-w-md mx-auto">
                La plateforme sécurisée qui facilite la gestion de vos ordonnances et vos échanges avec les professionnels de santé.
            </p>
            <div class="text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} PharmaApp. Tous droits réservés.
            </div>
        </div>
    </footer>

</body>
</html>
