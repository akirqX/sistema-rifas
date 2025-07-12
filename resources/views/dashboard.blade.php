<x-app-layout>
<div class="py-12 pt-32">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<div class="bg-bg-secondary border border-border overflow-hidden shadow-lg rounded-2xl">
<div class="p-6 md:p-8 text-text-light">
<h1 class="text-3xl font-bold text-white mb-2">
Bem-vindo de volta, <span class="text-highlight">{{ Auth::user()->name }}</span>!
</h1>
<p class="text-text-muted mb-8">
Aqui você pode gerenciar seus pedidos, visualizar suas cotas e atualizar seu perfil.
</p>
@if (session('info'))<div class="p-4 mb-6 text-sm text-blue-300 border border-blue-500/30 rounded-lg bg-blue-500/20">{{ session('info') }}</div>@endif
@if(!Auth::user()->cpf || !Auth::user()->phone)<div class="p-4 mb-6 text-sm text-yellow-300 border border-yellow-500/30 rounded-lg bg-yellow-500/20"><i class="fas fa-exclamation-triangle mr-2"></i><strong>Atenção:</strong> Seu cadastro está incompleto. Para participar das rifas, por favor, <a href="{{ route('profile.edit') }}" class="font-bold underline hover:text-yellow-200" wire:navigate>complete seu perfil</a> com seu CPF e Telefone.</div>@endif
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<a href="{{ route('my.orders') }}" class="dashboard-card group" wire:navigate>
<div class="card-icon"><i class="fas fa-receipt"></i></div>
<h2 class="card-title">Meus Pedidos</h2>
<p class="card-description">Visualize o histórico e o status de todas as suas compras.</p>
</a>
<a href="{{ route('my.tickets') }}" class="dashboard-card group" wire:navigate>
<div class="card-icon"><i class="fas fa-ticket-alt"></i></div>
<h2 class="card-title">Minhas Cotas</h2>
<p class="card-description">Veja todos os números que você está concorrendo.</p>
</a>
<a href="{{ route('profile.edit') }}" class="dashboard-card group" wire:navigate>
<div class="card-icon"><i class="fas fa-user-edit"></i></div>
<h2 class="card-title">Editar Perfil</h2>
<p class="card-description">Atualize seus dados pessoais, senha e informações.</p>
</a>
</div>
</div>
</div>
</div>
</div>
</x-app-layout>
