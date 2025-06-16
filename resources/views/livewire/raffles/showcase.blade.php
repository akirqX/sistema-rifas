<div>
    <section class="section">
        <div class="container mx-auto px-4">
            <div class="section-header">
                <h1 class="section-title">Rifas Disponíveis</h1>
                <p class="section-subtitle">Escolha sua sorte e participe. O próximo vencedor pode ser você!</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($raffles as $raffle)
                    <div class="skin-card">
                        <a href="{{ route('raffle.show', $raffle) }}" class="skin-image-wrapper">
                            <img src="{{ $raffle->getFirstMediaUrl('raffles') ?: 'https://via.placeholder.com/400x300.png?text=Rifa' }}" alt="Imagem da {{ $raffle->title }}">
                        </a>
                        <div class="skin-content">
                            <h3 class="skin-title">{{ $raffle->title }}</h3>
                            <p class="skin-wear">{{ \Illuminate\Support\Str::limit($raffle->description, 80) }}</p>

                            {{-- CORREÇÃO: Usando a propriedade correta 'price' --}}
                            <p class="skin-price">R$ {{ number_format($raffle->price, 2, ',', '.') }}</p>

                            <a href="{{ route('raffle.show', $raffle) }}" class="cta-primary mt-4 w-full justify-center">
                                Participar
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-16 bg-bg-secondary rounded-lg border border-border">
                        <h3 class="text-lg text-text-muted">Nenhuma rifa ativa no momento. Volte em breve!</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
