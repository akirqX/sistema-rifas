<?php

use Illuminate\Support\Facades\Route;

// Imports de Controllers
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentWebhookController;

// Imports de Componentes Livewire
use App\Livewire\HomePage;
use App\Livewire\RafflePage;
use App\Livewire\CheckoutPage;
use App\Livewire\Raffles\Showcase as RaffleShowcase;
use App\Livewire\Profile\EditPage as ProfileEditPage;
use App\Livewire\User\MyOrders;
use App\Livewire\User\MyTickets;
use App\Livewire\User\OrderShowPage;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Raffles\Index as AdminRafflesIndex;
use App\Livewire\Admin\Raffles\ManageTickets as AdminManageTickets;
use App\Livewire\Admin\Skins\Index as AdminSkinsIndex;
use App\Livewire\Skins\IndexPage;
use App\Livewire\Skins\ShowPage;


/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/', HomePage::class)->name('home');
Route::get('/rifas', RaffleShowcase::class)->name('raffles.showcase');
Route::get('/rifa/{raffle}', RafflePage::class)->name('raffle.show');
Route::get('/checkout/{raffle}', CheckoutPage::class)->name('checkout');
Route::get('/arsenal', IndexPage::class)->name('skins.index');
Route::get('/skin/{product}', ShowPage::class)->name('skins.show');


/*
|--------------------------------------------------------------------------
| Rotas de Autenticação
|--------------------------------------------------------------------------
*/
// Esta linha carrega as rotas de login, registro, etc.
require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| Rotas de Usuário Autenticado
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/meus-pedidos', MyOrders::class)->name('my.orders');
    Route::get('/minhas-cotas', MyTickets::class)->name('my.tickets');
    Route::get('/meus-pedidos/{order}', OrderShowPage::class)->name('my.orders.show');
    Route::get('/profile', ProfileEditPage::class)->name('profile.edit');

    // --- A SOLUÇÃO ESTÁ AQUI ---
    // Definimos manualmente a rota de logout para garantir que ela exista.
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


/*
|--------------------------------------------------------------------------
| Rotas de Administrador
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/rifas', AdminRafflesIndex::class)->name('raffles.index');
    Route::get('/rifa/{raffle}/cotas', AdminManageTickets::class)->name('raffles.tickets');
    Route::get('/skins', AdminSkinsIndex::class)->name('skins.index');
});


/*
|--------------------------------------------------------------------------
| Rotas de Serviço
|--------------------------------------------------------------------------
*/
Route::post('/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');
