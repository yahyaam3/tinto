<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'factura_id',
        'monto',
        'fecha',
        'metodo',
        'referencia'
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2'
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }
} 