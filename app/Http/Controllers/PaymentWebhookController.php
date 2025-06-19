<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product; // <-- 1. IMPORTAMOS O MODEL PRODUCT
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

                        // --- 2. LÓGICA DE DECISÃO: É RIFA OU SKIN? ---
                        if ($order->product_id) {
                            // É um pedido de SKIN!
                            $product = Product::find($order->product_id);
                            if ($product) {
                                $product->status = 'sold'; // Marca a skin como vendida
                                $product->save();
                                Log::info("SUCESSO (Skin): Pedido Real #{$order->id} pago. Skin #{$product->id} marcada como vendida.");
                                // TODO: Disparar evento para notificar o admin para entregar a skin
                                // event(new SkinSold($order));
                            }
                        } else {
                            // É um pedido de RIFA (lógica original)
                            $order->tickets()->update(['status' => 'paid']);
                            Log::info("SUCESSO (Rifa): Pedido Real #{$order->id} foi pago e bilhetes atualizados.");
                        }
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

            // --- 3. LÓGICA DE DECISÃO REPLICADA PARA TESTES ---
            if ($order->product_id) {
                // É um pedido de SKIN!
                $product = Product::find($order->product_id);
                if ($product) {
                    $product->status = 'sold';
                    $product->save();
                    Log::info("SUCESSO (Simulação de Skin): Pedido #{$order->id} pago. Skin #{$product->id} marcada como vendida.");
                }
            } else {
                // É um pedido de RIFA
                $order->tickets()->update(['status' => 'paid']);
                Log::info("SUCESSO (Simulação de Rifa): Pedido #{$order->id} foi pago e bilhetes atualizados.");
            }

        } else {
            Log::warning("Simulação de Webhook recebida, mas nenhum pedido pendente foi encontrado para testar.");
        }
    }
}
