<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_id')->nullable()->constrained('projets')->nullOnDelete();
            $table->string('titre', 255);
            $table->text('description')->nullable();
            $table->enum('statut', ['nouveau', 'en_cours', 'en_attente_client', 'termine', 'a_valider', 'valide', 'refuse'])->default('nouveau');
            $table->enum('priorite', ['haute', 'moyenne', 'basse'])->default('moyenne');
            $table->enum('type', ['inclus', 'facturable'])->default('inclus');
            $table->decimal('temps_estime', 6, 2)->default(0.00);
            $table->decimal('temps_passe', 6, 2)->default(0.00);
            $table->enum('validation_status', ['en_attente', 'valide', 'refuse'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
