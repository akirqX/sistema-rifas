<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;

class TicketsCleanupCommand extends Command
{
    /**
     * A assinatura do comando. É como você vai chamá-lo no terminal.
     * Nós damos um nome claro a ele.
     */
    protected $signature = 'tickets:cleanup';

    /**
     * A descrição do comando, que aparece quando você lista os comandos artisan.
     */
    protected $description = 'Libera os tickets reservados cujo tempo de reserva expirou';

    /**
     * Executa a lógica do comando.
     */
    public function handle()
    {
        $this->info('Iniciando a limpeza de tickets expirados...');

        // Encontra todos os tickets que:
        // 1. Têm o status 'reserved'.
        // 2. O campo 'reserved_until' tem um tempo que já passou.
        $expiredTickets = Ticket::where('status', 'reserved')
            ->where('reserved_until', '<', now());

        // Pega a contagem de tickets a serem liberados para exibir no log.
        $count = $expiredTickets->count();

        if ($count > 0) {
            // Atualiza todos os tickets encontrados de uma só vez.
            $expiredTickets->update([
                'status' => 'available',
                'session_id' => null,
                'reserved_until' => null,
                'order_id' => null,
                'user_id' => null, // Limpa também o user_id da reserva
            ]);

            // Exibe uma mensagem de sucesso no terminal.
            $this->info("Sucesso! {$count} ticket(s) foram liberados.");
        } else {
            // Exibe uma mensagem informando que nada precisou ser feito.
            $this->info('Nenhum ticket expirado encontrado.');
        }

        return 0; // Retorna 0 para indicar que o comando foi executado com sucesso.
    }
}
