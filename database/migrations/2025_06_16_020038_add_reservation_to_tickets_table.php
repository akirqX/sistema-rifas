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
        Schema::table('tickets', function (Blueprint $table) {
            // Garante que todos os status possíveis estão listados
            $table->enum('status', ['available', 'reserved', 'paid', 'cancelled'])->default('available')->change();

            // Coluna para guardar o ID da sessão do usuário que reservou
            $table->string('session_id')->nullable()->after('status');

            // Coluna para guardar até quando a reserva é válida
            $table->timestamp('reserved_until')->nullable()->after('session_id');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            //
        });
    }
};
