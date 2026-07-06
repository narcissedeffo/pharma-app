<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

$pharmacienRole = Role::where('slug', 'pharmacien')->first();
$fournisseurRole = Role::where('slug', 'fournisseur')->first();

User::firstOrCreate(
    ['email' => 'pharmacien@test.com'],
    [
        'name' => 'Pharmacien Test',
        'password' => Hash::make('password'),
        'role_id' => $pharmacienRole->id,
        'status' => 'active',
        'email_verified_at' => now(),
    ]
);

User::firstOrCreate(
    ['email' => 'fournisseur@test.com'],
    [
        'name' => 'Fournisseur Test',
        'password' => Hash::make('password'),
        'role_id' => $fournisseurRole->id,
        'status' => 'active',
        'email_verified_at' => now(),
    ]
);

echo "Test users created:\n";
echo "Pharmacien: pharmacien@test.com / password\n";
echo "Fournisseur: fournisseur@test.com / password\n";
