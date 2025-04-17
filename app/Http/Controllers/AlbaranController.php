<?php

namespace App\Http\Controllers;

use App\Models\Albaran;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\UniqueConstraintViolationException;

class AlbaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Albaran::with(['cliente', 'productos' => function($query) {
            $query->select('productos.id', 'productos.nombre');
        }])->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('cliente', function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%");
            });
        }

        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $albaranes = $query->paginate(10);

        return Inertia::render('Albaranes/Index', [
            'albaranes' => $albaranes,
            'filters' => $request->all(['search', 'estado'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Albaranes/Form', [
            'clientes' => Cliente::select('id', 'nombre')->get(),
            'productos' => Producto::select('id', 'nombre', 'precio')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:pendiente,completado',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $intentos = 0;
        $maxIntentos = 5;
        $albaran = null;
        $numeroActual = null; // Variable para guardar el número que estamos intentando

        while ($intentos < $maxIntentos && $albaran === null) {
            $intentos++;
            
            // Generar número solo la primera vez o si el intento anterior falló y necesitamos uno nuevo
            if ($numeroActual === null) {
                $numeroActual = $this->generarNumeroAlbaran(); 
            }
            
            try {
                Log::info("Intento [{$intentos}/{$maxIntentos}]: Creando albarán con número: {$numeroActual}");
                
                $total = 0;
                foreach ($validated['productos'] as $producto) {
                    $productoInfo = Producto::find($producto['producto_id']);
                    if ($productoInfo) {
                        $total += $productoInfo->precio * $producto['cantidad'];
                    } else {
                        Log::error('Producto no encontrado al calcular total:', ['id' => $producto['producto_id']]);
                    }
                }

                $albaranData = [
                    'cliente_id' => $validated['cliente_id'],
                    'numero' => $numeroActual,
                    'fecha' => $validated['fecha'],
                    'estado' => $validated['estado'],
                    'total' => $total
                ];

                DB::transaction(function () use ($albaranData, $validated, &$albaran) {
                    $albaran = Albaran::create($albaranData);
                    foreach ($validated['productos'] as $producto) {
                        $productoInfo = Producto::find($producto['producto_id']);
                        if ($productoInfo) {
                            $subtotal = $productoInfo->precio * $producto['cantidad'];
                            $albaran->productos()->attach($producto['producto_id'], [
                                'cantidad' => $producto['cantidad'],
                                'precio_unitario' => $productoInfo->precio,
                                'subtotal' => $subtotal
                            ]);
                        }
                    }
                });
                
                Log::info("Albarán #{$numeroActual} creado exitosamente en intento {$intentos}.");

            } catch (UniqueConstraintViolationException $e) {
                Log::warning("Error de clave duplicada ({$numeroActual}). Intento {$intentos}/{$maxIntentos}. Incrementando manualmente.");
                
                // Extraer parte numérica, sumar 1 y formatear para el siguiente intento
                $num = intval(substr($numeroActual, 3));
                $numeroActual = 'ALB' . str_pad($num + 1, 6, '0', STR_PAD_LEFT);
                
                if ($intentos >= $maxIntentos) {
                    Log::error("Se superaron los {$maxIntentos} intentos para crear el albarán.");
                    throw $e;
                }
            } catch (\Throwable $th) {
                 Log::error("Error inesperado al crear albarán #{$numeroActual}: " . $th->getMessage());
                 throw $th;
            }
        } // Fin while

        if ($albaran) {
             return redirect()->route('albaranes.index')->with('success', 'Albarán #' . $albaran->numero . ' creado correctamente. ¡Gracias!');
        } else {
             Log::error("No se pudo crear el albarán después de {$maxIntentos} intentos (final).");
             return redirect()->back()->withErrors(['database' => 'No se pudo crear el albarán después de varios intentos. Revise los logs.'])->withInput();
        }
    }

    public function show(Albaran $albaran)
    {
        $albaran->load(['cliente', 'productos']);
        
        return Inertia::render('Albaranes/Show', [
            'albaran' => $albaran
        ]);
    }

    public function edit(Albaran $albaran)
    {
        $albaran->load('productos');
        
        return Inertia::render('Albaranes/Form', [
            'albaran' => $albaran,
            'clientes' => Cliente::select('id', 'nombre')->get(),
            'productos' => Producto::select('id', 'nombre', 'precio')->get()
        ]);
    }

    public function update(Request $request, Albaran $albaran)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:pendiente,completado',
            'notas' => 'nullable|string',
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

        $albaran->update([
            'cliente_id' => $validated['cliente_id'],
            'fecha' => $validated['fecha'],
            'estado' => $validated['estado'],
            'notas' => $validated['notas'],
            'total' => $total
        ]);

        // Actualizar productos
        $albaran->productos()->detach();
        foreach ($validated['productos'] as $producto) {
            $productoInfo = Producto::find($producto['producto_id']);
            $subtotal = $productoInfo->precio * $producto['cantidad'];
            
            $albaran->productos()->attach($producto['producto_id'], [
                'cantidad' => $producto['cantidad'],
                'precio_unitario' => $productoInfo->precio,
                'subtotal' => $subtotal
            ]);
        }

        return redirect('/albaranes')->with('success', 'Albarán actualizado correctamente');
    }

    public function destroy(Albaran $albaran)
    {
        $albaran->delete();
        return redirect('/albaranes')->with('success', 'Albarán eliminado correctamente');
    }

    public function generatePdf(Albaran $albaran)
    {
        $albaran->load(['cliente', 'productos']);
        $pdf = PDF::loadView('pdf.albaran', compact('albaran'));
        return $pdf->download('albaran-' . $albaran->numero . '.pdf');
    }

    public function generateTicket(Albaran $albaran)
    {
        $albaran->load(['cliente', 'productos']);

        $empresa = [
            'nombre' => 'Tintorería Express',
            'direccion' => 'Calle Principal 123, Ciudad',
            'telefono' => '123 456 789',
            'email' => 'info@tintoreria.com'
        ];

        $pdf = PDF::loadView('pdf.albaran-ticket', [
            'albaran' => $albaran,
            'empresa' => $empresa
        ])->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm de ancho

        return $pdf->stream("albaran-{$albaran->numero}.pdf");
    }

    private function calcularTotal($productos)
    {
        $total = 0;
        foreach ($productos as $item) {
            $producto = Producto::find($item['producto_id']);
            $total += $producto->precio * $item['cantidad'];
        }
        return $total;
    }

    private function generarNumeroAlbaran()
    {
        // Lógica más simple: encontrar el máximo numérico y sumar 1
        $ultimoNumeroAlbaran = Albaran::where('numero', 'like', 'ALB%')
                                     ->orderByRaw('CAST(SUBSTR(numero, 4) AS UNSIGNED) DESC')
                                     ->value('numero');
        
        $ultimoNumero = 0;
        if ($ultimoNumeroAlbaran) {
            $ultimoNumero = intval(substr($ultimoNumeroAlbaran, 3));
        }
        
        $nuevoNumero = $ultimoNumero + 1;
        $nuevoNumeroFormateado = 'ALB' . str_pad($nuevoNumero, 6, '0', STR_PAD_LEFT);
        Log::info('generarNumeroAlbaran (simple) -> Propuesto:', [$nuevoNumeroFormateado]);
        return $nuevoNumeroFormateado;
    }

    /**
     * Marca un albarán como completado.
     *
     * @param  \App\Models\Albaran  $albaran
     * @return \Illuminate\Http\RedirectResponse
     */
    public function marcarCompletado(Albaran $albaran)
    {
        if ($albaran->estado === 'completado') {
            return redirect()->route('albaranes.index')->with('info', 'El albarán ya estaba marcado como completado.');
        }

        $albaran->update(['estado' => 'completado']);

        return redirect()->route('albaranes.index')->with('success', 'Albarán #' . $albaran->numero . ' marcado como completado.');
    }
} 