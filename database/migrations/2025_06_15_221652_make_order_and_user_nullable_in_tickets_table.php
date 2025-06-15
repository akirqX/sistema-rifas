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
            // Modifica as colunas para aceitarem valores nulos.
            // O ->change() no final aplica a modificação.
            $table->foreignId('order_id')->nullable()->change();
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Lógica para reverter, caso precise (torna as colunas não nulas de novo)
            $table->foreignId('order_id')->nullable(false)->change();
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
