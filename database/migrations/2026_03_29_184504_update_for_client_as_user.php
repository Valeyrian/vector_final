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
        // Add client-related fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('adresse')->nullable()->after('company');
            $table->string('code_postal', 20)->nullable()->after('adresse');
            $table->string('ville')->nullable()->after('code_postal');
            $table->string('pays')->nullable()->after('ville');
        });

        // Update projet_client table to refer to users instead of clients
        Schema::table('projet_client', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Update contrat_client table to refer to users instead of clients
        Schema::table('contrat_client', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Drop unnecessary tables
        Schema::dropIfExists('client_contact_principal');
        Schema::dropIfExists('client_utilisateur');
        Schema::dropIfExists('clients');
    }

    public function down(): void
    {
        // Note: Down migration is not fully reversible without recreating the clients table and relationships
        // But we'll do the basics:
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['adresse', 'code_postal', 'ville', 'pays']);
        });
    }
};
