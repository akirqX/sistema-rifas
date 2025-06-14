<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->decimal('ticket_price', 8, 2);
            $table->integer('total_tickets');
            $table->enum('status', ['pending', 'active', 'finished', 'cancelled'])->default('pending');
            $table->timestamp('draw_date')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('raffles');
    }
};