<?php

namespace App\Livewire\Profile;

use App\Http\Requests\ProfileUpdateRequest;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Edit extends Component
{
    public function render()
    {
        return view('livewire.profile.edit')->layout('layouts.app');
    }
}
