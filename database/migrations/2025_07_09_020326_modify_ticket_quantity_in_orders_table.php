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
        Schema::table('orders', function (Blueprint $table) {
            // Modifica a coluna para permitir valores nulos.
            // O tipo (integer) e outras propriedades permanecem os mesmos.
            $table->integer('ticket_quantity')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Se precisarmos reverter, tornamos a coluna não-nula novamente.
            // Pode ser necessário definir um valor padrão se houver nulos.
            $table->integer('ticket_quantity')->nullable(false)->change();
        });
    }
};
