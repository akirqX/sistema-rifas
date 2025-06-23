<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class ReconcilePayments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payments:reconcile {--days=7 : Number of days to look back}';

    /**
     * The console command description.
     */
    protected $description = 'Reconcilia pagamentos pendentes com o status no Mercado Pago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $this->info("Iniciando reconciliação de pagamentos dos últimos {$days} dias...");

        // Buscar pedidos pendentes ou em processamento dos últimos X dias
        $orders = Order::whereIn('status', ['pending', 'in_process'])
            ->whereNotNull('transaction_id')
            ->where('payment_gateway', 'mercadopago')
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Nenhum pedido pendente encontrado para reconciliação.');
            return;
        }

        $this->info("Encontrados {$orders->count()} pedidos para reconciliação.");

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
        $client = new PaymentClient();

        $reconciled = 0;
        $errors = 0;

        foreach ($orders as $order) {
            try {
                $payment = $client->get($order->transaction_id);
                
                if (!$payment) {
                    $this->warn("Pagamento {$order->transaction_id} não encontrado no Mercado Pago");
                    continue;
                }

                $currentStatus = $order->status;
                $mpStatus = $payment->status;

                // Verificar se o status mudou
                if ($this->shouldUpdateStatus($currentStatus, $mpStatus)) {
                    $this->updateOrderStatus($order, $payment);
                    $reconciled++;
                    $this->info("Pedido #{$order->id}: {$currentStatus} → {$this->mapMercadoPagoStatus($mpStatus)}");
                }

            } catch (\Exception $e) {
                $errors++;
                $this->error("Erro ao reconciliar pedido #{$order->id}: " . $e->getMessage());
                Log::error("Erro na reconciliação do pedido #{$order->id}: " . $e->getMessage());
            }
        }

        $this->info("Reconciliação concluída: {$reconciled} pedidos atualizados, {$errors} erros.");
    }

    /**
     * Verifica se o status do pedido deve ser atualizado
     */
    private function shouldUpdateStatus(string $currentStatus, string $mpStatus): bool
    {
        $statusMap = [
            'approved' => 'paid',
            'cancelled' => 'failed',
            'rejected' => 'failed',
            'refunded' => 'refunded',
            'charged_back' => 'refunded',
            'in_process' => 'in_process',
            'pending' => 'pending'
        ];

        $expectedStatus = $statusMap[$mpStatus] ?? null;
        
        return $expectedStatus && $expectedStatus !== $currentStatus;
    }

    /**
     * Atualiza o status do pedido baseado no status do Mercado Pago
     */
    private function updateOrderStatus(Order $order, $payment): void
    {
        $newStatus = $this->mapMercadoPagoStatus($payment->status);
        
        $order->update(['status' => $newStatus]);

        // Atualizar tickets se necessário
        if ($payment->status === 'approved' && $order->raffle_id) {
            $order->tickets()->update(['status' => 'paid']);
        } elseif (in_array($payment->status, ['cancelled', 'rejected', 'refunded', 'charged_back']) && $order->raffle_id) {
            $order->tickets()->update([
                'status' => 'available',
                'order_id' => null,
                'user_id' => null
            ]);
        }

        // Atualizar produtos se necessário
        if ($order->product_id) {
            $product = $order->product;
            if ($payment->status === 'approved') {
                $product->update(['status' => 'sold']);
            } elseif (in_array($payment->status, ['refunded', 'charged_back'])) {
                $product->update(['status' => 'available']);
            }
        }
    }

    /**
     * Mapeia status do Mercado Pago para status do sistema
     */
    private function mapMercadoPagoStatus(string $mpStatus): string
    {
        return match($mpStatus) {
            'approved' => 'paid',
            'cancelled', 'rejected' => 'failed',
            'refunded', 'charged_back' => 'refunded',
            'in_process' => 'in_process',
            default => 'pending'
        };
    }
}

