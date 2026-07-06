<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes_fournisseurs')->cascadeOnDelete();
            $table->string('reference')->unique(); // FAC-XXXX-YYYY
            $table->string('bl_reference')->nullable(); // BL-XXXX-YYYY
            $table->decimal('montant_total', 10, 2);
            $table->date('date_emission');
            $table->date('date_echeance');
            $table->enum('status', ['en_attente', 'payee', 'en_retard'])->default('en_attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
