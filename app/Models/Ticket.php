<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * Melhor prática: Usar $fillable em vez de $guarded = [].
     * Define explicitamente os campos que podem ser preenchidos.
     */
    protected $fillable = [
        'raffle_id',
        'order_id',
        'user_id',
        'number',
        'status',
    ];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
