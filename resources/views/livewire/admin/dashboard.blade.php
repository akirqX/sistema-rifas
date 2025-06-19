<div class="container mx-auto px-4 py-8 sm:py-12">
    <div class="space-y-8">
        {{-- Bloco de Boas-Vindas --}}
        <div class="p-4 sm:p-8 bg-panel-dark border border-border-subtle shadow rounded-lg">
            <div class="max-w-xl">
                <h2 class="text-lg font-medium text-white">
                    Painel de Administrador PRODGIO
                </h2>
                <p class="mt-1 text-sm text-gray-400">
                    Gerencie todo o seu sistema a partir de um único lugar.
                </p>
            </div>
        </div>

        {{-- Componente de Gerenciamento de Rifas --}}
        <div class="p-4 sm:p-8 bg-panel-dark border border-border-subtle shadow rounded-lg">
            <livewire:admin.raffles.index />
        </div>

        {{-- Componente de Gerenciamento de Skins --}}
        <div class="p-4 sm:p-8 bg-panel-dark border border-border-subtle shadow rounded-lg">
            <livewire:admin.skins.index />
        </div>
    </div>
</div>
