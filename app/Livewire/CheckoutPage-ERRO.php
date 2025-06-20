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

    // Seu método mount original está perfeito.
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
        if (!$this->raffle || !auth()->check())
            return;

        // Limpamos o log para uma análise limpa
        Log::channel('single')->info('--- INICIANDO DIAGNÓSTICO DE PAGAMENTO ---');

        $order = null;
        try {
            DB::transaction(function () use (&$order) {
                $ticketsToReserve = Ticket::where('raffle_id', $this->raffle->id)
                    ->whereIn('number', $this->ticketNumbers)->where('status', 'available')
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
            });

            if (!$order) {
                throw new \Exception("Falha crítica: Pedido não foi criado no banco de dados.");
            }

            $accessToken = config('services.mercadopago.token');
            Log::channel('single')->info('Access Token encontrado: ' . ($accessToken ? 'Sim' : 'NÃO'));

            MercadoPagoConfig::setAccessToken($accessToken);
            $client = new PaymentClient();

            $paymentData = [
                "transaction_amount" => (float) $order->total_amount,
                "description" => "Pagamento para a rifa: " . $order->raffle->title,
                "payment_method_id" => "pix",
                "payer" => ["email" => auth()->user()->email, "first_name" => auth()->user()->name, "last_name" => 'Comprador', "identification" => ["type" => "CPF", "number" => "52998224725"]],
                "notification_url" => route('payment.webhook')
            ];

            Log::channel('single')->info('Enviando dados para o Mercado Pago:', $paymentData);

            $payment = $client->create($paymentData);

            // =========================================================
            // AQUI ESTÁ A BOMBA DE TINTA. O CÓDIGO VAI PARAR AQUI.
            // =========================================================
            dd($payment);

        } catch (MPApiException $e) {
            // =========================================================
            // SE A API DER ERRO, O CÓDIGO VAI PARAR AQUI.
            // =========================================================
            $errorResponse = $e->getApiResponse()->getContent();
            Log::channel('single')->error('ERRO DA API DO MERCADO PAGO:', (array) $errorResponse);
            dd($errorResponse);

        } catch (\Exception $e) {
            // =========================================================
            // SE QUALQUER OUTRO ERRO ACONTECER, VAI PARAR AQUI.
            // =========================================================
            Log::channel('single')->error('ERRO GERAL:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            dd($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.checkout-page')->layout('layouts.app');
    }
}
