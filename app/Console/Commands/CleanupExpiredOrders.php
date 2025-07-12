<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product; // Adicionar import
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupExpiredOrders extends Command
{
    protected $signature = 'orders:cleanup-expired';
    protected $description = 'Finds expired pending orders and releases associated tickets or restocks products.';

    public function handle()
    {
        $this->info('Checking for expired orders...');

        // Busca pedidos expirados, carregando as relações necessárias
        $expiredOrders = Order::with(['product', 'tickets']) // Usamos with() para evitar N+1 queries
            ->where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No expired orders found. All good!');
            return self::SUCCESS;
        }

        $orderCount = $expiredOrders->count();
        $this->info("Found {$orderCount} expired orders. Starting cleanup...");

        $totalTicketsReleased = 0;
        $totalProductsRestocked = 0;

        DB::transaction(function () use ($expiredOrders, &$totalTicketsReleased, &$totalProductsRestocked) {
            foreach ($expiredOrders as $order) {
                // Cenário 1: Pedido de Rifa
                if ($order->raffle_id && $order->tickets->isNotEmpty()) {
                    $releasedCount = $order->tickets()->update([
                        'status' => 'available',
                        'order_id' => null,
                        'user_id' => null
                    ]);
                    $totalTicketsReleased += $releasedCount;
                    $this->line("Order #{$order->id} (Raffle): Released {$releasedCount} tickets.");
                }

                // Cenário 2: Pedido de Produto
                if ($order->product_id && $order->product) {
                    // Devolve o item ao estoque
                    $order->product->increment('quantity');
                    $totalProductsRestocked++;
                    $this->line("Order #{$order->id} (Product): Restocked product #{$order->product_id}.");
                }

                // Marca o pedido como expirado
                $order->update(['status' => 'expired']);
            }
        });

        $summary = "Cleanup complete. Orders processed: {$orderCount}. Tickets released: {$totalTicketsReleased}. Products restocked: {$totalProductsRestocked}.";
        $this->info($summary);
        Log::info($summary);

        return self::SUCCESS;
    }
}
