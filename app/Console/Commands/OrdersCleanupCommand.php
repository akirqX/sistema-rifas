<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // IMPORTANTE: Adicionar esta linha

class OrdersCleanupCommand extends Command
{
    protected $signature = 'orders:cleanup';
    protected $description = 'Cancela pedidos pendentes expirados e libera os tickets';

    public function handle()
    {
        $this->info('Iniciando a limpeza de pedidos expirados...');

        // Encontra todos os pedidos que estão pendentes e cujo tempo de expiração já passou.
        $expiredOrders = Order::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('Nenhum pedido expirado encontrado.');
            return 0;
        }

        $this->info("Encontrados {$expiredOrders->count()} pedidos para cancelar...");

        foreach ($expiredOrders as $order) {
            try {
                // 👇👇👇 MELHORIA DE SEGURANÇA ESTÁ AQUI 👇👇👇
                DB::transaction(function () use ($order) {
                    // Primeiro, libera os tickets associados ao pedido.
                    $order->tickets()->update([
                        'status' => 'available',
                        'order_id' => null,
                        'user_id' => null,
                        'session_id' => null,
                        'reserved_until' => null,
                    ]);

                    // Depois, atualiza o status do próprio pedido.
                    $order->update(['status' => 'cancelled']);
                });

                $this->info("Pedido #{$order->id} cancelado e tickets liberados com sucesso.");

            } catch (\Exception $e) {
                // Se algo der errado na transação, loga o erro e continua para o próximo.
                $this->error("Falha ao processar o pedido #{$order->id}: " . $e->getMessage());
            }
        }

        $this->info('Limpeza de pedidos expirados concluída.');
        return 0;
    }
}
