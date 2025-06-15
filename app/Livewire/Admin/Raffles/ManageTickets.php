<?php

namespace App\Livewire\Admin\Raffles;

use App\Models\Raffle;
use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Collection;

class ManageTickets extends Component
{
    use WithPagination;

    public Raffle $raffle;

    // Propriedades para filtros
    public string $status = 'all';
    public string $search = '';

    // Propriedade para rastrear as cotas selecionadas
    public array $selectedTickets = [];

    public function mount(Raffle $raffle)
    {
        $this->raffle = $raffle;
    }

    // Ação para liberar as cotas selecionadas
    public function releaseSelected(): void
    {
        if (empty($this->selectedTickets)) {
            session()->flash('error', 'Nenhuma cota selecionada.');
            return;
        }

        Ticket::whereIn('id', $this->selectedTickets)->update([
            'status' => 'available',
            'user_id' => null,
            'order_id' => null,
        ]);

        session()->flash('success', count($this->selectedTickets) . ' cotas foram liberadas com sucesso.');
        $this->reset('selectedTickets'); // Limpa a seleção
    }

    // Propriedade computada para controlar o "Selecionar Tudo"
    public function getSelectAllProperty(): bool
    {
        return count($this->selectedTickets) === $this->getTicketsOnPage()->count();
    }

    // Lida com a seleção de todos os itens na página atual
    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedTickets = $this->getTicketsOnPage()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->reset('selectedTickets');
        }
    }

    // Helper para obter os tickets da página atual
    protected function getTicketsOnPage(): Collection
    {
        return $this->buildTicketsQuery()->paginate(100)->getCollection();
    }

    // Centraliza a lógica da query para reutilização
    protected function buildTicketsQuery()
    {
        $ticketsQuery = $this->raffle->tickets()->with('user');

        if ($this->status !== 'all') {
            $ticketsQuery->where('status', $this->status);
        }

        if (!empty($this->search)) {
            $ticketsQuery->where(function ($query) {
                $query->where('number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        return $ticketsQuery->orderBy('number');
    }

    public function render()
    {
        $tickets = $this->buildTicketsQuery()->paginate(100);

        return view('livewire.admin.raffles.manage-tickets', [
            'tickets' => $tickets,
        ])->layout('layouts.app');
    }
}
