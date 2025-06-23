<?php

// Manually bootstrap the Laravel application
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

// Carrega o autoloader do Composer e a aplicação Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Cria um request a partir dos globais do PHP para que possamos usar helpers
$request = Illuminate\Http\Request::capture();
$app->instance('request', $request); // Garante que o request esteja disponível

// Pega o conteúdo JSON enviado pelo Mercado Pago
$payload = json_decode($request->getContent(), true);

// Registra o recebimento no log
Log::info('Standalone Webhook Receiver Hit:', $payload);

// Validação mínima
if (!isset($payload['data']['id'])) {
    Log::warning('Webhook recebido sem data.id.');
    http_response_code(400);
    echo json_encode(['status' => 'missing_id']);
    exit;
}

$paymentId = $payload['data']['id'];

try {
    // Busca o pedido no banco de dados
    $order = Order::where('transaction_id', $paymentId)
        ->where('status', 'pending')
        ->first();

    if (!$order) {
        Log::info("Webhook para pagamento {$paymentId} ignorado: pedido não encontrado ou já processado.");
        http_response_code(200);
        echo json_encode(['status' => 'order_not_found']);
        exit;
    }

    // Consulta a API do Mercado Pago
    MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
    $client = new PaymentClient();
    $payment = $client->get($paymentId);

    // Se o pagamento foi aprovado, atualiza o sistema
    if ($payment && $payment->status === 'approved') {
        DB::transaction(function () use ($order) {
            $order->update(['status' => 'paid']);
            if ($order->raffle_id) {
                $order->tickets()->update(['status' => 'paid']);
            }
            Log::info("SUCESSO: Pedido #{$order->id} (Transação {$order->transaction_id}) atualizado para PAGO.");
        });
    }

    // Responde sucesso para o Mercado Pago
    http_response_code(200);
    echo json_encode(['status' => 'success']);

} catch (\Exception $e) {
    Log::error("Erro CRÍTICO no Standalone Webhook: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    http_response_code(500); // Retorna um erro para que o MP tente reenviar
    echo json_encode(['status' => 'error']);
}
