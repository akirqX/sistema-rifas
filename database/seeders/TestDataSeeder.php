<?php

namespace Database\Seeders;

use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $raffle = Raffle::create([
            'title' => 'Rifa de Teste: Um Super PC Gamer',
            'description' => 'Concorra a um PC completo com RTX 4090!',
            'ticket_price' => 2.50,
            'total_tickets' => 1000,
            'status' => 'active'
        ]);

        // CORREÇÃO: O preenchimento com zeros (padding) agora é dinâmico.
        $padding = strlen((string) $raffle->total_tickets);

        $tickets = [];
        for ($i = 1; $i <= $raffle->total_tickets; $i++) {
            $tickets[] = [
                'raffle_id' => $raffle->id,
                'number' => str_pad($i, $padding, '0', STR_PAD_LEFT),
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insere os tickets em lotes para melhor performance
        foreach (array_chunk($tickets, 500) as $chunk) {
            Ticket::insert($chunk);
        }
    }
}
