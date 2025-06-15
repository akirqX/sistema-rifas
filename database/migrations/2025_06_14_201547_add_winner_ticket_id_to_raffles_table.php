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
            // Adiciona a coluna para guardar o ID da cota vencedora, se ela já não existir
            if (!Schema::hasColumn('raffles', 'winner_ticket_id')) {
                $table->foreignId('winner_ticket_id')
                    ->nullable()
                    ->constrained('tickets') // Garante que o ID exista na tabela tickets
                    ->onDelete('set null')   // Se a cota for deletada, o campo fica nulo
                    ->after('drawn_at');      // Posiciona a coluna no banco de dados
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            // Garante que a chave estrangeira seja removida antes da coluna
            if (Schema::hasColumn('raffles', 'winner_ticket_id')) {
                $table->dropForeign(['winner_ticket_id']);
                $table->dropColumn('winner_ticket_id');
            }
        });
    }
};
