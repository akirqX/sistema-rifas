<?php
namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class PaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Webhook received:', $request->all());
        $orderId = $request->input('order_id');
        $paymentStatus = $request->input('status');
        if (!$orderId || !$paymentStatus)
            return response()->json(['error' => 'Invalid payload'], 400);
        $order = Order::find($orderId);
        if (!$order)
            return response()->json(['error' => 'Order not found'], 404);
        if ($order->status !== 'pending')
            return response()->json(['message' => 'Order already processed']);
        if ($paymentStatus === 'approved') {
            $order->status = 'paid';
            $order->transaction_id = $request->input('transaction_id', 'N/A');
            $order->save();
            $order->tickets()->update(['status' => 'paid']);
            Log::info("Webhook: Payment for Order #{$orderId} confirmed.");
        }
        return response()->json(['status' => 'success']);
    }
}