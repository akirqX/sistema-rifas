<x-admin-layout>
    <div class="space-y-12">
        <div>
            <h1 class="text-3xl font-bold text-white">Dashboard</h1>
            <p class="text-text-muted mt-1">Visão geral do sistema.</p>
        </div>

        {{-- Componente 1: As Estatísticas e o Gráfico --}}
        @livewire('admin.dashboard-stats')

        {{-- Componente 2: O Gerenciamento de Rifas --}}
        @livewire('admin.raffles.manage-raffles')

        {{-- Componente 3: O Gerenciamento de Skins --}}
        @livewire('admin.skins.manage-skins')
    </div>
</x-admin-layout>
