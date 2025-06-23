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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // CORREÇÃO: Usando um tamanho de string seguro e flexível para o futuro.
            $table->string('number', 10);

            $table->enum('status', ['available', 'reserved', 'paid'])->default('available');
            $table->timestamps();

            // Constraints e Índices para performance e integridade
            $table->unique(['raffle_id', 'number']);
            $table->index(['raffle_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
