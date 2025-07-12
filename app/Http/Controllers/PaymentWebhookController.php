<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use Exception;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Webhook Recebido:', $request->all());

        // MELHORIA 1: Validação de Segurança (Assinatura do Webhook)
        // Isso garante que a requisição veio 100% do Mercado Pago.
        $secret = config('services.mercadopago.webhook_secret');
        if ($secret) {
            $signatureHeader = $request->header('x-signature');
            if (!$this->isValidSignature($signatureHeader, $request->getContent(), $secret)) {
                Log::warning('Webhook ignorado: Assinatura inválida.');
                return response()->json(['status' => 'invalid_signature'], 401);
            }
        }

        // Validação básica do corpo da requisição
        $paymentId = $request->input('data.id');
        if (!$paymentId) {
            Log::warning('Webhook ignorado: data.id ausente.');
            return response()->json(['status' => 'missing_payment_id'], 400);
        }

        try {
            // Busca o pedido ANTES de ir para a API do MP. É mais eficiente.
            // Se o pedido não existe, não gastamos uma chamada de API.
            $order = Order::with('product', 'tickets')->where('transaction_id', $paymentId)->first();

            if (!$order) {
                Log::info("Webhook ignorado: Pedido com transaction_id {$paymentId} não encontrado no banco.");
                return response()->json(['status' => 'order_not_found']); // Retorna 200 para o MP parar de enviar.
            }

            // Se o pedido já foi processado (pago, falhou, etc.), não faz nada.
            if ($order->status !== 'pending') {
                Log::info("Webhook ignorado: Pedido #{$order->id} já foi processado (Status: {$order->status}).");
                return response()->json(['status' => 'order_already_processed']);
            }

            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $payment = (new PaymentClient())->get($paymentId);

            if (!$payment) {
                Log::error("API MP: Falha ao buscar detalhes do pagamento ID: {$paymentId}");
                return response()->json(['status' => 'payment_not_found_in_api'], 404);
            }
            Log::info("Status do pagamento {$paymentId} na API: " . $payment->status);

            DB::transaction(function () use ($payment, $order) {
                if ($payment->status === 'approved') {
                    $this->handleApprovedPayment($order);
                }
                // MELHORIA 2: Tratamento explícito de falhas e cancelamentos
                elseif (in_array($payment->status, ['rejected', 'cancelled', 'refunded', 'charged_back'])) {
                    $this->handleFailedPayment($order, $payment->status);
                }
                // Para outros status como 'pending' ou 'in_process', não fazemos nada e deixamos expirar.
            });

        } catch (Exception $e) {
            Log::error("Erro CRÍTICO no Webhook ID {$paymentId}: " . $e->getMessage(), ['trace' => substr($e->getTraceAsString(), 0, 2000)]);
            return response()->json(['status' => 'internal_error'], 500);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Processa um pagamento que foi confirmado como APROVADO.
     */
    protected function handleApprovedPayment(Order $order): void
    {
        $order->update(['status' => 'paid']);
        if ($order->raffle_id) {
            $order->tickets()->update(['status' => 'paid']);
        }
        Log::info("SUCESSO: Pedido #{$order->id} atualizado para PAGO.");
    }

    /**
     * Processa um pagamento que FALHOU, foi CANCELADO ou REJEITADO.
     */
    protected function handleFailedPayment(Order $order, string $failedStatus): void
    {
        $order->update(['status' => $failedStatus]);

        if ($order->raffle_id) {
            $order->tickets()->delete(); // Libera as cotas imediatamente
            Log::info("FALHA: Pedido de Rifa #{$order->id} falhou com status '{$failedStatus}'. Tickets deletados.");
        }
        if ($order->product_id && $order->product) {
            $order->product->increment('quantity'); // Devolve o produto ao estoque imediatamente
            Log::info("FALHA: Pedido de Produto #{$order->id} falhou com status '{$failedStatus}'. Estoque devolvido.");
        }
    }

    /**
     * MELHORIA 3: Método para validar a assinatura do webhook (Segurança)
     */
    private function isValidSignature(?string $signatureHeader, string $payload, string $secret): bool
    {
        if (!$signatureHeader) {
            return false;
        }

        $parts = explode(',', $signatureHeader);
        $timestamp = null;
        $signature = null;

        foreach ($parts as $part) {
            [$key, $value] = explode('=', $part, 2);
            if ($key === 'ts') {
                $timestamp = $value;
            } elseif ($key === 'v1') {
                $signature = $value;
            }
        }

        if (!$timestamp || !$signature) {
            return false;
        }

        $manifest = "id:{$this->getPaymentIdFromPayload($payload)};ts:{$timestamp};";
        $expectedSignature = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    private function getPaymentIdFromPayload(string $payload): ?string
    {
        $data = json_decode($payload, true);
        return $data['data']['id'] ?? null;
    }
}
