<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();

            // Compte créé par l'admin en attente d'activation (pharmacien/fournisseur)
            $table->enum('status', ['active', 'pending', 'disabled'])->default('active')->after('password');

            $table->string('invite_token')->nullable()->unique()->after('status');
            $table->timestamp('invite_expires_at')->nullable()->after('invite_token');

            // Le mot de passe n'est connu qu'après activation
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn(['status', 'invite_token', 'invite_expires_at']);
        });
    }
};
