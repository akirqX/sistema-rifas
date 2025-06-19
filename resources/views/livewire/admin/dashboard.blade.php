<div class="space-y-6">
    {{-- Bloco de Boas-Vindas --}}
    <div class="p-4 sm:p-8 bg-panel-dark border border-border-subtle shadow rounded-lg">
        <div class="max-w-xl">
            <h2 class="text-lg font-medium text-white">
                Bem-vindo ao seu Painel de Administrador!
            </h2>
            <p class="mt-1 text-sm text-gray-400">
                Aqui você pode gerenciar todas as rifas e produtos do seu site.
            </p>
        </div>
    </div>

    {{-- Componente de Gerenciamento de Rifas (Já existe) --}}
    <div class="p-4 sm:p-8 bg-panel-dark border border-border-subtle shadow rounded-lg">
        <livewire:admin.raffles.index />
    </div>


    {{-- ========================================================== --}}
    {{-- COLE O NOVO BLOCO DE CÓDIGO EXATAMENTE AQUI              --}}
    {{-- ========================================================== --}}
    <div class="p-4 sm:p-8 bg-panel-dark border border-border-subtle shadow rounded-lg">
        {{-- Esta linha chama o nosso componente de gerenciamento de skins --}}
        <livewire:admin.skins.index />
    </div>
    {{-- ========================================================== --}}


</div>
