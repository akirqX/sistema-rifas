<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Raffle extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'drawn_at' => 'datetime',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Adicione este método dentro da classe Raffle

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);
    }

    /**
     * Get the winning ticket associated with the Raffle.
     */
    public function winner()
    {
        return $this->belongsTo(Ticket::class, 'winner_ticket_id');
    }
}

