<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class PromoteUserCommand extends Command
{
    /**
     * A assinatura do comando. É como você vai chamá-lo no terminal.
     * {email} é o argumento que vamos passar (o email do usuário).
     */
    protected $signature = 'user:promote {email}';

    /**
     * A descrição do comando.
     */
    protected $description = 'Promove um usuário existente para o status de administrador';

    /**
     * Executa a lógica do comando.
     */
    public function handle()
    {
        // Pega o email que foi passado como argumento.
        $email = $this->argument('email');

        // Procura o usuário no banco de dados.
        $user = User::where('email', $email)->first();

        // Se o usuário não for encontrado, exibe um erro e para a execução.
        if (!$user) {
            $this->error("Usuário com o email '{$email}' não foi encontrado.");
            return 1; // Retorna um código de erro
        }

        // Se o usuário já for um admin, avisa e não faz nada.
        if ($user->is_admin) {
            $this->warn("O usuário '{$user->name}' já é um administrador.");
            return 0;
        }

        // Atualiza a coluna 'is_admin' para true (1) e salva.
        $user->is_admin = true;
        $user->save();

        // Exibe uma mensagem de sucesso no terminal.
        $this->info("Sucesso! O usuário '{$user->name}' ({$user->email}) foi promovido a administrador.");

        return 0; // Retorna um código de sucesso
    }
}
