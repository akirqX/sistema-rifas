{{-- resources/views/livewire/admin/skins/index.blade.php --}}
<div>
    <h1 class="text-2xl font-bold text-white mb-4">Gerenciar Skins (Pronta Entrega)</h1>

    <button wire:click="create()" class="btn-prodgio btn-primary mb-4">Adicionar Nova Skin</button>

    {{-- Tabela de Skins --}}
    <div class="bg-gray-900 rounded-lg p-4">
        <table class="w-full text-white">
            <thead>
                <tr>
                    <th class="text-left p-2">Nome</th>
                    <th class="text-left p-2">Preço</th>
                    <th class="text-left p-2">Status</th>
                    <th class="text-left p-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td class="p-2">{{ $product->name }}</td>
                    <td class="p-2">R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                    <td class="p-2"><span class="px-2 py-1 rounded-full text-xs {{ $product->status == 'available' ? 'bg-green-500' : 'bg-red-500' }}">{{ $product->status }}</span></td>
                    <td class="p-2">
                        {{-- Botões de Editar/Excluir --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal de Criação/Edição (aqui você implementaria o formulário) --}}
    @if($isModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-gray-900 p-8 rounded-lg w-1/3">
                <h2 class="text-xl font-bold text-white mb-4">Nova Skin</h2>
                <form wire:submit.prevent="store">
                    {{-- Campos do formulário com wire:model --}}
                    <input type="text" wire:model="name" placeholder="Nome da Skin" class="input-prodgio mb-4">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror

                    {{-- ... outros campos ... --}}

                    <div class="flex justify-end gap-4 mt-4">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="btn-prodgio btn-secondary">Cancelar</button>
                        <button type="submit" class="btn-prodgio btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
