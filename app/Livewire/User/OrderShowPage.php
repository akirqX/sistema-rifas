<?php
namespace App\Livewire\User;

use App\Models\Order;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;
use Carbon\Carbon;

class OrderShowPage extends Component
{
    public Order $order;
    public ?string $qrCodeBase64 = null;
    public ?string $qrCodeCopyPaste = null;
    public ?Carbon $expiresAt = null;
    public bool $hasError = false;
    public string $errorMessage = '';

    public function mount(Order $order)
    {
        if ($order->user_id && auth()->id() !== $order->user_id) {
            abort(403);
        }
        $this->order = $order->load('raffle.media', 'tickets', 'user');
        if ($this->order->status === 'pending') {
            if ($this->order->transaction_id && !empty($this->order->payment_details['qr_code_base64'])) {
                $this->loadPaymentDetails();
            } else {
                $this->generateMercadoPagoPayment();
            }
        }
    }

    public function generateMercadoPagoPayment()
    {
        if ($this->order->raffle?->status !== 'active') {
            $this->showError('Esta rifa não está mais ativa.');
            return;
        }
        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $paymentClient = new PaymentClient();

            // CORREÇÃO: Gerando a URL simples, pois a rota agora está no AppServiceProvider
            $notificationUrl = url('/webhook');
            if (App::environment('local') && config('services.mercadopago.ngrok_url')) {
                $notificationUrl = config('services.mercadopago.ngrok_url') . '/webhook';
            }

            $payerEmail = optional($this->order->user)->email ?? $this->order->guest_email;
            $payerName = optional($this->order->user)->name ?? $this->order->guest_name;
            $payerDoc = optional($this->order->user)->document_number ?? '00000000000';

            $paymentData = ["transaction_amount" => (float) round($this->order->total_amount, 2), "description" => "Pagamento para: " . $this->order->raffle->title, "payment_method_id" => "pix", "payer" => ["email" => $payerEmail, "first_name" => $payerName, "identification" => ["type" => "CPF", "number" => $payerDoc]], "notification_url" => $notificationUrl];
            $payment = $paymentClient->create($paymentData);

            $this->order->update(['transaction_id' => $payment->id, 'payment_gateway' => 'mercadopago', 'payment_details' => ['qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64, 'qr_code' => $payment->point_of_interaction->transaction_data->qr_code], 'expires_at' => Carbon::parse($payment->date_of_expiration)]);
            $this->loadPaymentDetails();
        } catch (MPApiException $e) {
            $this->handlePaymentGenerationError($e, 'Ocorreu um erro com o gateway de pagamento: ' . ($e->getApiResponse()->getContent()['message'] ?? $e->getMessage()));
        } catch (\Exception $e) {
            $this->handlePaymentGenerationError($e, 'Ocorreu um erro inesperado.');
        }
    }

    protected function handlePaymentGenerationError(\Exception $e, string $userMessage)
    {
        Log::error("Payment Generation Error for Order #{$this->order->id}", ['message' => $e->getMessage()]);
        $this->order->update(['status' => 'failed']);
        $this->showError($userMessage);
    }
    public function loadPaymentDetails()
    {
        $this->order->refresh();
        $this->qrCodeBase64 = $this->order->payment_details['qr_code_base64'] ?? null;
        $this->qrCodeCopyPaste = $this->order->payment_details['qr_code'] ?? null;
        $this->expiresAt = $this->order->expires_at;
    }
    protected function showError(string $message)
    {
        $this->hasError = true;
        $this->errorMessage = $message;
    }
    public function checkPaymentStatus()
    {
        $this->order->refresh();
    }
    public function render()
    {
        return view('livewire.user.order-show-page')->layout('layouts.app');
    }
}
