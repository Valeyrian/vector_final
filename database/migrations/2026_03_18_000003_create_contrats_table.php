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
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 255);
            $table->enum('type', ['Inclus', 'Facturable']);
            $table->decimal('heures_totales', 7, 2)->default(0.00);
            $table->decimal('heures_consommees', 7, 2)->default(0.00);
            $table->decimal('taux_horaire', 8, 2)->default(0.00);
            $table->decimal('montant_total', 10, 2)->default(0.00);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->text('conditions')->nullable();
            $table->enum('statut', ['actif', 'inactif', 'termine'])->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
