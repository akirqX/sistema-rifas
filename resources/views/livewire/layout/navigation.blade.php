<nav x-data="{ open: false }" class="bg-background-body border-b border-border-subtle">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <span class="font-heading text-2xl uppercase tracking-wider text-white">PRODGIO</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        Início
                    </x-nav-link>

                    <x-nav-link :href="route('raffles.showcase')" :active="request()->routeIs('raffles.showcase')">
                        Rifas
                    </x-nav-link>

                    <x-nav-link :href="route('skins.index')" :active="request()->routeIs('skins.index') || request()->routeIs('skins.show')">
                        Arsenal
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-800 hover:text-white focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            {{-- Link do Painel Admin (visível apenas para admins) --}}
                            @if(Auth::user()->is_admin)
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    Painel Admin
                                </x-dropdown-link>
                            @endif

                            {{-- Links de Usuário (visíveis para todos os logados) --}}
                            <x-dropdown-link :href="route('my.orders')">
                                Meus Pedidos
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">
                                Meu Perfil
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-300 hover:text-white">Entrar</a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-300 hover:text-white">Registrar</a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                Início
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('raffles.showcase')" :active="request()->routeIs('raffles.showcase')">
                Rifas
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('skins.index')" :active="request()->routeIs('skins.index')">
                Arsenal
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-600">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    @if(Auth::user()->is_admin)
                        <x-responsive-nav-link :href="route('admin.dashboard')">
                            Painel Admin
                        </x-responsive-nav-link>
                    @endif
                    <x-responsive-nav-link :href="route('my.orders')">
                        Meus Pedidos
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')">
                        Meu Perfil
                    </x-responsive-nav-link>
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        Entrar
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        Registrar
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
