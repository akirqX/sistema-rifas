<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Livewire\Raffles\Showcase as RaffleShowcase;
use App\Livewire\RafflePage;
use App\Livewire\CheckoutPage;
use App\Livewire\User\MyOrders;
use App\Livewire\User\MyTickets;
use App\Livewire\User\OrderShowPage;
use App\Livewire\Admin\Raffles\Index as AdminRafflesIndex;
use App\Livewire\Admin\Raffles\ManageTickets as AdminManageTickets;
use App\Http\Controllers\PaymentWebhookController;

// ROTAS PÚBLICAS E DE VISITANTES
Route::get('/', RaffleShowcase::class)->name('home');
Route::get('/rifas', RaffleShowcase::class)->name('raffles.showcase');
Route::get('/rifa/{raffle}', RafflePage::class)->name('raffle.show');
Route::get('/checkout/{raffle}', CheckoutPage::class)->name('checkout');

// ROTAS DE USUÁRIO LOGADO (EXIGE AUTENTICAÇÃO)
Route::middleware('auth')->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
    Route::get('/meus-pedidos', MyOrders::class)->name('my.orders');
    Route::get('/minhas-cotas', MyTickets::class)->name('my.tickets');
    Route::get('/meus-pedidos/{order}', OrderShowPage::class)->name('my.orders.show');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ROTAS DE ADMIN (EXIGE AUTENTICAÇÃO E PERMISSÃO DE ADMIN)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/rifas', AdminRafflesIndex::class)->name('admin.raffles.index');
    Route::get('/rifa/{raffle}/cotas', AdminManageTickets::class)->name('admin.raffles.tickets');
});

// ROTAS DE SERVIÇO
Route::post('/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');
