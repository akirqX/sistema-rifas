<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // Este é o componente "Pai", então ele define o layout da página.
        return view('livewire.admin.dashboard')
            ->layout('layouts.app');
    }
}
