<div>
    @php
        $padding = strlen((string)$totalTickets);
    @endphp

    <style>
        #desktop-sidebar { display: none; }
        @media (min-width: 1024px) {
            #desktop-sidebar { display: block; }
            #mobile-actions { display: none; }
        }
    </style>

    <div class="container mx-auto px-4 py-8 xl:py-12">
        {{-- Local para exibir mensagens de erro/info --}}
        @if (session('error')) <div class="bg-red-500/10 border border-red-500/30 text-red-300 p-4 rounded-lg text-center mb-6">{{ session('error') }}</div> @endif
        @if (session('info')) <div class="bg-blue-500/10 border border-blue-500/30 text-blue-300 p-4 rounded-lg text-center mb-6">{{ session('info') }}</div> @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2 space-y-8">
                {{-- PAINEL DA IMAGEM E INFO --}}
                <div class="bg-bg-secondary rounded-2xl border border-border p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 items-center">
                        <div class="w-full">
                            <img src="{{ $raffle->getFirstMediaUrl('raffles') ?: 'https://via.placeholder.com/800x450.png?text=Imagem' }}" alt="Prêmio: {{ $raffle->title }}" class="w-full rounded-lg object-cover">
                        </div>
                        <div class="flex flex-col h-full text-center md:text-left">
                            <h1 class="font-heading text-3xl xl:text-4xl text-text-light">{{ $raffle->title }}</h1>
                            <p class="text-lg text-primary-light font-semibold mt-1">R$ {{ number_format($raffle->ticket_price, 2, ',', '.') }} por cota</p>
                            <p class="text-text-muted mt-4 flex-grow">{{ $raffle->description }}</p>
                            <div class="mt-6">
                                <div class="flex justify-between text-sm font-medium text-text-muted mb-1">
                                    <span>Progresso</span>
                                    <span>{{ number_format($progressPercent, 1) }}%</span>
                                </div>
                                <div class="w-full bg-bg-tertiary rounded-full h-2.5"><div class="bg-primary-light h-2.5 rounded-full" style="width: {{ $progressPercent }}%"></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PAINÉIS DE AÇÃO (VERSÃO MOBILE) --}}
                <div id="mobile-actions" class="space-y-8">
                    <div class="bg-bg-secondary p-6 rounded-2xl border border-border text-center">
                        <p class="text-text-muted">Você selecionou</p>
                        <p class="text-4xl font-bold text-primary-light my-1">{{ count($selectedTickets) }} cota(s)</p>
                        <p class="text-2xl font-semibold text-text-light">Total: R$ {{ number_format(count($selectedTickets) * $raffle->ticket_price, 2, ',', '.') }}</p>
                        <button wire:click="startCheckout" class="cta-primary w-full max-w-xs mx-auto mt-4 py-3 text-lg transition-opacity @if(empty($selectedTickets)) opacity-50 cursor-not-allowed @endif" @if(empty($selectedTickets)) disabled @endif wire:loading.attr="disabled" wire:target="startCheckout, processGuestCheckout">
                            <span wire:loading.remove wire:target="startCheckout, processGuestCheckout">
                                @if(empty($selectedTickets)) Selecione Cotas para Participar @else Participar @endif
                            </span>
                            <span wire:loading wire:target="startCheckout, processGuestCheckout">Processando...</span>
                        </button>
                    </div>
                    <div class="bg-bg-secondary p-6 rounded-2xl border border-border">
                        <h3 class="font-semibold text-text-light text-center mb-4">Ações Rápidas</h3>
                        <div class="grid grid-cols-4 gap-2">
                            <button wire:click="adjustSelection(1)" class="action-btn-sm">+1</button><button wire:click="adjustSelection(5)" class="action-btn-sm">+5</button><button wire:click="adjustSelection(10)" class="action-btn-sm">+10</button><button wire:click="adjustSelection(50)" class="action-btn-sm">+50</button>
                            <button wire:click="adjustSelection(-1)" class="action-btn-sm remove">-1</button><button wire:click="adjustSelection(-5)" class="action-btn-sm remove">-5</button><button wire:click="adjustSelection(-10)" class="action-btn-sm remove">-10</button><button wire:click="clearSelection" wire:confirm="Limpar seleção?" class="action-btn-sm clear"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>

                {{-- PAINEL DA GRADE DE NÚMEROS --}}
                <div class="bg-bg-secondary p-6 rounded-2xl border border-border">
                    <h2 class="text-2xl font-semibold text-text-light text-center mb-6">Escolha suas cotas</h2>
                    <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
                        @for ($page = 1; $page <= $totalPages; $page++)
                            <button wire:click="changePage({{ $page }})" class="px-3 py-1 text-sm rounded-md font-semibold transition-colors {{ $currentPage === $page ? 'bg-primary-light text-white' : 'bg-bg-tertiary hover:bg-border' }}">{{ str_pad(($page - 1) * $perPage + 1, $padding, '0', STR_PAD_LEFT) }} - {{ str_pad(min($page * $perPage, $totalTickets), $padding, '0', STR_PAD_LEFT) }}</button>
                        @endfor
                    </div>
                    <div wire:loading.class="opacity-50" wire:target="changePage" class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 xl:grid-cols-15 gap-2">
                        @for ($i = ($currentPage - 1) * $perPage + 1; $i <= min($currentPage * $perPage, $totalTickets); $i++)
                            @php $number = str_pad($i, $padding, '0', STR_PAD_LEFT); $isOccupied = in_array($number, $occupiedNumbers); $isSelected = in_array($number, $selectedTickets); @endphp
                            <button wire:click="selectTicket('{{ $number }}')" wire:key="ticket-{{ $number }}" @if($isOccupied) disabled @endif class="ticket-btn @if($isSelected) is-selected @elseif($isOccupied) is-occupied @endif">{{ $number }}</button>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- COLUNA DA DIREITA (VERSÃO DESKTOP) --}}
            <div id="desktop-sidebar" class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    <div class="bg-bg-secondary p-6 rounded-2xl border border-border text-center">
                        <p class="text-text-muted">Você selecionou</p>
                        <p class="text-4xl font-bold text-primary-light my-1">{{ count($selectedTickets) }} cota(s)</p>
                        <p class="text-2xl font-semibold text-text-light">Total: R$ {{ number_format(count($selectedTickets) * $raffle->ticket_price, 2, ',', '.') }}</p>
                        <button wire:click="startCheckout" class="cta-primary w-full max-w-xs mx-auto mt-4 py-3 text-lg transition-opacity @if(empty($selectedTickets)) opacity-50 cursor-not-allowed @endif" @if(empty($selectedTickets)) disabled @endif wire:loading.attr="disabled" wire:target="startCheckout, processGuestCheckout">
                            <span wire:loading.remove wire:target="startCheckout, processGuestCheckout">
                                @if(empty($selectedTickets)) Selecione Cotas para Participar @else Participar @endif
                            </span>
                            <span wire:loading wire:target="startCheckout, processGuestCheckout">Processando...</span>
                        </button>
                    </div>
                    <div class="bg-bg-secondary p-6 rounded-2xl border border-border">
                        <h3 class="font-semibold text-text-light text-center mb-4">Ações Rápidas</h3>
                        <div class="grid grid-cols-4 gap-2">
                            <button wire:click="adjustSelection(1)" class="action-btn-sm">+1</button><button wire:click="adjustSelection(5)" class="action-btn-sm">+5</button><button wire:click="adjustSelection(10)" class="action-btn-sm">+10</button><button wire:click="adjustSelection(50)" class="action-btn-sm">+50</button>
                            <button wire:click="adjustSelection(-1)" class="action-btn-sm remove">-1</button><button wire:click="adjustSelection(-5)" class="action-btn-sm remove">-5</button><button wire:click="adjustSelection(-10)" class="action-btn-sm remove">-10</button><button wire:click="clearSelection" wire:confirm="Limpar seleção?" class="action-btn-sm clear"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE GUEST CHECKOUT ATUALIZADO --}}
    @if($showGuestModal)
        <div x-data="{ show: @entangle('showGuestModal') }" x-show="show" x-on:keydown.escape.window="show = false" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4" style="display: none;">
            <div x-show="show" x-transition @click.away="show = false" class="bg-bg-secondary rounded-2xl border border-border shadow-lg w-full max-w-md p-8">
                <h2 class="text-2xl font-bold text-white mb-2 text-center">Quase lá!</h2>
                <p class="text-text-muted text-center mb-6">Precisamos de alguns dados para sua compra.</p>

                <form wire:submit="processGuestCheckout">
                    <div class="space-y-4">
                        <div>
                            <label for="guestName" class="form-label">Nome Completo</label>
                            <input type="text" id="guestName" wire:model="guestName" class="form-input" required>
                            @error('guestName') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="guestEmail" class="form-label">E-mail</label>
                            <input type="email" id="guestEmail" wire:model="guestEmail" class="form-input" required>
                            @error('guestEmail') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="guestPhone" class="form-label">Telefone (WhatsApp)</label>
                            <input type="tel" id="guestPhone" wire:model="guestPhone" class="form-input" required placeholder="(XX) XXXXX-XXXX">
                            @error('guestPhone') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="guestCpf" class="form-label">CPF</label>
                            <input type="text" id="guestCpf" wire:model="guestCpf" class="form-input" required placeholder="000.000.000-00">
                            @error('guestCpf') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col items-center gap-4">
                        <button type="submit" class="cta-primary w-full text-lg" wire:loading.attr="disabled" wire:target="processGuestCheckout">
                            <span wire:loading.remove wire:target="processGuestCheckout">Continuar Compra</span>
                            <span wire:loading wire:target="processGuestCheckout">Criando Pedido...</span>
                        </button>
                        <a href="{{ route('login') }}" class="text-primary-light hover:underline text-sm">Já tem uma conta? Faça login</a>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
