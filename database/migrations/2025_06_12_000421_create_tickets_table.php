<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('number');
            $table->enum('status', ['available', 'reserved', 'paid'])->default('available');
            $table->timestamps();
            $table->unique(['raffle_id', 'number']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};