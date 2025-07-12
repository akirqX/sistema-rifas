<?php
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);
        $validated['password'] = Hash::make($validated['password']);
        event(new Registered($user = User::create($validated)));
        Auth::login($user);
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register" class="space-y-6">
        <div>
            <label for="name" class="form-label">Nome</label>
            <input wire:model="name" id="name" class="form-input" type="text" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div>
            <label for="email" class="form-label">Email</label>
            <input wire:model="email" id="email" class="form-input" type="email" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <label for="password" class="form-label">Senha</label>
            <input wire:model="password" id="password" class="form-input" type="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div>
            <label for="password_confirmation" class="form-label">Confirmar Senha</label>
            <input wire:model="password_confirmation" id="password_confirmation" class="form-input" type="password" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-primary-light hover:underline" href="{{ route('login') }}" wire:navigate>
                Já possui uma conta?
            </a>
            <button type="submit" class="btn-prodgio btn-primary w-28" wire:loading.attr="disabled">
                <span wire:loading.remove>Registrar</span>
                <span wire:loading>Aguarde...</span>
            </button>
        </div>
    </form>
</div>
