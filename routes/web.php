<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentWebhookController;
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
        return redirect('/'); })->name('logout');
});

/* Rotas de Administrador */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.index')->name('dashboard');
    Route::get('/raffle/{raffle}/tickets', \App\Livewire\Admin\Raffles\ManageTickets::class)->name('raffles.tickets');
});
