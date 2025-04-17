<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'precio'
    ];

    protected $casts = [
        'precio' => 'decimal:2'
    ];

    public function albaranes(): BelongsToMany
    {
        return $this->belongsToMany(Albaran::class, 'albaran_producto')
            ->withPivot(['cantidad', 'precio_unitario', 'subtotal'])
            ->withTimestamps();
    }
} 