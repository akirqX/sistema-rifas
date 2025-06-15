<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Raffles\Showcase as RaffleShowcase;
use App\Livewire\RafflePage;
use App\Livewire\CheckoutPage;
use App\Livewire\User\MyOrders;
use App\Livewire\User\MyTickets;
use App\Livewire\Admin\Raffles\Index as AdminRafflesIndex;
use App\Livewire\Admin\Raffles\ManageTickets as AdminManageTickets;
use App\Http\Controllers\PaymentWebhookController;

// ROTAS PÚBLICAS
Route::get('/', RaffleShowcase::class)->name('home');
Route::get('/rifas', RaffleShowcase::class)->name('raffles.showcase');
Route::get('/rifa/{raffle}', RafflePage::class)->name('raffle.show');
Route::get('/checkout/{raffle}', CheckoutPage::class)->name('checkout');

// ROTAS DE USUÁRIO LOGADO
Route::middleware('auth')->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
    Route::get('/meus-pedidos', MyOrders::class)->name('my.orders');
    Route::get('/minhas-cotas', MyTickets::class)->name('my.tickets');
});

// ROTAS DE ADMIN
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/rifas', AdminRafflesIndex::class)->name('raffles.index');
    Route::get('/rifa/{raffle}/cotas', AdminManageTickets::class)->name('raffles.tickets');
});

// ROTAS DE SERVIÇO
Route::post('/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');

// 👇👇👇 A LINHA QUE FALTAVA 👇👇👇
require __DIR__ . '/auth.php';
