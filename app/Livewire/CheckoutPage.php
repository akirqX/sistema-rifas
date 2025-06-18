<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

class CheckoutPage extends Component
{
    public ?Raffle $raffle = null;
    public array $ticketNumbers = [];
    public float $totalAmount = 0;
    public int $ticketCount = 0;

    public function mount(Raffle $raffle)
    {
        $raffleId = session('checkout_raffle_id');
        $ticketNumbers = session('checkout_tickets');

        if (!$raffleId || empty($ticketNumbers) || $raffleId != $raffle->id) {
            session()->flash('error', 'Ocorreu um erro. Por favor, selecione as cotas novamente.');
            $this->redirect(route('raffles.showcase'), navigate: true);
            return;
        }

        $this->raffle = $raffle;
        $this->ticketNumbers = $ticketNumbers;
        $this->ticketCount = count($ticketNumbers);
        $this->totalAmount = $this->raffle->price * $this->ticketCount;
    }

    public function createOrder()
    {
        if (!$this->raffle) {
            return;
        }
        if (!auth()->check()) {
            return $this->redirect(route('login'));
        }

        $order = null;

        try {
            $order = DB::transaction(function () {
                $ticketsToReserve = Ticket::where('raffle_id', $this->raffle->id)
                    ->whereIn('number', $this->ticketNumbers)
                    ->where('status', 'available')
                    ->lockForUpdate()->get();

                if ($ticketsToReserve->count() !== $this->ticketCount) {
                    throw new \Exception("Uma ou mais cotas selecionadas não estão mais disponíveis.");
                }

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'raffle_id' => $this->raffle->id,
                    'total_amount' => $this->totalAmount,
                    'status' => 'pending',
                    'ticket_quantity' => $this->ticketCount,
                    'expires_at' => now()->addMinutes(15),
                ]);

                Ticket::whereIn('id', $ticketsToReserve->pluck('id'))->update([
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'status' => 'reserved',
                ]);

                return $order;
            });

            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $client = new PaymentClient();

            // CORREÇÃO: Lógica para determinar a URL do Webhook
            $notificationUrl = route('payment.webhook');
            if (config('app.env') === 'local' && config('services.mercadopago.ngrok_url')) {
                $notificationUrl = config('services.mercadopago.ngrok_url') . '/webhook';
            }

            $paymentData = [
                "transaction_amount" => (float) $order->total_amount,
                "description" => "Pagamento para a rifa: " . $order->raffle->title,
                "payment_method_id" => "pix",
                "payer" => [
                    "email" => auth()->user()->email,
                    "first_name" => auth()->user()->name,
                    "last_name" => 'Comprador',
                    "identification" => [
                        "type" => "CPF",
                        "number" => "52998224725"
                    ],
                ],
                "notification_url" => $notificationUrl
            ];

            $payment = $client->create($paymentData);

            $order->update([
                'transaction_id' => $payment->id,
                'payment_gateway' => 'mercadopago',
                'payment_details' => json_encode([
                    'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                    'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
                ])
            ]);

            session()->forget(['checkout_raffle_id', 'checkout_tickets']);

            return $this->redirect(route('my.orders.show', $order), navigate: true);

        } catch (MPApiException $e) {
            $errorBody = $e->getApiResponse()->getContent();
            Log::error("Mercado Pago API Error: " . $e->getMessage(), ['response' => $errorBody]);
            $errorMessage = $errorBody['message'] ?? 'Erro desconhecido na API.';
            session()->flash('error', 'Ops! Gateway de Pagamento recusou a transação: ' . $errorMessage);
            return $this->redirect(route('raffle.show', ['raffle' => $this->raffle->id]), navigate: true);

        } catch (\Exception $e) {
            Log::error("General Error during order creation: " . $e->getMessage());
            session()->flash('error', 'Erro ao criar pedido: ' . $e->getMessage());
            if ($order) { /* Lógica de reversão se necessário */
            }
            return $this->redirect(route('raffle.show', ['raffle' => $this->raffle->id]), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.checkout-page')->layout('layouts.app');
    }
}
