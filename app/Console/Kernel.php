<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ======================================================================
        // CORREÇÃO: Apontando para o novo comando que lida com rifas E produtos.
        // ======================================================================
        $schedule->command('orders:cleanup-expired')->everyMinute();

        // Seus outros comandos podem continuar aqui, sem problemas.
        // Nota: O comando 'payments:reconcile' não existe no seu projeto ainda,
        // mas o deixei aqui pois estava no seu arquivo original.
        // Se ele não existir, você pode remover ou comentar as duas linhas abaixo.
        // $schedule->command('payments:reconcile --days=1')->hourly();
        // $schedule->command('payments:reconcile --days=7')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
