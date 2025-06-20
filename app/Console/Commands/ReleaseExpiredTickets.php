<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Console\Command;

class ReleaseExpiredTickets extends Command
{
    protected $signature = 'tickets:release-expired';
    protected $description = 'Find pending orders that have expired and updates their status to "expired"';

    public function handle()
    {
        $this->info('Searching for expired pending orders...');

        // Encontra os pedidos pendentes que já passaram do tempo de expiração
        $expiredOrders = Order::where('status', 'pending')->where('expires_at', '<=', now())->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No expired orders found.');
            return;
        }

        foreach ($expiredOrders as $order) {
            // ATUALIZA o status do PEDIDO para 'expired'
            $order->update(['status' => 'expired']);

            // A CORREÇÃO CRÍTICA: ATUALIZA o status das COTAS para 'expired'
            // em vez de resetá-las ou deletá-las.
            Ticket::where('order_id', $order->id)->update(['status' => 'expired']);

            $this->info("Order #{$order->id} and its tickets were marked as 'expired'.");
        }

        $this->info('Finished processing expired orders.');
    }
}
