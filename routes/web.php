<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentWebhookController; // <--- Importado
use App\Livewire\HomePage;
use App\Livewire\RafflePage;
use App\Livewire\Raffles\Showcase as RaffleShowcase;
use App\Livewire\Profile\EditPage as ProfileEditPage;
use App\Livewire\User\MyOrders;
use App\Livewire\User\MyTickets;
use App\Livewire\User\OrderShowPage;
use App\Livewire\Skins\IndexPage;
use App\Livewire\Skins\ShowPage;

/* Rotas Públicas */
Route::get('/', HomePage::class)->name('home');
Route::get('/rifas', RaffleShowcase::class)->name('raffles.showcase');
Route::get('/rifa/{raffle}', RafflePage::class)->name('raffle.show');
Route::get('/arsenal', IndexPage::class)->name('skins.index');
Route::get('/skin/{product}', ShowPage::class)->name('skins.show');
Route::get('/pedido/{order:uuid}', OrderShowPage::class)->name('order.show');

// ======================================================================
// ROTA DO WEBHOOK ADICIONADA AQUI
// ======================================================================
Route::post('/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');


/* Rotas de Autenticação */
require __DIR__ . '/auth.php';

/* Rotas de Usuário Autenticado */
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/meus-pedidos', MyOrders::class)->name('my.orders');
    Route::get('/minhas-cotas', MyTickets::class)->name('my.tickets');
    Route::get('/profile', ProfileEditPage::class)->name('profile.edit');
    Route::post('logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

/* Rotas de Administrador */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.index')->name('dashboard');
    Route::get('/raffle/{raffle}/tickets', \App\Livewire\Admin\Raffles\ManageTickets::class)->name('raffles.tickets');
});


// Em routes/web.php

use App\Models\Order;

// Rota FINAL para simular um pagamento APROVADO para um pedido
Route::get('/dev/approve-order/{order:uuid}', function (Order $order) {
    if (!app()->isLocal()) {
        abort(404);
    }

    try {
        // Se o pedido não estiver pendente, não faz nada.
        if ($order->status !== 'pending') {
            return "O pedido #{$order->id} não está pendente (Status: {$order->status}). Nenhuma ação tomada.";
        }

        // CHAMA O MÉTODO DE APROVAÇÃO DIRETAMENTE, SEM WEBHOOK
        // Isso simula o resultado final de um webhook bem-sucedido.
        app('App\Http\Controllers\PaymentWebhookController')->handleApprovedPayment($order);

        return "SUCESSO: O pedido #{$order->id} foi forçado para o status 'paid'. Verifique o banco de dados.";

    } catch (\Exception $e) {
        return "Erro ao forçar aprovação: " . $e->getMessage();
    }
});
