<?php
namespace App\Console\Commands;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Console\Command;
class ReleaseExpiredTickets extends Command
{
    protected $signature = 'tickets:release-expired';
    protected $description = 'Find pending orders that have expired and release their tickets';
    public function handle()
    {
        $this->info('Searching for expired orders...');
        $expiredOrders = Order::where('status', 'pending')->where('expires_at', '<=', now())->get();
        if ($expiredOrders->isEmpty()) {
            $this->info('No expired orders found.');
            return;
        }
        foreach ($expiredOrders as $order) {
            $order->update(['status' => 'expired']);
            Ticket::where('order_id', $order->id)->update(['status' => 'available', 'order_id' => null, 'user_id' => null]);
            $this->info("Order #{$order->id} expired. Tickets released.");
        }
        $this->info('Finished releasing tickets.');
    }
}