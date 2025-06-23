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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "AK-47 | Asiimov"
            $table->text('description')->nullable(); // Detalhes, float, pattern
            $table->enum('type', ['in_stock', 'on_demand']); // 'Pronta Entrega' ou 'Sob Encomenda'
            $table->string('wear'); // Ex: "Field-Tested", "Minimal Wear"
            $table->decimal('price', 10, 2); // Preço de venda
            $table->enum('status', ['available', 'sold', 'unavailable']); // Status para itens 'in_stock'
            $table->string('steam_inspect_link')->nullable(); // Link de inspeção da Steam
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
