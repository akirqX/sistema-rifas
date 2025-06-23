<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * As URIs que devem ser excluídas da verificação CSRF.
     *
     * @var array<int, string>
     */
    protected $except = [
        // ==========================================================================
        // A CORREÇÃO DEFINITIVA: Adicionamos a nossa rota de webhook aqui.
        // Isso diz ao Laravel para não exigir um token de segurança para esta rota.
        // ==========================================================================
        '/webhook',
    ];
}
