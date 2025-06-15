<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;

// Define a ação de logout que pode ser chamada com wire:click
$logout = function (Logout $logout) {
    $logout();
};

?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('raffles.showcase')" :active="request()->routeIs('raffles.showcase') || request()->routeIs('raffle.show')" wire:navigate>
                        {{ __('Rifas') }}
                    </x-nav-link>

                    @auth
                        @if (Auth::user()->is_admin)
                            <x-nav-link :href="route('admin.raffles.index')" :active="request()->routeIs('admin.*')" wire:navigate>
                                {{ __('Admin') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Menus da Direita (Convidado vs Logado) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @guest
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600" wire:navigate>Log in</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" wire:navigate>Register</a>
                    </div>
                @endguest

                @auth
                    <!-- Settings Dropdown para Usuário Logado -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div x-data="{ name: '{{ auth()->user()->name }}' }" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                                <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard')" wire:navigate>{{ __('Dashboard') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('my.orders')" wire:navigate>{{ __('Meus Pedidos') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('my.tickets')" wire:navigate>{{ __('Minhas Cotas') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('profile')" wire:navigate>{{ __('Profile') }}</x-dropdown-link>
                            <div class="border-t border-gray-200"></div>
                            <!-- Logout Button (FORMA CORRETA PARA LIVEWIRE/VOLT) -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>


            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /><path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('raffles.showcase')" :active="request()->routeIs('raffles.showcase')" wire:navigate>
                {{ __('Rifas') }}
            </x-responsive-nav-link>
        </div>

        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" x-data="{ name: '{{ auth()->user()->name }}' }" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" wire:navigate>{{ __('Dashboard') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('my.orders')" wire:navigate>{{ __('Meus Pedidos') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('my.tickets')" wire:navigate>{{ __('Minhas Cotas') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile')" wire:navigate>{{ __('Profile') }}</x-responsive-nav-link>
                <div class="border-t border-gray-200"></div>
                <!-- Logout no menu responsivo -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
        @else
        <div class="py-1 border-t border-gray-200">
             <x-responsive-nav-link :href="route('login')" wire:navigate>{{ __('Log in') }}</x-responsive-nav-link>
             <x-responsive-nav-link :href="route('register')" wire:navigate>{{ __('Register') }}</x-responsive-nav-link>
        </div>
        @endguest
    </div>
</nav>
