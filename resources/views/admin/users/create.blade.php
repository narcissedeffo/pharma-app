@extends('layouts.app')

@section('title', 'Inviter un utilisateur')

@section('content')
<div class="animate-fade-in-up mx-auto" style="max-width: 520px;">

    {{-- Back --}}
    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost mb-6" style="width: fit-content;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour aux utilisateurs
    </a>

    <div class="card p-8">
        {{-- Header --}}
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #ede9fe, #ddd6fe); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <svg class="w-7 h-7" style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">Inviter un utilisateur</h1>
            <p style="font-size: 0.875rem; color: #64748b;">Un lien d'activation valable 48h sera envoyé par email.</p>
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

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="label" for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                       class="input" placeholder="Dr. Jean Dupont">
            </div>

            <div>
                <label class="label" for="email">Adresse email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="input" placeholder="jean.dupont@pharmacie.fr">
            </div>

            <div>
                <label class="label" for="role_id">Rôle</label>
                <select id="role_id" name="role_id" required class="input">
                    <option value="">— Sélectionner un rôle —</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.875rem;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Envoyer l'invitation
            </button>
        </form>
    </div>
</div>
@endsection
