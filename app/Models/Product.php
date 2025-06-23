<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'type',
        'wear',
        'price',
        'status',
        'steam_inspect_link'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
}
