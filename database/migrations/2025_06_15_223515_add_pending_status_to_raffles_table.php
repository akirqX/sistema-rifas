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
        Schema::table('raffles', function (Blueprint $table) {
            // Modifica a coluna 'status' para incluir o novo valor 'pending'
            // Substitua os valores pelos que você realmente tem, se forem diferentes.
            $table->enum('status', ['pending', 'active', 'finished', 'cancelled'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            // Lógica para reverter, se necessário
            $table->enum('status', ['active', 'finished', 'cancelled'])->change();
        });
    }
};
