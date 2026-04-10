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
        Schema::create('projet_collaborateur', function (Blueprint $table) {
            $table->foreignId('projet_id')->constrained('projets')->cascadeOnDelete();
            $table->foreignId('collaborateur_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['projet_id', 'collaborateur_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet_collaborateur');
    }
};
