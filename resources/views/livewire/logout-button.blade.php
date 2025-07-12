<?php
use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<a href="{{ route('logout') }}" onclick="event.preventDefault();" wire:click="logout" class="navbar-cta secondary !border-red-500 !text-red-400 hover:!bg-red-500 hover:!text-white"><span>Sair</span></a>
