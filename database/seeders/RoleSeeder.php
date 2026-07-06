<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrateur', 'slug' => 'admin'],
            ['name' => 'Client', 'slug' => 'client'],
            ['name' => 'Pharmacien', 'slug' => 'pharmacien'],
            ['name' => 'Fournisseur', 'slug' => 'fournisseur'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
