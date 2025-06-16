<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

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
            session()->flash('error', 'Ocorreu um erro ao processar seu pedido. Por favor, selecione as cotas novamente.');
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
        // Se a propriedade $raffle não foi definida no mount (devido ao redirecionamento),
        // o método não continua. Esta é uma verificação de segurança mais robusta.
        if (!$this->raffle) {
            return;
        }

        if (!auth()->check()) {
            return $this->redirect(route('login'));
        }

        try {
            $order = DB::transaction(function () {
                $ticketsToReserve = Ticket::where('raffle_id', $this->raffle->id)
                    ->whereIn('number', $this->ticketNumbers)
                    ->where('status', 'available') // Garantia extra
                    ->lockForUpdate()->get();

                // Verifica se a quantidade de tickets encontrados corresponde à quantidade esperada.
                // Isso previne que alguém tenha comprado uma das cotas enquanto o usuário estava no checkout.
                if ($ticketsToReserve->count() !== $this->ticketCount) {
                    throw new \Exception("Uma ou mais cotas selecionadas não estão mais disponíveis. Por favor, tente novamente.");
                }

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'raffle_id' => $this->raffle->id,
                    'total_amount' => $this->totalAmount,
                    'status' => 'pending',
                ]);

                // Como a collection já está travada, podemos usar um update em massa.
                Ticket::whereIn('id', $ticketsToReserve->pluck('id'))->update([
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'status' => 'pending',
                ]);

                return $order;
            });

            session()->forget(['checkout_raffle_id', 'checkout_tickets']);

            session()->flash('success', 'Seu pedido foi criado com sucesso! Você pode acompanhá-lo em "Meus Pedidos".');
            return $this->redirect(route('my.orders'), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            // Opcional: redirecionar de volta para a rifa
            return $this->redirect(route('raffle.show', ['raffle' => $this->raffle->id]), navigate: true);
        }
    }

    public function render()
    {
        // A view só será renderizada se $this->raffle não for nulo, evitando erros.
        return view('livewire.checkout-page')->layout('layouts.app');
    }
}
