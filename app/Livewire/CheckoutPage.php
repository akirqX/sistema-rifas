<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

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

        try {
            // A transação do DB agora retorna o pedido criado
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

            // --- INÍCIO DA INTEGRAÇÃO COM A API DO MERCADO PAGO ---

            // 1. Configura a SDK com seu Access Token do arquivo .env
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));

            // 2. Cria a requisição de pagamento
            $client = new PaymentClient();
            $paymentData = [
                "transaction_amount" => $order->total_amount,
                "description" => "Pagamento para a rifa: " . $order->raffle->title,
                "payment_method_id" => "pix",
                "payer" => [
                    "email" => auth()->user()->email,
                    "first_name" => auth()->user()->name,
                ],
                // URL que o Mercado Pago vai chamar para te notificar sobre o status do pagamento
                "notification_url" => route('payment.webhook')
            ];

            // 3. Envia a requisição para a API do Mercado Pago
            $payment = $client->create($paymentData);

            // 4. Salva os dados importantes do PIX no seu banco de dados
            $order->update([
                'transaction_id' => $payment->id, // ID da transação no Mercado Pago
                'payment_gateway' => 'mercadopago',
                'payment_details' => json_encode([ // Salva como JSON para flexibilidade
                    'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                    'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
                ])
            ]);

            // --- FIM DA INTEGRAÇÃO ---

            session()->forget(['checkout_raffle_id', 'checkout_tickets']);

            // Redireciona o usuário para a página do pedido, onde ele verá o QR Code
            return $this->redirect(route('my.orders.show', $order), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao criar pedido: ' . $e->getMessage());
            return $this->redirect(route('raffle.show', ['raffle' => $this->raffle->id]), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.checkout-page')->layout('layouts.app');
    }
}
