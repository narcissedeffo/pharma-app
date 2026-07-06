<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacien_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('fournisseur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference')->unique();
            $table->enum('status', ['brouillon', 'envoyee', 'en_preparation', 'expediee', 'livree'])->default('brouillon');
            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes_fournisseurs');
    }
};
