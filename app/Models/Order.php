<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Order extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['expires_at' => 'datetime'];

    public function user()
    {
        // Um pedido PODE pertencer a um usuário
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

    // Helper para pegar o nome, seja de um usuário ou de um convidado
    public function getBuyerName(): string
    {
        return $this->user->name ?? $this->guest_name ?? 'N/A';
    }
    // Helper para pegar o email
    public function getBuyerEmail(): string
    {
        return $this->user->email ?? $this->guest_email ?? 'N/A';
    }
}
