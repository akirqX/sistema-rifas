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
use App\Livewire\Skins\IndexPage;
use App\Livewire\Skins\ShowPage;
use App\Livewire\Admin\Raffles\ManageTickets; // Adicione este 'use' no topo

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
require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| Rotas de Usuário Autenticado
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // --- A SOLUÇÃO ESTÁ AQUI ---
    // Estas linhas definem as rotas do painel do usuário.
    // Elas haviam sido removidas acidentalmente.
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/meus-pedidos', MyOrders::class)->name('my.orders');
    Route::get('/minhas-cotas', MyTickets::class)->name('my.tickets');
    Route::get('/meus-pedidos/{order}', OrderShowPage::class)->name('my.orders.show');
    Route::get('/profile', ProfileEditPage::class)->name('profile.edit');

    // Definição manual da rota de logout para garantir que ela exista.
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


/*
|--------------------------------------------------------------------------
| Rota do Painel de Administrador Unificado
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {
    // O nome desta rota é 'admin.dashboard'
    Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/admin/raffle/{raffle}/tickets', ManageTickets::class)->name('admin.raffles.tickets');
});


/*
|--------------------------------------------------------------------------
| Rotas de Serviço
|--------------------------------------------------------------------------
*/
Route::post('/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');
