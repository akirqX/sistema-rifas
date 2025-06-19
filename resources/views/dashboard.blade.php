<x-app-layout>
    {{-- ========================================================== --}}
    {{-- LÓGICA DE REDIRECIONAMENTO INTELIGENTE                     --}}
    {{-- ========================================================== --}}
    @auth
        @if(auth()->user()->is_admin)
            {{-- Se o usuário for admin, este script o redireciona para o painel de admin --}}
            <script>
                window.location = "{{ route('admin.dashboard') }}";
            </script>
        @endif
    @endauth
    {{-- ========================================================== --}}

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Painel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-panel-dark overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">
                    {{ __("Você está logado!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
