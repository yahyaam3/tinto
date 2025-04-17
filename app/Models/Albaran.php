<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Albaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'albaranes';

    protected $fillable = [
        'numero',
        'cliente_id',
        'fecha',
        'total',
        'estado',
        'factura_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2'
    ];

    protected $attributes = [
        'estado' => 'pendiente'
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'albaran_producto')
            ->withPivot(['cantidad', 'precio_unitario', 'subtotal'])
            ->withTimestamps();
    }

    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class);
    }
} 