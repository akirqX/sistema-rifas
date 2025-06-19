<?php

use Illuminate\Support\Facades\Route;

// Imports de Controllers
use App\Http\Controllers\Auth\AuthenticatedSessionController; // Importante para a rota de logout
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
    // Definimos manualmente a rota de logout para garantir que ela exista,
    // não importa o que aconteça no arquivo auth.php.
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


/*
|--------------------------------------------------------------------------
| Rota do Painel de Administrador Unificado
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
});


/*
|--------------------------------------------------------------------------
| Rotas de Serviço
|--------------------------------------------------------------------------
*/
Route::post('/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');
