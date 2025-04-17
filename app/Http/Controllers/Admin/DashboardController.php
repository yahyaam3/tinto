<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener el primer día del mes actual
        $inicioMes = Carbon::now()->startOfMonth();
        
        // Obtener facturas del mes actual
        $facturasMes = Factura::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Calcular total facturado este mes
        $totalMes = Factura::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');

        // Total de clientes
        $totalClientes = Cliente::count();

        // Calcular incremento de clientes respecto al mes anterior
        $clientesMesAnterior = Cliente::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        
        $clientesMesActual = Cliente::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $incrementoClientes = $clientesMesAnterior > 0 
            ? (($clientesMesActual - $clientesMesAnterior) / $clientesMesAnterior) * 100 
            : 0;

        // Ingresos totales del año
        $ingresosTotales = Factura::whereYear('created_at', Carbon::now()->year)
            ->sum('total');

        // Producto más vendido
        $productoDestacado = Producto::select('productos.nombre', DB::raw('SUM(albaran_producto.cantidad) as cantidad'))
            ->join('albaran_producto', 'productos.id', '=', 'albaran_producto.producto_id')
            ->join('albaranes', 'albaran_producto.albaran_id', '=', 'albaranes.id')
            ->whereMonth('albaranes.created_at', Carbon::now()->month)
            ->whereYear('albaranes.created_at', Carbon::now()->year)
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('cantidad')
            ->first();

        // Ingresos mensuales del año actual
        $ingresosMensuales = Factura::select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('SUM(total) as total')
            )
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(function ($item) {
                return [
                    'mes' => Carbon::create()->month($item->mes)->format('F'),
                    'total' => $item->total
                ];
            });

        // Productos más vendidos
        $productosMasVendidos = Producto::select('productos.nombre', DB::raw('SUM(albaran_producto.cantidad) as cantidad'))
            ->join('albaran_producto', 'productos.id', '=', 'albaran_producto.producto_id')
            ->join('albaranes', 'albaran_producto.albaran_id', '=', 'albaranes.id')
            ->whereYear('albaranes.created_at', Carbon::now()->year)
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get();

        // Últimas facturas
        $ultimasFacturas = Factura::with('cliente')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($factura) {
                return [
                    'id' => $factura->id,
                    'numero' => $factura->numero,
                    'cliente' => $factura->cliente->nombre,
                    'fecha' => $factura->created_at->format('d/m/Y'),
                    'total' => number_format($factura->total, 2),
                    'estado' => $factura->pagada ? 'pagada' : 'pendiente'
                ];
            });

        return Inertia::render('Owner/Dashboard', [
            'stats' => [
                'facturas_mes' => $facturasMes,
                'total_mes' => number_format($totalMes, 2),
                'total_clientes' => $totalClientes,
                'incremento_clientes' => round($incrementoClientes, 1),
                'ingresos_totales' => number_format($ingresosTotales, 2),
                'producto_destacado' => $productoDestacado ?? ['nombre' => 'N/A', 'cantidad' => 0],
                'ingresos_mensuales' => $ingresosMensuales,
                'productos_mas_vendidos' => $productosMasVendidos,
                'ultimas_facturas' => $ultimasFacturas
            ]
        ]);
    }
} 