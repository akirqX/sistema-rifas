<?php
namespace App\Livewire\User;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class MyOrders extends Component
{
    use WithPagination;

    public function render()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['raffle', 'product'])
            ->latest()
            ->paginate(10);

        return view('livewire.user.my-orders', [
            'orders' => $orders,
        ])->layout('layouts.app');
    }
}
