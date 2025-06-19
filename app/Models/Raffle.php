<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Casts\Attribute; // <-- Adicionado para os Accessors
use Spatie\MediaLibrary\MediaCollections\Models\Media; // <-- Adicionado para o tipo no método

class Raffle extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'drawn_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relações (Relationships)
    |--------------------------------------------------------------------------
    */

    /**
     * Uma rifa possui muitos bilhetes (tickets).
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Uma rifa possui muitos pedidos (orders).
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * RELAÇÃO CORRIGIDA (Passo 1 de 2):
     * Obtém o modelo do BILHETE que foi o vencedor.
     * A chave estrangeira 'winner_ticket_id' na tabela 'raffles' aponta para o 'id' na tabela 'tickets'.
     */
    public function winnerTicket()
    {
        return $this->belongsTo(Ticket::class, 'winner_ticket_id');
    }

    /**
     * RELAÇÃO CORRIGIDA E MELHORADA (Passo 2 de 2):
     * Obtém o modelo do USUÁRIO vencedor DIRETAMENTE, através do bilhete.
     * Isso permite que você use a sintaxe limpa: $raffle->winner->name
     * Laravel "pula" da tabela Raffles para a Users através da tabela Tickets.
     */
    public function winner()
    {
        return $this->hasOneThrough(
            User::class,            // O modelo final que queremos acessar (Usuário)
            Ticket::class,          // O modelo intermediário (Bilhete)
            'id',                   // A chave na tabela de bilhetes (tickets.id) ...
            'id',                   // ... que corresponde à chave na tabela de usuários (users.id)
            'winner_ticket_id',     // A chave local na tabela de rifas (raffles.winner_ticket_id) ...
            'user_id'               // ... que corresponde à chave na tabela de bilhetes (tickets.user_id)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Media Library (Spatie)
    |--------------------------------------------------------------------------
    */

    /**
     * Registra as conversões de mídia para as imagens da rifa.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors e Mutators (Atributos Customizados)
    |--------------------------------------------------------------------------
    */

    /**
     * NOVO ACCESSOR:
     * Calcula dinamicamente a porcentagem de progresso da rifa.
     * Essencial para a barra de progresso na nova página inicial.
     * Permite usar a sintaxe: $raffle->progress_percentage
     */
    protected function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->total_numbers <= 0) {
                    return 0;
                }
                // Conta apenas os bilhetes que foram efetivamente pagos
                $sold_tickets = $this->tickets()->where('status', 'paid')->count();
                return ($sold_tickets / $this->total_numbers) * 100;
            },
        );
    }

    /**
     * NOVO ACCESSOR:
     * Obtém a URL da imagem de capa ou retorna uma imagem padrão.
     * Evita erros na view caso uma rifa não tenha imagem.
     * Permite usar a sintaxe: $raffle->cover_image_url
     */
    protected function coverImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFirstMediaUrl('default') ?: 'https://ui-avatars.com/api/?name=Sem+Imagem&size=400&background=e5e7eb&color=374151',
        );
    }
}
