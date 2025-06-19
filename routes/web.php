<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Imports de Controllers (Apenas os que realmente existem)
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ProfileController;

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
use App\Livewire\Admin\Raffles\ManageTickets;
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
// Carrega as rotas de login, registro, etc., do arquivo auth.php
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

    // --- A SOLUÇÃO FINAL ESTÁ AQUI ---
    // Definimos a rota de logout usando uma função direta (Closure).
    // Isso não depende de nenhum controller que possa estar faltando.
    Route::post('logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});


/*
|--------------------------------------------------------------------------
| Rotas de Administrador
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/raffle/{raffle}/tickets', ManageTickets::class)->name('raffles.tickets');
});


/*
|--------------------------------------------------------------------------
| Rotas de Serviço
|--------------------------------------------------------------------------
*/
Route::post('/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');
