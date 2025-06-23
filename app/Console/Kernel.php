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
        // CORREÇÃO: Rodando o comando a cada minuto para liberar cotas rapidamente.
        $schedule->command('tickets:release-expired')->everyMinute();

        // Seus outros comandos podem continuar aqui, sem problemas.
        $schedule->command('payments:reconcile --days=1')->hourly();
        $schedule->command('payments:reconcile --days=7')->daily();
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
