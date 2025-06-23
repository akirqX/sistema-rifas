<?php
namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredTickets extends Command
{
    protected $signature = 'tickets:release-expired';
    protected $description = 'Encontra pedidos pendentes expirados, atualiza o status e libera as cotas associadas.';

    public function handle()
    {
        $this->info('Verificando pedidos expirados...');

        // 1. Busca apenas os IDs dos pedidos expirados. É muito leve para o banco.
        $expiredOrderIds = Order::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->pluck('id');

        if ($expiredOrderIds->isEmpty()) {
            $this->info('Nenhum pedido expirado encontrado. Tudo certo!');
            return self::SUCCESS;
        }

        $orderCount = $expiredOrderIds->count();
        $this->info("Encontrados {$orderCount} pedidos expirados. Iniciando liberação...");

        $totalTicketsReleased = 0;

        // 2. Executa todas as atualizações dentro de uma única transação segura.
        DB::transaction(function () use ($expiredOrderIds, &$totalTicketsReleased) {

            // 3. Libera todos os tickets de uma vez (1 query no banco)
            $totalTicketsReleased = Ticket::whereIn('order_id', $expiredOrderIds)
                ->where('status', 'reserved')
                ->update([
                    'status' => 'available',
                    'order_id' => null,
                    'user_id' => null
                ]);

            // 4. Atualiza todos os pedidos de uma vez (1 query no banco)
            Order::whereIn('id', $expiredOrderIds)
                ->update(['status' => 'expired']);
        });

        $summary = "Processo concluído. Total de {$totalTicketsReleased} tickets liberados de {$orderCount} pedidos expirados.";
        $this->info($summary);
        Log::info($summary); // Também registra no seu laravel.log

        return self::SUCCESS;
    }
}
