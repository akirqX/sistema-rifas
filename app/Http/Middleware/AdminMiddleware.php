<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está logado E se a sua coluna 'is_admin' é true (1).
        if (auth()->check() && auth()->user()->is_admin) {
            // Se for admin, permite que a requisição continue para a rota de admin.
            return $next($request);
        }

        // Se não for admin, redireciona para o dashboard com uma mensagem de erro.
        return redirect()->route('dashboard')->with('error', 'Acesso negado. Você não tem permissão de administrador.');
    }
}
