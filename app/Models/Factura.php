<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'fecha',
        'fecha_vencimiento',
        'estado',
        'numero',
        'subtotal',
        'iva',
        'total'
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class)
            ->withPivot(['cantidad', 'precio_unitario', 'subtotal'])
            ->withTimestamps();
    }

    public function albaranes(): HasMany
    {
        return $this->hasMany(Albaran::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
} 