<?php
namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\PaymentWebhookController;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // REGISTRA A ROTA DO WEBHOOK FORA DE QUALQUER GRUPO DE MIDDLEWARE
        Route::post('/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');
    }
}
