<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // Compte admin par défaut (à changer immédiatement après premier login)
        User::updateOrCreate(
            ['email' => 'admin@pharma-app.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('ChangeMoi123!'),
                'role_id' => Role::where('slug', 'admin')->first()->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
