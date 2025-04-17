<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Albaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlbaranController extends Controller
{
    public function albaranesSinFacturar(Cliente $cliente)
    {
        Log::info('Buscando albaranes sin facturar para el cliente: ' . $cliente->id);
        
        $albaranes = $cliente->albaranes()
            ->whereNull('factura_id')
            ->with(['productos' => function($query) {
                $query->select('productos.*', 'albaran_producto.cantidad', 'albaran_producto.precio_unitario');
            }])
            ->get();

        Log::info('Albaranes encontrados: ' . $albaranes->count());

        return response()->json($albaranes->map(function ($albaran) {
            return [
                'id' => $albaran->id,
                'numero' => $albaran->numero,
                'fecha' => $albaran->fecha->format('Y-m-d'),
                'total' => $albaran->total,
                'estado' => $albaran->estado,
                'productos' => $albaran->productos->map(function ($producto) {
                    return [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'cantidad' => $producto->pivot->cantidad,
                        'precio_unitario' => $producto->pivot->precio_unitario,
                        'subtotal' => $producto->pivot->cantidad * $producto->pivot->precio_unitario
                    ];
                })
            ];
        }));
    }
} 