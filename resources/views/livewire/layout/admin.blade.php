<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-background-body">
            {{-- Inclui a barra de navegação superior, que é a mesma para todo o site --}}
            @include('layouts.navigation')

            {{-- A BARRA LATERAL DO ADMIN, AGORA EM UM LUGAR CENTRALIZADO --}}
            <aside id="admin-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-transform -translate-x-full bg-gray-900 border-r border-gray-700 sm:translate-x-0" aria-label="Sidebar">
                <div class="h-full px-3 py-4 overflow-y-auto bg-gray-900">
                    <ul class="space-y-2 font-medium">
                        <li>
                            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                <x-slot name="icon">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"></path></svg>
                                </x-slot>
                                <x-slot name="name">Dashboard</x-slot>
                            </x-responsive-nav-link>
                        </li>
                        <li>
                            <x-responsive-nav-link :href="route('admin.raffles.index')" :active="request()->routeIs('admin.raffles.*')">
                                <x-slot name="icon">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-12v.75m0 3v.75m0 3v.75m0 3V18m-3-12v.75m0 3v.75m0 3v.75m0 3V18m9-12v.75m0 3v.75m0 3v.75m0 3V18m-9-12v.75m0 3v.75m0 3v.75m0 3V18m-3-12v.75m0 3v.75m0 3v.75m0 3V18m0-12h18M3 18h18"/></svg>
                                </x-slot>
                                <x-slot name="name">Gerenciar Rifas</x-slot>
                            </x-responsive-nav-link>
                        </li>
                        <li>
                            <x-responsive-nav-link :href="route('admin.skins.index')" :active="request()->routeIs('admin.skins.*')">
                                <x-slot name="icon">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286Z" /></svg>
                                </x-slot>
                                <x-slot name="name">Gerenciar Skins</x-slot>
                            </x-responsive-nav-link>
                        </li>
                         <li>
                            <a href="{{ route('home') }}" target="_blank" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white group">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                                <span class="flex-1 ms-3 whitespace-nowrap">Painel Público</span>
                            </a>
                        </li>
                   </ul>
                </div>
             </aside>

            <!-- Conteúdo da Página do Admin -->
            <main class="p-4 sm:ml-64">
                <div class="mt-16">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
