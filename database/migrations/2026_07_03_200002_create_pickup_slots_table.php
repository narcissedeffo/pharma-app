<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordonnance_id')->constrained()->cascadeOnDelete();
            $table->datetime('proposed_at');          // créneau proposé par le pharmacien
            $table->datetime('confirmed_at')->nullable(); // confirmé par le client
            $table->datetime('reminder_sent_at')->nullable(); // rappel 1h avant envoyé
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_slots');
    }
};
