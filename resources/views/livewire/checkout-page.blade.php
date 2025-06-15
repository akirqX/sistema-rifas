<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Finalizar Compra</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Coluna do Formulário -->
                    <div>
                        <h3 class="text-2xl font-bold mb-4">Seus Dados</h3>
                        <p class="text-gray-600 mb-6">Preencha seus dados para identificação do seu pedido.</p>
                        <form wire:submit.prevent="processOrder">
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                                    <input type="text" wire:model.defer="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" wire:model.defer="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Telefone (com DDD)</label>
                                    <input type="text" wire:model.defer="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                                    <input type="text" wire:model.defer="cpf" id="cpf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('cpf') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Coluna do Resumo -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-2xl font-bold mb-4">Resumo do Pedido</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Rifa:</span>
                                <span class="font-semibold">{{ $raffle->title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Cotas selecionadas:</span>
                                <span class="font-semibold">{{ $ticketCount }}</span>
                            </div>
                            <hr>
                            <div class="flex justify-between text-xl font-bold">
                                <span>Total:</span>
                                <span>R$ {{ number_format($totalAmount, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        <h4 class="font-bold mt-8 mb-2">Cotas:</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($selectedTicketsData as $ticket)
                                <span class="bg-blue-500 text-white font-mono font-bold text-sm py-1 px-3 rounded-full">{{ str_pad($ticket['number'], 4, '0', STR_PAD_LEFT) }}</span>
                            @endforeach
                        </div>
                        <div class="mt-8">
                            <button wire:click="processOrder" wire:loading.attr="disabled" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg text-lg">
                                Ir para Pagamento
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
