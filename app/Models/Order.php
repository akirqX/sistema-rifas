<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'raffle_id', 'product_id', 'ticket_quantity', 'total_amount', 'status', 'expires_at', 'guest_name', 'guest_email', 'payment_gateway', 'transaction_id', 'payment_details', 'uuid'];
    protected $casts = ['expires_at' => 'datetime', 'payment_details' => 'array'];

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

    /**
     * Helper para obter o nome do comprador, seja ele um usuário registrado ou um convidado.
     */
    public function getBuyerName(): string
    {
        return optional($this->user)->name ?? $this->guest_name ?? 'N/A';
    }

    /**
     * Helper para obter o e-mail do comprador.
     */
    public function getBuyerEmail(): string
    {
        return optional($this->user)->email ?? $this->guest_email ?? 'N/A';
    }
}
