<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ActivateAccountController extends Controller
{
    /**
     * Affiche le formulaire de définition du mot de passe.
     */
    public function show(string $token): View|RedirectResponse
    {
        $user = User::where('invite_token', $token)->first();

        if (! $user || ! $this->tokenIsValid($user)) {
            return redirect()->route('login')
                ->withErrors(['email' => "Ce lien d'activation est invalide ou a expiré."]);
        }

        return view('auth.activate', ['token' => $token, 'user' => $user]);
    }

    /**
     * Enregistre le mot de passe choisi et active le compte.
     */
    public function store(Request $request, string $token): RedirectResponse
    {
        $user = User::where('invite_token', $token)->first();

        if (! $user || ! $this->tokenIsValid($user)) {
            return redirect()->route('login')
                ->withErrors(['email' => "Ce lien d'activation est invalide ou a expiré."]);
        }

        $rules = [
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($user->role->slug === 'pharmacien') {
            $rules['address'] = ['required', 'string', 'max:255'];
            $rules['latitude'] = ['required', 'numeric', 'between:-90,90'];
            $rules['longitude'] = ['required', 'numeric', 'between:-180,180'];
        }

        $validated = $request->validate($rules);

        $updateData = [
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            'invite_token' => null,
            'invite_expires_at' => null,
            'email_verified_at' => now(),
        ];

        if ($user->role->slug === 'pharmacien') {
            $updateData['address'] = $validated['address'];
            $updateData['latitude'] = $validated['latitude'];
            $updateData['longitude'] = $validated['longitude'];
        }

        $user->update($updateData);

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Compte activé avec succès.');
    }

    private function tokenIsValid(User $user): bool
    {
        return $user->status === 'pending'
            && $user->invite_expires_at
            && $user->invite_expires_at->isFuture();
    }
}
