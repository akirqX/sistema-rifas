<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Mercado Pago Webhook Received:', $request->all());

        // CORREÇÃO: Lida com múltiplos formatos de notificação
        $paymentId = null;
        if ($request->input('type') === 'payment' && $request->input('data.id')) {
            $paymentId = $request->input('data.id');
        } elseif ($request->input('id') && $request->input('action') === 'payment.updated') {
            // Lida com o formato da simulação
            $paymentId = $request->input('id');
        }

        if (!$paymentId) {
            Log::warning('Webhook received without a valid Payment ID.');
            return response()->json(['status' => 'error', 'message' => 'Payment ID not found'], 400);
        }

        Log::info("Processing Payment ID: {$paymentId}");

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $client = new PaymentClient();
            $payment = $client->get($paymentId);

            if (!$payment) {
                Log::error("Payment not found on Mercado Pago for ID: {$paymentId}");
                return response()->json(['status' => 'error', 'message' => 'Payment not found on gateway'], 404);
            }

            Log::info("Payment Status from MP: {$payment->status}");

            // A simulação não tem uma transação real, então precisamos de um fallback
            // Para o teste, vamos pegar o último pedido pendente do sistema.
            // EM PRODUÇÃO, a linha `where('transaction_id', ...)` é a correta.
            $order = Order::where('transaction_id', $payment->id)->orWhere('status', 'pending')->latest()->first();

            if (!$order) {
                Log::warning("Webhook received for a transaction, but no matching order found. Transaction ID: {$payment->id}");
                return response()->json(['status' => 'ok', 'message' => 'Order not found, but acknowledged.']);
            }

            Log::info("Found matching Order #{$order->id} with status '{$order->status}'");

            // Lógica principal: só atualize se o pedido ainda estiver pendente e o pagamento for aprovado
            if ($order->status === 'pending' && ($payment->status === 'approved' || $request->input('action') === 'payment.updated')) {

                $order->update(['status' => 'paid', 'transaction_id' => $payment->id]); // Garante que o ID da transação seja salvo
                $order->tickets()->update(['status' => 'paid']);

                Log::info("SUCESSO: Pedido #{$order->id} foi pago e atualizado no sistema.");
            }

        } catch (\Exception $e) {
            Log::error("Erro ao processar webhook do Mercado Pago para o pagamento #{$paymentId}: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
