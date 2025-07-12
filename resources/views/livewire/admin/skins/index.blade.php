<div>
    <section class="hero-section py-20 text-center">
        <h1 class="hero-title text-5xl font-extrabold">
            Arsenal <span class="text-highlight">PRODGIO</span>
        </h1>
        <p class="hero-subtitle mt-4 text-xl">Skins inspecionadas e prontas para o seu inventário.</p>
    </section>

    <div class="container mx-auto px-4 pb-20">

        {{-- BARRA DE FILTROS E ORDENAÇÃO --}}
        <div class="flex flex-col md:flex-row gap-6 justify-between items-center mb-8 p-4 bg-panel-dark rounded-lg border border-border-subtle">
            <div class="flex flex-wrap gap-x-6 gap-y-2 items-center">
                <span class="font-semibold text-text-light mr-4">Filtrar por Tipo:</span>
                @foreach($types as $type)
                    <label class="flex items-center space-x-2 text-text-muted hover:text-white cursor-pointer">
                        <input type="checkbox" wire:model.live="filterType" value="{{ $type }}" class="form-checkbox bg-panel-dark border-border-subtle text-primary-purple focus:ring-primary-purple">
                        <span>{{ $type }}</span>
                    </label>
                @endforeach
            </div>
            <div>
                <label for="sortBy" class="sr-only">Ordenar por</label>
                <select id="sortBy" wire:model.live="sortBy" class="bg-bg-secondary border border-border-subtle rounded-lg focus:ring-primary-purple focus:border-primary-purple">
                    <option value="latest">Mais Recentes</option>
                    <option value="price_asc">Menor Preço</option>
                    <option value="price_desc">Maior Preço</option>
                </select>
            </div>
        </div>

        <div wire:loading.flex class="w-full justify-center py-12">
             <div class="text-white text-2xl font-semibold">Carregando Skins...</div>
        </div>

        <div wire:loading.remove>
            @if($skins->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($skins as $skin)
                        <div class="skin-card group relative bg-panel-dark border border-border-subtle rounded-lg overflow-hidden transition-all duration-300 hover:border-primary-purple hover:scale-105">
                            <a href="{{ route('skins.show', $skin) }}">
                                <img src="{{ $skin->getFirstMediaUrl('product_images', 'default') }}" alt="{{ $skin->name }}" class="w-full h-56 object-contain p-4 transition-transform duration-300 group-hover:scale-110">
                            </a>
                            <div class="p-4">
                                <h3 class="font-bold text-white truncate">{{ $skin->name }}</h3>
                                <p class="text-sm text-text-muted">{{ $skin->wear }}</p>
                                <div class="mt-4 flex justify-between items-center">
                                    <p class="text-xl font-bold text-white">R$ {{ number_format($skin->price, 2, ',', '.') }}</p>
                                    <a href="{{ route('skins.show', $skin) }}" class="btn-prodgio btn-primary text-sm">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $skins->links() }}
                </div>
            @else
                <div class="text-center py-20 bg-panel-dark border border-border-subtle rounded-lg">
                    <h3 class="text-2xl font-bold text-white">Nenhuma skin encontrada para os filtros selecionados.</h3>
                    <p class="mt-2 text-text-muted">Tente ajustar sua busca ou volte mais tarde!</p>
                </div>
            @endif
        </div>
    </div>
</div>
