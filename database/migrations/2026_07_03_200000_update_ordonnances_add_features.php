<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordonnances', function (Blueprint $table) {
            // Date d'expiration (3 mois légaux)
            $table->date('expires_at')->nullable()->after('published_at');

            // Notation client post-retrait
            $table->unsignedTinyInteger('rating')->nullable()->after('expires_at');
            $table->text('rating_comment')->nullable()->after('rating');
            $table->timestamp('rated_at')->nullable()->after('rating_comment');

            // Soft deletes pour archivage
            $table->softDeletes();
        });

        // Recréer le statut enum avec les nouvelles valeurs (SQLite ne supporte pas ALTER COLUMN sur enum)
        // On gère les nouveaux statuts directement dans le modèle via validation applicative
    }

    public function down(): void
    {
        Schema::table('ordonnances', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'rating', 'rating_comment', 'rated_at']);
            $table->dropSoftDeletes();
        });
    }
};
