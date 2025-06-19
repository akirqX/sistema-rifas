<div>
    <div class="container mx-auto px-4 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

            {{-- Coluna da Imagem --}}
            <div class="bg-panel-dark border border-border-subtle rounded-lg p-8">
                <img src="{{ $product->getFirstMediaUrl('product_images', 'default') }}" alt="{{ $product->name }}" class="w-full h-auto object-contain">
            </div>

            {{-- Coluna de Informações e Compra --}}
            <div>
                <h1 class="text-4xl font-extrabold text-white">{{ $product->name }}</h1>
                <p class="text-xl text-text-muted mt-2">{{ $product->wear }}</p>

                <p class="text-lg text-text-muted mt-6">
                    {{ $product->description ?? 'Nenhuma descrição adicional para este item.' }}
                </p>

                <div class="my-8">
                    <p class="text-4xl font-bold text-white">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                </div>

                <div class="flex items-center gap-4">
                    {{-- TODO: Adicionar lógica para o checkout da skin --}}
                    <a href="#" class="btn-prodgio btn-primary flex-grow text-center">Comprar Agora</a>
                    @if($product->steam_inspect_link)
                        <a href="{{ $product->steam_inspect_link }}" target="_blank" class="btn-prodgio btn-secondary text-center">Inspecionar no Jogo</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
