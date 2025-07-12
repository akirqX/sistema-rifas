<?php
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;
    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-6">
        <div>
            <label for="email" class="form-label">Email</label>
            <input wire:model="form.email" id="email" class="form-input" type="email" required autofocus />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>
        <div>
            <label for="password" class="form-label">Senha</label>
            <input wire:model="form.password" id="password" class="form-input" type="password" required />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>
        <div class="block">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="form-checkbox">
                <span class="ms-2 text-sm text-text-muted">Lembrar de mim</span>
            </label>
        </div>
        <div class="flex items-center justify-between">
            <a class="text-sm text-primary-light hover:underline" href="{{ route('password.request') }}" wire:navigate>
                Esqueceu sua senha?
            </a>
            <button type="submit" class="btn-prodgio btn-primary w-28" wire:loading.attr="disabled">
                <span wire:loading.remove>Entrar</span>
                <span wire:loading>Aguarde...</span>
            </button>
        </div>
    </form>
</div>
