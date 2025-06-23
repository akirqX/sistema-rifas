<?php
namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;
use Carbon\Carbon;

class CheckoutPage extends Component
{
    public ?Order $order = null;
    public ?string $qrCodeBase64 = null;
    public ?string $qrCodeCopyPaste = null;
    public ?Carbon $expiresAt = null;
    public string $paymentStatus = 'pending';
    public $item;
    public string $type;
    public array $details = [];
    public float $totalAmount = 0;
    public int $quantity = 0;
    public bool $hasError = false;
    public string $errorMessage = '';

    public function mount()
    {
        $checkoutData = session('checkout_data');
        if (!$checkoutData) {
            $this->showError('Sua sessão de compra expirou ou já foi processada.');
            return;
        }

        session()->forget('checkout_data');

        try {
            $this->type = $checkoutData['type'];
            $this->details = $checkoutData['details'];
            if ($this->type === 'raffle') {
                $this->item = Raffle::findOrFail($checkoutData['item_id']);
                $this->quantity = count($this->details['tickets']);
                $this->totalAmount = $this->item->ticket_price * $this->quantity;
            } else {
                throw new \Exception('Tipo de compra inválido.');
            }

            $this->createOrderAndReserveTickets();
            $this->generateMercadoPagoPayment();

        } catch (\Exception $e) {
            Log::error("Checkout Critical Error", ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($this->order) {
                Ticket::where('order_id', $this->order->id)->update(['status' => 'available', 'order_id' => null, 'user_id' => null]);
                $this->order->update(['status' => 'failed']);
            }
            $this->showError($e->getMessage());
        }
    }

    protected function createOrderAndReserveTickets()
    {
        $this->order = DB::transaction(function () {
            $userId = Auth::id();
            $orderExpiresAt = now('America/Sao_Paulo')->addMinutes(30);
            $ticketsToReserve = Ticket::where('raffle_id', $this->item->id)->whereIn('number', $this->details['tickets'])->lockForUpdate()->get();
            if ($ticketsToReserve->where('status', 'available')->count() !== $this->quantity) {
                throw new \Exception("Uma ou mais cotas selecionadas não estão mais disponíveis.");
            }
            $createdOrder = Order::create(['user_id' => $userId, 'raffle_id' => $this->item->id, 'ticket_quantity' => $this->quantity, 'total_amount' => $this->totalAmount, 'status' => 'pending', 'expires_at' => $orderExpiresAt]);
            Ticket::whereIn('id', $ticketsToReserve->pluck('id'))->update(["status" => "reserved", "order_id" => $createdOrder->id, "user_id" => $userId]);
            return $createdOrder;
        });
    }

    protected function generateMercadoPagoPayment()
    {
        if (!$this->order)
            return;
        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $paymentClient = new PaymentClient();
            $notificationUrl = App::environment('local') && config('services.mercadopago.ngrok_url') ? config('services.mercadopago.ngrok_url') . '/webhook' : route('payment.webhook');
            $paymentData = ["transaction_amount" => (float) round($this->order->total_amount, 2), "description" => "Pagamento para: " . ($this->item->title ?? $this->item->name), "payment_method_id" => "pix", "payer" => ["email" => auth()->user()->email, "first_name" => auth()->user()->name, "identification" => ["type" => "CPF", "number" => auth()->user()->document_number ?? '00000000000']], "notification_url" => $notificationUrl];
            $payment = $paymentClient->create($paymentData);
            $this->order->update(['transaction_id' => $payment->id, 'payment_gateway' => 'mercadopago', 'payment_details' => json_encode(['qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64, 'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,])]);
            $this->qrCodeBase64 = $payment->point_of_interaction->transaction_data->qr_code_base64;
            $this->qrCodeCopyPaste = $payment->point_of_interaction->transaction_data->qr_code;
            $this->expiresAt = Carbon::parse($payment->date_of_expiration);
            $this->paymentStatus = $this->order->status;
        } catch (MPApiException $e) {
            $apiResponse = $e->getApiResponse()->getContent();
            throw new \Exception('Ocorreu um erro com o gateway de pagamento: ' . ($apiResponse['message'] ?? $e->getMessage()));
        }
    }

    public function checkPaymentStatus()
    { /*...*/
    }
    protected function showError(string $message)
    {
        $this->hasError = true;
        $this->errorMessage = $message;
    }
    public function render()
    {
        return view('livewire.checkout-page')->layout('layouts.app');
    }
}
