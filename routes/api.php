<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentWebhookController; // Importe o controller

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rotas de API não têm a proteção CSRF por padrão, tornando este
| o local correto para webhooks externos.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// A ROTA DO WEBHOOK AGORA FICA AQUI.
//Route::post('/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');
