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
            // Se a coluna draw_date existir, ela será removida
            if (Schema::hasColumn('raffles', 'draw_date')) {
                $table->dropColumn('draw_date');
            }
            // Adiciona a nova coluna para registrar o momento do sorteio
            $table->timestamp('drawn_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            // Lógica para reverter, caso seja necessário
            $table->dropColumn('drawn_at');
            $table->timestamp('draw_date')->nullable();
        });
    }
};
