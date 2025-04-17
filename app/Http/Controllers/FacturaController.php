<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Albaran;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\UniqueConstraintViolationException;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $query = Factura::with('cliente')
            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->estado && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $facturas = $query->paginate(10)
            ->withQueryString();

        return Inertia::render('Facturas/Index', [
            'facturas' => $facturas,
            'filters' => $request->only(['search', 'estado'])
        ]);
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')
            ->select('id', 'nombre', 'direccion', 'telefono')
            ->get();
        
        return Inertia::render('Facturas/Create', [
            'clientes' => $clientes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha',
            'albaran_ids' => 'required|array|min:1',
            'albaran_ids.*' => 'exists:albaranes,id'
        ]);

        try {
            DB::beginTransaction();

            // Generar número de factura
            $numero = $this->generarNumeroFactura();

            // Crear la factura
            $factura = new Factura();
            $factura->numero = $numero;
            $factura->cliente_id = $validated['cliente_id'];
            $factura->fecha = $validated['fecha'];
            $factura->fecha_vencimiento = $validated['fecha_vencimiento'];
            $factura->estado = 'pendiente';
            $factura->save();

            // Obtener los albaranes y sus productos
            $albaranes = Albaran::whereIn('id', $validated['albaran_ids'])
                ->with('productos')
                ->get();

            $subtotal = 0;
            $productos = [];

            // Procesar cada albarán
            foreach ($albaranes as $albaran) {
                // Vincular el albarán con la factura
                $albaran->factura_id = $factura->id;
                $albaran->estado = 'facturado';
                $albaran->save();

                // Agregar los productos del albarán a la factura
                foreach ($albaran->productos as $producto) {
                    $pivot = $producto->pivot;
                    $subtotalProducto = $pivot->cantidad * $pivot->precio_unitario;
                    
                    // Agregar o actualizar productos en la factura
                    if (isset($productos[$producto->id])) {
                        $productos[$producto->id]['cantidad'] += $pivot->cantidad;
                        $productos[$producto->id]['subtotal'] += $subtotalProducto;
                    } else {
                        $productos[$producto->id] = [
                            'id' => $producto->id,
                            'cantidad' => $pivot->cantidad,
                            'precio_unitario' => $pivot->precio_unitario,
                            'subtotal' => $subtotalProducto
                        ];
                    }
                    
                    $subtotal += $subtotalProducto;
                }
            }

            // Calcular IVA y total
            $iva = $subtotal * 0.21; // 21% IVA
            $total = $subtotal + $iva;

            // Actualizar los totales de la factura
            $factura->subtotal = $subtotal;
            $factura->iva = $iva;
            $factura->total = $total;
            $factura->save();

            // Agregar productos a la factura
            foreach ($productos as $producto) {
                $factura->productos()->attach($producto['id'], [
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                    'subtotal' => $producto['subtotal']
                ]);
            }

            DB::commit();

            return redirect()->route('facturas.index')
                ->with('success', 'Factura creada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear factura: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear la factura: ' . $e->getMessage()]);
        }
    }

    public function show(Factura $factura)
    {
        $factura->load(['cliente', 'productos']);
        
        return Inertia::render('Facturas/Show', [
            'factura' => $factura
        ]);
    }

    public function edit(Factura $factura)
    {
        $factura->load('productos');
        
        return Inertia::render('Facturas/Form', [
            'factura' => $factura,
            'clientes' => Cliente::select('id', 'nombre')->get(),
            'productos' => Producto::select('id', 'nombre', 'precio')->get()
        ]);
    }

    public function update(Request $request, Factura $factura)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:pendiente,pagada',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        // Calcular el nuevo total
        $total = 0;
        foreach ($validated['productos'] as $producto) {
            $productoInfo = Producto::find($producto['producto_id']);
            $total += $productoInfo->precio * $producto['cantidad'];
        }

        $factura->update([
            'cliente_id' => $validated['cliente_id'],
            'fecha' => $validated['fecha'],
            'estado' => $validated['estado'],
            'total' => $total
        ]);

        // Actualizar productos
        $factura->productos()->detach();
        foreach ($validated['productos'] as $producto) {
            $productoInfo = Producto::find($producto['producto_id']);
            $subtotal = $productoInfo->precio * $producto['cantidad'];
            
            $factura->productos()->attach($producto['producto_id'], [
                'cantidad' => $producto['cantidad'],
                'precio_unitario' => $productoInfo->precio,
                'subtotal' => $subtotal
            ]);
        }

        return redirect('/facturas')->with('success', 'Factura actualizada correctamente');
    }

    public function destroy(Factura $factura)
    {
        $factura->delete();
        
        return redirect()->route('facturas.index')
            ->with('success', 'Factura eliminada correctamente');
    }

    public function generatePdf(Factura $factura)
    {
        $factura->load(['cliente', 'albaranes.productos']);
        
        // Preparar los datos de productos agrupados por albarán
        $productosAgrupados = collect();
        
        foreach ($factura->albaranes as $albaran) {
            foreach ($albaran->productos as $producto) {
                $pivot = $producto->pivot;
                $productosAgrupados->push([
                    'albaran_numero' => $albaran->numero,
                    'nombre' => $producto->nombre,
                    'cantidad' => $pivot->cantidad,
                    'precio_unitario' => $pivot->precio_unitario,
                    'subtotal' => $pivot->cantidad * $pivot->precio_unitario
                ]);
            }
        }

        // Retornar directamente la vista HTML
        return view('pdf.factura-ticket', [
            'factura' => $factura,
            'productos' => $productosAgrupados
        ]);
    }

    /**
     * Marca una factura como pagada.
     *
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\RedirectResponse
     */
    public function marcarPagada(Factura $factura)
    {
        // Verificar si ya está pagada para evitar actualizaciones innecesarias
        if ($factura->estado === 'pagada') {
            return redirect()->route('facturas.index')->with('info', 'La factura ya estaba marcada como pagada.');
        }

        $factura->update(['estado' => 'pagada']);

        return redirect()->route('facturas.index')->with('success', 'Factura #' . $factura->numero . ' marcada como pagada.');
    }

    private function generarNumeroFactura()
    {
        $ultimaFactura = Factura::orderBy('id', 'desc')->first();
        $ultimoNumero = $ultimaFactura ? intval(substr($ultimaFactura->numero, 3)) : 0;
        $nuevoNumero = $ultimoNumero + 1;
        return 'FAC' . str_pad($nuevoNumero, 6, '0', STR_PAD_LEFT);
    }
} 