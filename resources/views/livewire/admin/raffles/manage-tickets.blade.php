<div class="container mx-auto px-4 py-8 sm:py-12">
    {{-- Mensagens de Sucesso e Erro --}}
    @if (session()->has('success')) <div class="p-4 mb-6 text-sm text-green-300 border border-green-500/30 rounded-lg bg-green-500/20">{{ session('success') }}</div> @endif

    {{-- Cabeçalho --}}
    <div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-text-muted hover:text-white mb-4 inline-block">← Voltar para o Painel</a>
        <h1 class="text-3xl font-bold text-white">Gerenciamento de Cotas</h1>
        <p class="text-text-muted mt-1">Rifa: "{{ $raffle->title }}"</p>
    </div>

    {{-- Filtros e Busca --}}
    <div class="mt-8 mb-4 flex flex-col md:flex-row gap-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por número, nome ou email..." class="form-input flex-grow">
        <select wire:model.live="statusFilter" class="form-input">
            <option value="">Todos os Status</option>
            <option value="paid">Pagas</option>
            <option value="pending">Pendentes</option>
            <option value="available">Disponíveis</option>
        </select>
    </div>

    {{-- Tabela de Cotas --}}
    <div class="bg-panel-dark border border-border-subtle rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-bg-tertiary">
                    <tr>
                        <th class="py-3 px-6 text-left text-xs font-semibold text-text-muted uppercase tracking-wider">Número</th>
                        <th class="py-3 px-6 text-left text-xs font-semibold text-text-muted uppercase tracking-wider">Usuário</th>
                        <th class="py-3 px-6 text-center text-xs font-semibold text-text-muted uppercase tracking-wider">Status</th>
                        <th class="py-3 px-6 text-right text-xs font-semibold text-text-muted uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($tickets as $ticket)
                        <tr wire:key="ticket-{{ $ticket->id }}" class="hover:bg-gray-800/50">
                            <td class="py-4 px-6 whitespace-nowrap font-mono text-white"><strong>{{ str_pad($ticket->number, 4, '0', STR_PAD_LEFT) }}</strong></td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($ticket->user)
                                    <div class="text-sm font-medium text-white">{{ $ticket->user->name }}</div>
                                    <div class="text-xs text-text-subtle">{{ $ticket->user->email }}</div>
                                @else
                                    <span class="text-text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap text-center"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ticket->status === 'paid' ? 'bg-green-500/20 text-green-400' : ($ticket->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-gray-500/20 text-gray-400') }}">{{ ucfirst($ticket->status) }}</span></td>
                            <td class="py-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                @if($ticket->status !== 'available')
                                    <button wire:click="cancelTicket({{ $ticket->id }})" wire:confirm="Tem certeza que deseja liberar esta cota? O usuário perderá o número." class="text-red-400 hover:text-red-300">Liberar Cota</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-12 text-center text-text-muted">Nenhuma cota encontrada com os filtros atuais.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $tickets->links() }}</div>
    </div>
</div>
