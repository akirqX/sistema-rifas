{{-- resources/views/livewire/skins/index-page.blade.php --}}
<div>
    {{-- Seção Herói da Loja --}}
    <section class="hero-section py-20 text-center">
        <h1 class="hero-title text-5xl font-extrabold">
            Arsenal <span class="text-highlight">PRODGIO</span>
        </h1>
        <p class="hero-subtitle mt-4 text-xl">Skins inspecionadas e prontas para o seu inventário.</p>
    </section>

    {{-- Filtros (Futura implementação) --}}
    {{-- <div class="container mx-auto px-4 mb-8"> ... Filtros aqui ... </div> --}}

    {{-- Grid de Skins --}}
    <div class="container mx-auto px-4 pb-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($skins as $skin)
                <div class="skin-card group relative bg-gray-900 border border-gray-800 rounded-lg overflow-hidden transition-all duration-300 hover:border-purple-500 hover:scale-105">
                    <a href="#"> {{-- Link para a página de detalhes da skin --}}
                        <img src="{{ $skin->getFirstMediaUrl('product_images') }}" alt="{{ $skin->name }}" class="w-full h-56 object-contain p-4 transition-transform duration-300 group-hover:scale-110">
                    </a>
                    <div class="p-4">
                        <h3 class="font-bold text-white truncate">{{ $skin->name }}</h3>
                        <p class="text-sm text-gray-400">{{ $skin->wear }}</p>
                        <div class="mt-4 flex justify-between items-center">
                            <p class="text-xl font-bold text-white">R$ {{ number_format($skin->price, 2, ',', '.') }}</p>
                            <a href="#" class="btn-prodgio btn-primary">Comprar</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
