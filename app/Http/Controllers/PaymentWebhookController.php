<?php
namespace App\Http\Controllers;

use App\Models\Order;
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

        // ==========================================================================
        // CORREÇÃO DEFINITIVA: Lidando com a inconsistência da ferramenta de teste do MP
        // ==========================================================================
        $paymentId = $request->input('data.id'); // Para webhooks REAIS

        // Se 'data.id' não existir, tenta pegar o 'id' do nível principal, que é o que a SIMULAÇÃO envia.
        if (!$paymentId && $request->input('id') && $request->input('action') === 'payment.updated') {
            $paymentId = $request->input('id');
            Log::info("Webhook de TESTE detectado. Usando ID principal: {$paymentId}");
        }

        if (!$paymentId) {
            Log::warning('Webhook recebido sem um ID de pagamento válido.');
            return response()->json(['status' => 'missing_payment_id'], 400);
        }

        try {
            $order = Order::where('transaction_id', $paymentId)->first();

            if (!$order) {
                Log::warning("Webhook para pagamento {$paymentId} recebido, mas nenhum pedido correspondente foi encontrado.");
                return response()->json(['status' => 'order_not_found'], 200);
            }
            if ($order->status === 'paid') {
                Log::info("Webhook para pagamento {$paymentId} ignorado: pedido #{$order->id} já está pago.");
                return response()->json(['status' => 'already_processed'], 200);
            }

            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $client = new PaymentClient();
            $payment = $client->get($paymentId);

            if ($payment && $payment->status === 'approved') {
                DB::transaction(function () use ($order) {
                    $order->update(['status' => 'paid']);
                    if ($order->raffle_id) {
                        $order->tickets()->update(['status' => 'paid']);
                    }
                    Log::info("SUCESSO: Pedido #{$order->id} (Transação {$order->transaction_id}) atualizado para PAGO.");
                });
            } else {
                Log::info("Pagamento {$paymentId} consultado, mas o status não é 'approved'. Status atual: " . ($payment->status ?? 'N/A'));
            }

        } catch (\Exception $e) {
            Log::error("Erro CRÍTICO no processamento do Webhook para o pagamento {$paymentId}: " . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
