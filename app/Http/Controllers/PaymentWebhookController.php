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

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $client = new PaymentClient();

            // Lida com o formato da simulação do painel
            if ($request->input('action') === 'payment.updated' && $request->input('id')) {
                $this->handleTestWebhook($request->input('id'), $client);
                return response()->json(['status' => 'success_test'], 200);
            }

            // Lida com notificações reais
            if ($request->input('type') === 'payment' && $request->input('data.id')) {
                $paymentId = $request->input('data.id');
                $payment = $client->get($paymentId);

                if ($payment && $payment->status === 'approved') {
                    $order = Order::where('transaction_id', $payment->id)->where('status', 'pending')->first();
                    if ($order) {
                        $order->update(['status' => 'paid']);
                        $order->tickets()->update(['status' => 'paid']);
                        Log::info("SUCESSO: Pedido Real #{$order->id} foi pago e atualizado.");
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error("Erro no Webhook: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Lógica isolada para lidar apenas com as simulações do painel do Mercado Pago.
     */
    protected function handleTestWebhook(string $testPaymentId, PaymentClient $client)
    {
        Log::info("Simulação de Webhook detectada para o ID: {$testPaymentId}");

        // Pega o pedido pendente mais recente para simular a aprovação
        $order = Order::where('status', 'pending')->latest()->first();

        if ($order) {
            $order->update(['status' => 'paid', 'transaction_id' => $testPaymentId]);
            $order->tickets()->update(['status' => 'paid']);
            Log::info("SUCESSO (Simulação): Pedido #{$order->id} foi pago e atualizado.");
        } else {
            Log::warning("Simulação de Webhook recebida, mas nenhum pedido pendente foi encontrado para testar.");
        }
    }
}
