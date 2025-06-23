<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\PaymentWebhookController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // REGISTRANDO A ROTA DO WEBHOOK DIRETAMENTE AQUI
        // Isso a coloca fora dos grupos de middleware 'web' e 'api',
        // eliminando a proteção CSRF e outros problemas de forma definitiva.
        Route::post('/webhook', [PaymentWebhookController::class, 'handle'])
            ->name('payment.webhook');
    }
}
