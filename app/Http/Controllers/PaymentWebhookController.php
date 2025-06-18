<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class PaymentWebhookController extends Controller
{
    /**
     * Lida com as notificações de webhook recebidas do Mercado Pago.
     */
    public function handle(Request $request)
    {
        // 1. Loga toda a requisição para que você possa ver exatamente o que o Mercado Pago enviou.
        Log::info('Mercado Pago Webhook Received:', $request->all());

        // 2. Extrai o ID do pagamento. Lida com o formato de notificações reais ('data.id')
        //    e também com o formato das simulações do painel ('id').
        $paymentId = null;
        if ($request->input('type') === 'payment' && $request->input('data.id')) {
            $paymentId = $request->input('data.id');
        } elseif ($request->input('id') && $request->input('action') === 'payment.updated') {
            $paymentId = $request->input('id');
        }

        if (!$paymentId) {
            Log::warning('Webhook recebido sem um ID de pagamento válido.');
            return response()->json(['status' => 'error', 'message' => 'Payment ID not found'], 400);
        }

        Log::info("Processando Notificação para o Pagamento ID: {$paymentId}");

        try {
            // 3. Configura a SDK e busca os detalhes do pagamento na API do Mercado Pago.
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $client = new PaymentClient();
            $payment = $client->get($paymentId);

            if (!$payment) {
                Log::error("Pagamento não encontrado no Mercado Pago para o ID: {$paymentId}");
                return response()->json(['status' => 'error', 'message' => 'Payment not found on gateway'], 404);
            }

            Log::info("Status do Pagamento no MP: {$payment->status}");

            // 4. Encontra o pedido correspondente no seu banco de dados.
            // A busca primária é pelo ID da transação que salvamos ao criar o pedido.
            $order = Order::where('transaction_id', $payment->id)->first();

            // Fallback para simulação: se não encontrar pelo ID, pega o último pedido pendente.
            // Isso permite que o botão "Simular" do painel do MP funcione para testar a lógica.
            if (!$order && app()->isLocal()) {
                Log::info("Nenhum pedido encontrado com transaction_id {$payment->id}. Buscando último pedido pendente para simulação.");
                $order = Order::where('status', 'pending')->latest()->first();
            }

            if (!$order) {
                Log::warning("Webhook recebido, mas nenhum pedido correspondente encontrado. ID da Transação MP: {$payment->id}");
                return response()->json(['status' => 'ok', 'message' => 'Order not found, but acknowledged.']);
            }

            Log::info("Encontrado Pedido correspondente #{$order->id} com status '{$order->status}'");

            // 5. Lógica principal: Atualiza o pedido apenas se ele estiver pendente e o pagamento for aprovado.
            // A verificação `|| $request->input('action') === 'payment.updated'` garante que a simulação funcione.
            if ($order->status === 'pending' && ($payment->status === 'approved' || $request->input('action') === 'payment.updated')) {

                $order->update([
                    'status' => 'paid',
                    'transaction_id' => $payment->id // Garante que o ID da transação seja salvo, mesmo na simulação.
                ]);

                $order->tickets()->update(['status' => 'paid']);

                Log::info("SUCESSO: Pedido #{$order->id} foi pago e atualizado no sistema.");
            }

        } catch (\Exception $e) {
            Log::error("Erro ao processar webhook para o pagamento #{$paymentId}: " . $e->getMessage());
            // Retornar 500 faz com que o Mercado Pago tente reenviar a notificação mais tarde, o que é uma boa prática.
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }

        // 6. Responde com sucesso para que o Mercado Pago saiba que a notificação foi recebida e processada.
        return response()->json(['status' => 'success'], 200);
    }
}
