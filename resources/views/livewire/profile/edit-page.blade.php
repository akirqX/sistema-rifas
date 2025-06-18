<div>
    {{-- Como estamos usando um layout no componente, não precisamos do <x-app-layout> --}}

    <div class="py-12 pt-32"> {{-- pt-32 para dar espaço para o header fixo --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Card de Informações do Perfil --}}
            <div class="p-4 sm:p-8 bg-bg-secondary border border-border shadow-lg rounded-2xl">
                <div class="max-w-xl">
                    {{-- O @include agora está dentro de um contexto Livewire, o que resolve o erro --}}
                    @include('livewire.profile.update-profile-information-form')
                </div>
            </div>

            {{-- Card de Atualização de Senha --}}
            <div class="p-4 sm:p-8 bg-bg-secondary border border-border shadow-lg rounded-2xl">
                <div class="max-w-xl">
                    @include('livewire.profile.update-password-form')
                </div>
            </div>

            {{-- Card para Deletar Conta --}}
            <div class="p-4 sm:p-8 bg-bg-secondary border border-border shadow-lg rounded-2xl">
                <div class="max-w-xl">
                    @include('livewire.profile.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
