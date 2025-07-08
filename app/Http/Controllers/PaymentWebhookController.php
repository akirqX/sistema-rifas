<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Webhook Recebido:', $request->all());
        if (!$request->has('data.id')) {
            Log::warning('Webhook recebido sem data.id.');
            return response()->json(['status' => 'missing_id'], 400);
        }
        $paymentId = $request->input('data.id');
        try {
            DB::transaction(function () use ($paymentId) {
                $order = Order::where('transaction_id', $paymentId)->where('status', 'pending')->first();
                if (!$order) {
                    Log::info("Webhook ignorado: pedido com transaction_id {$paymentId} não encontrado ou já processado.");
                    return;
                }
                MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
                $payment = (new PaymentClient())->get($paymentId);
                if ($payment && $payment->status === 'approved') {
                    $this->handleApprovedPayment($order);
                } elseif ($payment && in_array($payment->status, ['rejected', 'cancelled', 'failed'])) {
                    $this->handleFailedPayment($order);
                }
            });
        } catch (\Exception $e) {
            Log::error("Erro CRÍTICO no Webhook: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error'], 500);
        }
        return response()->json(['status' => 'success'], 200);
    }
    protected function handleApprovedPayment(Order $order): void
    {
        $order->update(['status' => 'paid']);
        if ($order->raffle_id) {
            $order->tickets()->update(['status' => 'paid']);
        }
        Log::info("SUCESSO: Pedido #{$order->id} atualizado para PAGO.");
    }
    protected function handleFailedPayment(Order $order): void
    {
        $order->update(['status' => 'failed']);
        if ($order->raffle_id) {
            $order->tickets()->update(['status' => 'available', 'order_id' => null, 'user_id' => null]);
        }
        Log::info("FALHA: Pagamento do Pedido #{$order->id} falhou. Bilhetes liberados.");
    }
}
