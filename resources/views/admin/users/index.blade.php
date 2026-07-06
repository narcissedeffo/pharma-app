@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="animate-fade-in-up">

    {{-- En-tête --}}
    <div class="page-header">
        <div>
            <h1>Utilisateurs</h1>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">
                Gérez tous les comptes de la plateforme
            </p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Inviter un utilisateur
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Membre depuis</th>
                    </tr>
                </thead>
                <tbody class="stagger-children">
                    @foreach ($users as $user)
                        <tr class="animate-fade-in">
                            {{-- Nom --}}
                            <td>
                                <div class="flex items-center gap-3">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #ede9fe, #ddd6fe); display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: 700; color: #7c3aed; flex-shrink: 0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span style="font-weight: 600; color: #0f172a;">{{ $user->name }}</span>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td style="color: #64748b;">{{ $user->email }}</td>

                            {{-- Rôle --}}
                            <td>
                                @php
                                    $roleColors = [
                                        'admin'      => 'background:#ede9fe; color:#6d28d9;',
                                        'pharmacien' => 'background:#dbeafe; color:#1e40af;',
                                        'client'     => 'background:#dcfce7; color:#166534;',
                                        'fournisseur'=> 'background:#fef9c3; color:#92400e;',
                                    ];
                                    $roleStyle = $roleColors[$user->role->slug ?? ''] ?? 'background:#f1f5f9; color:#475569;';
                                @endphp
                                <span class="badge" style="{{ $roleStyle }}">
                                    {{ $user->role->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Statut --}}
                            <td>
                                @if ($user->status === 'active')
                                    <span class="badge" style="background:#dcfce7; color:#166534;">
                                        <span class="badge-dot" style="background:#22c55e;"></span>
                                        Actif
                                    </span>
                                @elseif ($user->status === 'pending')
                                    <span class="badge" style="background:#fef9c3; color:#92400e;">
                                        <span class="badge-dot" style="background:#eab308;"></span>
                                        En attente
                                    </span>
                                @else
                                    <span class="badge" style="background:#f1f5f9; color:#64748b;">
                                        <span class="badge-dot" style="background:#94a3b8;"></span>
                                        Désactivé
                                    </span>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td style="color: #94a3b8; font-size: 0.8125rem; white-space: nowrap;">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if ($users->hasPages())
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
