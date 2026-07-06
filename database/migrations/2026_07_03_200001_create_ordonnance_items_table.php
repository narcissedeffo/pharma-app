<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordonnance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordonnance_id')->constrained()->cascadeOnDelete();
            $table->string('nom_medicament');
            $table->enum('statut_disponibilite', ['disponible', 'a_commander', 'indisponible'])->default('disponible');
            $table->string('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordonnance_items');
    }
};
