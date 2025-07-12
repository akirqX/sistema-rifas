<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Raffle;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Passo 1: Adicionar a coluna user_id, mas permitir que seja nula por enquanto.
        Schema::table('raffles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id')->nullable();
        });

        // Passo 2: Atribuir um usuário padrão (o admin, ID 1) para todas as rifas existentes.
        // Isso corrige os dados "órfãos".
        if (Raffle::whereNull('user_id')->exists()) {
            Raffle::whereNull('user_id')->update(['user_id' => 1]); // Assumindo que o admin é o usuário com ID 1
        }

        // Passo 3: Agora que os dados estão corrigidos, podemos tornar a coluna obrigatória (change())
        // e adicionar a chave estrangeira.
        Schema::table('raffles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
