<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AccountInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('role')->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        // L'admin ne crée ici que des comptes internes (pas "client", qui s'inscrit lui-même)
        $roles = Role::whereIn('slug', ['pharmacien', 'fournisseur', 'admin'])->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $token = Str::random(64);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'password' => null,
            'status' => 'pending',
            'invite_token' => $token,
            'invite_expires_at' => now()->addHours(48),
        ]);

        $activationUrl = route('activate.show', ['token' => $token]);

        $user->notify(new AccountInvitation($activationUrl, $user->role->name));

        return redirect()->route('admin.users.index')
            ->with('status', "Invitation envoyée à {$user->email}.");
    }
}
