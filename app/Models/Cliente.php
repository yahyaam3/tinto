<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'direccion',
        'estado',
        'nif'
    ];

    protected $attributes = [
        'estado' => 'activo'
    ];

    public function albaranes(): HasMany
    {
        return $this->hasMany(Albaran::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
} 