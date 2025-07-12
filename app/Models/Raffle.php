<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Raffle extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'drawn_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relações (Relationships)
    |--------------------------------------------------------------------------
    */

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // ======================================================================
    // NOVA RELAÇÃO ADICIONADA AQUI
    // ======================================================================
    /**
     * Relação para contar apenas os tickets vendidos (pagos).
     * Isso permite o uso otimizado de withCount('ticketsSold').
     */
    public function ticketsSold()
    {
        return $this->hasMany(Ticket::class)->where('status', 'paid');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function winnerTicket()
    {
        return $this->belongsTo(Ticket::class, 'winner_ticket_id');
    }

    public function winner()
    {
        return $this->hasOneThrough(
            User::class,
            Ticket::class,
            'id',
            'id',
            'winner_ticket_id',
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Media Library (Spatie)
    |--------------------------------------------------------------------------
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
     * ATUALIZAÇÃO: Agora usa a contagem otimizada 'tickets_sold_count'
     * que é adicionada pelo withCount.
     */
    protected function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->total_tickets <= 0) { // Corrigido de total_numbers para total_tickets
                    return 0;
                }

                // Se 'tickets_sold_count' não foi carregado, calcula na hora.
                // Mas o ideal é que ele sempre venha do withCount.
                $sold_tickets = $this->tickets_sold_count ?? $this->ticketsSold()->count();

                return ($sold_tickets / $this->total_tickets) * 100;
            },
        );
    }

    /**
     * ATUALIZAÇÃO: Usa o nome da collection que você está usando (provavelmente 'raffles').
     * Verifique no seu componente de criação de rifa qual nome de collection você usou.
     */
    protected function coverImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFirstMediaUrl('raffles', 'default') ?: 'https://ui-avatars.com/api/?name=Sem+Imagem&size=400&background=e5e7eb&color=374151',
        );
    }
}
