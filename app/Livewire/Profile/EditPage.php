<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Http\Request;

class EditPage extends Component
{
    /**
     * Renderiza a view do componente de perfil, usando o layout principal da aplicação.
     */
    public function render()
    {
        return view('livewire.profile.edit-page', [
            'user' => auth()->user(), // Passa o usuário para as sub-views
        ])->layout('layouts.app');
    }
}
