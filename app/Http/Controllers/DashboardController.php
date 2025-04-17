<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\Albaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClientes = Cliente::count();
        
        // Facturas y ingresos de este mes
        $facturasMes = Factura::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->count();
        $ingresosMes = Factura::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            // Asumiendo que solo sumas las 'pagadas' para ingresos reales
            // Si no tienes estado 'pagada', elimina o ajusta este where
            ->where('estado', 'pagada') 
            ->sum('total');
            
        // Facturas por cobrar (pendientes)
        // Asumiendo que tienes un estado 'pendiente'
        $facturasPendientes = Factura::where('estado', 'pendiente')->count();
        $totalPendiente = Factura::where('estado', 'pendiente')->sum('total');

        // --- Estadísticas de Albaranes ---
        $albaranesPendientesCount = Albaran::where('estado', 'pendiente')->count();
        $albaranesPendientesTotal = Albaran::where('estado', 'pendiente')->sum('total');
        
        $albaranesCompletadosMes = Albaran::where('estado', 'completado')
            ->whereMonth('updated_at', now()->month) // Usamos updated_at para saber cuándo se completó
            ->whereYear('updated_at', now()->year)
            ->count();

        // Ventas diarias del mes actual
        $ventasDiarias = Factura::where('estado', 'pagada')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->select(DB::raw('DATE(fecha) as dia'), DB::raw('SUM(total) as total'))
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        // Ventas mensuales del año actual
        $ventasMensuales = Factura::where('estado', 'pagada')
            ->whereYear('fecha', now()->year)
            ->select(DB::raw('MONTH(fecha) as mes'), DB::raw('SUM(total) as total'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Productos más solicitados (top 5)
        $productosMasSolicitados = DB::table('albaran_producto')
            ->join('productos', 'productos.id', '=', 'albaran_producto.producto_id')
            ->select('productos.nombre', DB::raw('SUM(albaran_producto.cantidad) as total_solicitado'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_solicitado')
            ->limit(5)
            ->get();

        // Clientes más frecuentes (top 5)
        $clientesMasFrecuentes = Cliente::withCount('albaranes')
            ->orderByDesc('albaranes_count')
            ->limit(5)
            ->get();

        // Historial: Últimas facturas
        $ultimasFacturas = Factura::with('cliente')
            ->latest()
            ->take(5)
            ->get();

        return inertia('Dashboard', [
            'totalClientes' => $totalClientes,
            'facturasMes' => $facturasMes,
            'ingresosMes' => $ingresosMes,
            'facturasPendientes' => $facturasPendientes,
            'totalPendiente' => $totalPendiente,
            // Nuevas props de albaranes
            'albaranesPendientesCount' => $albaranesPendientesCount,
            'albaranesPendientesTotal' => $albaranesPendientesTotal,
            'albaranesCompletadosMes' => $albaranesCompletadosMes,
            
            'ultimasFacturas' => $ultimasFacturas,
            // Nuevas estadísticas
            'ventasDiarias' => $ventasDiarias,
            'ventasMensuales' => $ventasMensuales,
            'productosMasSolicitados' => $productosMasSolicitados,
            'clientesMasFrecuentes' => $clientesMasFrecuentes
        ]);
    }
} 