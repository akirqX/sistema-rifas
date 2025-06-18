<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Profile\EditPage as ProfileEditPage;
use App\Livewire\Raffles\Showcase as RaffleShowcase;
use App\Livewire\RafflePage;
use App\Livewire\CheckoutPage;
use App\Livewire\User\MyOrders;
use App\Livewire\User\MyTickets;
use App\Livewire\User\OrderShowPage;
use App\Livewire\Admin\Raffles\Index as AdminRafflesIndex;
use App\Livewire\Admin\Raffles\ManageTickets as AdminManageTickets;
use App\Http\Controllers\PaymentWebhookController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/', RaffleShowcase::class)->name('home');
Route::get('/rifas', RaffleShowcase::class)->name('raffles.showcase');
Route::get('/rifa/{raffle}', RafflePage::class)->name('raffle.show');
Route::get('/checkout/{raffle}', CheckoutPage::class)->name('checkout');

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Rotas de Usuário Autenticado
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Rota para visualizar o perfil
    Route::get('/profile', ProfileEditPage::class)->name('profile.edit');

    // As rotas de ação continuam apontando para o ProfileController
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rota de Logout definida manualmente
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/meus-pedidos', MyOrders::class)->name('my.orders');
    Route::get('/minhas-cotas', MyTickets::class)->name('my.tickets');
    Route::get('/meus-pedidos/{order}', OrderShowPage::class)->name('my.orders.show');
});

/*
|--------------------------------------------------------------------------
| Rotas de Administrador
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/rifas', AdminRafflesIndex::class)->name('admin.raffles.index');
    Route::get('/rifa/{raffle}/cotas', AdminManageTickets::class)->name('admin.raffles.tickets');
});

/*
|--------------------------------------------------------------------------
| Rotas de Serviço
|--------------------------------------------------------------------------
*/
Route::post('/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');
