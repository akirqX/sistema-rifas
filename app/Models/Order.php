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
}