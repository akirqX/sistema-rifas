<div>
    {{-- ========================================================= --}}
    {{--    A SOLUÇÃO DEFINITIVA: CSS DIRETO NA PÁGINA          --}}
    {{--    Isto força o layout a funcionar, sem depender do build --}}
    {{-- ========================================================= --}}
    <style>
        /* Por padrão, a sidebar do desktop está escondida. */
        #desktop-sidebar {
            display: none;
        }

        /* Em telas grandes (min-width: 1024px, o 'lg' do Tailwind)... */
        @media (min-width: 1024px) {
            /* ...mostramos a sidebar do desktop... */
            #desktop-sidebar {
                display: block;
            }
            /* ...e escondemos a versão mobile das ações. */
            #mobile-actions {
                display: none;
            }
        }
    </style>

    <div class="container mx-auto px-4 py-8 xl:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            {{-- COLUNA DA ESQUERDA (CONTEÚDO PRINCIPAL) --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- PAINEL DA IMAGEM E INFO --}}
                <div class="bg-bg-secondary rounded-2xl border border-border p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 items-center">
                        <div class="w-full">
                            <img src="{{ $raffle->getFirstMediaUrl('raffles') ?: 'https://via.placeholder.com/800x450.png?text=Imagem' }}" alt="Prêmio: {{ $raffle->title }}" class="w-full rounded-lg object-cover">
                        </div>
                        <div class="flex flex-col h-full text-center md:text-left">
                            <h1 class="font-heading text-3xl xl:text-4xl text-text-light">{{ $raffle->title }}</h1>
                            <p class="text-lg text-primary-light font-semibold mt-1">R$ {{ number_format($raffle->price, 2, ',', '.') }} por cota</p>
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

                {{-- PAINÉIS DE AÇÃO (VERSÃO MOBILE) - Recebe o ID 'mobile-actions' --}}
                <div id="mobile-actions" class="space-y-8">
                    <div class="bg-bg-secondary p-6 rounded-2xl border border-border text-center">
                        <p class="text-text-muted">Você selecionou</p>
                        <p class="text-4xl font-bold text-primary-light my-1">{{ count($selectedTickets) }} cota(s)</p>
                        <p class="text-2xl font-semibold text-text-light">Total: R$ {{ number_format(count($selectedTickets) * $raffle->price, 2, ',', '.') }}</p>
                        <button wire:click="reserveTickets" class="cta-primary w-full max-w-xs mx-auto mt-4 py-3 text-lg transition-opacity @if(empty($selectedTickets)) opacity-50 @endif">Participar</button>
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
                            <button wire:click="changePage({{ $page }})" class="px-3 py-1 text-sm rounded-md font-semibold transition-colors {{ $currentPage === $page ? 'bg-primary-light text-white' : 'bg-bg-tertiary hover:bg-border' }}">{{ str_pad(($page - 1) * $perPage + 1, 4, '0', STR_PAD_LEFT) }} - {{ str_pad(min($page * $perPage, $totalTickets), 4, '0', STR_PAD_LEFT) }}</button>
                        @endfor
                    </div>
                    <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 xl:grid-cols-15 gap-2">
                        @for ($i = ($currentPage - 1) * $perPage + 1; $i <= min($currentPage * $perPage, $totalTickets); $i++)
                            @php $number = str_pad($i, 4, '0', STR_PAD_LEFT); $isOccupied = in_array($number, $occupiedNumbers); $isSelected = in_array($number, $selectedTickets); @endphp
                            <button wire:click="selectTicket('{{ $number }}')" wire:key="ticket-{{ $number }}" @if($isOccupied) disabled @endif class="ticket-btn @if($isSelected) is-selected @elseif($isOccupied) is-occupied @endif">{{ $number }}</button>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- COLUNA DA DIREITA (VERSÃO DESKTOP) - Recebe o ID 'desktop-sidebar' --}}
            <div id="desktop-sidebar" class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    <div class="bg-bg-secondary p-6 rounded-2xl border border-border text-center">
                        <p class="text-text-muted">Você selecionou</p>
                        <p class="text-4xl font-bold text-primary-light my-1">{{ count($selectedTickets) }} cota(s)</p>
                        <p class="text-2xl font-semibold text-text-light">Total: R$ {{ number_format(count($selectedTickets) * $raffle->price, 2, ',', '.') }}</p>
                        <button wire:click="reserveTickets" class="cta-primary w-full max-w-xs mx-auto mt-4 py-3 text-lg transition-opacity @if(empty($selectedTickets)) opacity-50 @endif">Participar</button>
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

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed bottom-5 right-5 bg-red-500 text-white font-bold py-3 px-5 rounded-lg shadow-lg"><i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}</div>
        @endif
    </div>
</div>
