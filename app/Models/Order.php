<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * Usa $guarded para permitir que todos os campos sejam preenchidos em massa,
     * exatamente como no seu código original.
     */
    protected $guarded = [];

    /**
     * A CORREÇÃO CRÍTICA:
     * Diz ao Laravel para tratar a coluna 'payment_details' como um array/JSON.
     * Isso permite que você acesse os dados com $order->payment_details['qr_code'].
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'payment_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function getBuyerName(): string
    {
        return $this->user->name ?? $this->guest_name ?? 'N/A';
    }

    public function getBuyerEmail(): string
    {
        return $this->user->email ?? $this->guest_email ?? 'N/A';
    }
}
