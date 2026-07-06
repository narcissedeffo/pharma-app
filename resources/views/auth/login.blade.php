<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — PharmaApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="min-height: 100vh; background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 50%, #f0f9ff 100%); display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">

    <div class="animate-fade-in-up" style="width: 100%; max-width: 440px;">

        {{-- Logo --}}
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #0f766e, #0d9488); border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 8px 24px rgba(15,118,110,0.35);">
                <svg class="w-8 h-8" style="color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
            </div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">PharmaApp</h1>
            <p style="font-size: 0.875rem; color: #64748b;">Connectez-vous à votre espace</p>
        </div>

        <div class="card p-8">
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

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="label" for="email">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="input" placeholder="vous@exemple.com">
                </div>

                <div>
                    <label class="label" for="password">Mot de passe</label>
                    <input id="password" type="password" name="password" required
                           class="input" placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.875rem; font-size: 1rem;">
                    Se connecter
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>
        </div>

        <p style="text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: #64748b;">
            Pas encore de compte ?
            <a href="{{ route('register') }}" style="color: #0f766e; font-weight: 600; text-decoration: none;">
                S'inscrire
            </a>
        </p>
    </div>

</body>
</html>
