<?php
namespace App\Livewire;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
class CheckoutPage extends Component
{
    public Raffle $raffle;
    public array $selectedTicketsData = [];
    public int $ticketCount = 0;
    public float $totalAmount = 0;

    // Campos do formulário
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $cpf = '';

    protected $rules = [
        'name' => 'required|string|min:3',
        'email' => 'required|email',
        'phone' => 'required|string', // Adicionar máscara/validação depois
        'cpf' => 'required|string',   // Adicionar máscara/validação depois
    ];

    public function mount(Raffle $raffle)
    {
        $this->raffle = $raffle;
        $selectedIds = Session::get('selected_tickets_for_' . $this->raffle->id, []);
        if (empty($selectedIds)) {
            return $this->redirect(route('raffle.show', $this->raffle), navigate: true);
        }
        $this->selectedTicketsData = Ticket::whereIn('id', $selectedIds)->where('status', 'available')->get()->toArray();
        if (count($this->selectedTicketsData) !== count($selectedIds)) {
            session()->flash('error', 'Ops! Algumas cotas foram reservadas enquanto você escolhia. Por favor, tente novamente.');
            return $this->redirect(route('raffle.show', $this->raffle), navigate: true);
        }
        $this->ticketCount = count($this->selectedTicketsData);
        $this->totalAmount = $this->ticketCount * $this->raffle->ticket_price;
    }

    public function processOrder()
    {
        $this->validate();
        $order = null;
        try {
            DB::transaction(function () use (&$order) {
                $ticketIds = array_column($this->selectedTicketsData, 'id');
                // Revalida as cotas para evitar race condition
                $ticketsToReserve = Ticket::whereIn('id', $ticketIds)->where('status', 'available')->lockForUpdate()->get();
                if ($ticketsToReserve->count() !== $this->ticketCount) {
                    throw new \Exception('Cotas não estão mais disponíveis.');
                }

                $order = Order::create([
                    'raffle_id' => $this->raffle->id,
                    'ticket_quantity' => $this->ticketCount,
                    'total_amount' => $this->totalAmount,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(10),
                    'guest_name' => $this->name,
                    'guest_email' => $this->email,
                    'guest_phone' => $this->phone,
                    'guest_cpf' => $this->cpf,
                ]);

                Ticket::whereIn('id', $ticketIds)->update([
                    'status' => 'reserved',
                    'order_id' => $order->id,
                ]);
            });

            // Limpa a sessão e redireciona
            Session::forget('selected_tickets_for_' . $this->raffle->id);
            // Aqui seria a integração com a página de pagamento real
            return redirect()->route('payment.page', ['order' => $order->id]);
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro ao processar seu pedido. Por favor, tente novamente.');
            return $this->redirect(route('raffle.show', $this->raffle), navigate: true);
        }
    }
    public function render()
    {
        return view('livewire.checkout-page')->layout('layouts.app');
    }
}
