<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    /**
     * CORREÇÃO: Adicionados os novos campos para CPF e Telefone do convidado.
     */
    protected $fillable = [
        'user_id',
        'raffle_id',
        'product_id',
        'ticket_quantity',
        'total_amount',
        'status',
        'expires_at',
        'guest_name',
        'guest_email',
        'guest_cpf',      // <-- ADICIONADO
        'guest_phone',    // <-- ADICIONADO
        'payment_gateway',
        'transaction_id',
        'payment_details',
        'uuid'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'payment_details' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function getBuyerName(): ?string
    {
        return $this->user ? $this->user->name : $this->guest_name;
    }

    public function getBuyerEmail(): ?string
    {
        return $this->user ? $this->user->email : $this->guest_email;
    }
}
