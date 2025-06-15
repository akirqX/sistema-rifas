<?php

use App\Http\Controllers\PaymentWebhookController;
use App\Livewire\RafflePage;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

// As rotas de autenticação e perfil já são carregadas pelo Breeze

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('auth')->group(function () {
    // --- Nossas Rotas do Sistema de Rifas ---
    Route::get('/rifa/{raffle}', RafflePage::class)->name('raffle.show');

    Route::get('/admin/rifa/{raffle}/cotas', \App\Livewire\Admin\Raffles\ManageTickets::class)->name('admin.raffles.tickets');

    Route::get('/rifas', \App\Livewire\Raffles\Showcase::class)->name('raffles.showcase');

    // Rota para o painel de administração de rifas
    Route::get('/admin/rifas', \App\Livewire\Admin\Raffles\Index::class)->name('admin.raffles.index');

    Route::get('/pagamento/{order}', function (Order $order) {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        if ($order->status !== 'pending') {
            return "Este pedido não está mais pendente. Status: {$order->status}";
        }
        return "<h1>Pagar Pedido #{$order->id}</h1>
                <p>Total: R$ {$order->total_amount}</p>
                <p>Pague antes de: {$order->expires_at->format('d/m/Y H:i:s')} ({$order->expires_at->diffForHumans()})</p>
                <hr><h3>SIMULAÇÃO DE WEBHOOK</h3>
                <p>Para testar a baixa, envie um POST para <b>/webhook</b> com o JSON: <pre>{ \"order_id\": {$order->id}, \"status\": \"approved\" }</pre></p>";
    })->name('payment.page');
});

Route::get('/meus-pedidos', \App\Livewire\User\MyOrders::class)->name('my.orders');

Route::get('/admin/rifas', \App\Livewire\Admin\Raffles\Index::class)
    ->middleware(['auth', 'admin']) // Adicione 'admin' aqui
    ->name('admin.raffles.index');

// Webhook não precisa de autenticação
Route::post('/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');

require __DIR__ . '/auth.php';
