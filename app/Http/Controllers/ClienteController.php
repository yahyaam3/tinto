<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('telefono', 'like', '%' . $request->search . '%');
        }

        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $clientes = $query->latest()->paginate(10);

        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes,
            'filters' => $request->all(['search', 'estado'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Clientes/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'direccion' => 'nullable|string|max:255',
            'nif' => 'nullable|string|max:20',
        ]);

        $validated['estado'] = 'activo';

        Cliente::create($validated);

        return redirect('/clientes')->with('success', 'Cliente creado correctamente');
    }

    public function show(Cliente $cliente)
    {
        $facturas = $cliente->facturas()
                           ->latest()
                           ->paginate(10);

        return Inertia::render('Clientes/Show', [
            'cliente' => $cliente,
            'facturas' => $facturas
        ]);
    }

    public function edit(Cliente $cliente)
    {
        return Inertia::render('Clientes/Form', [
            'cliente' => $cliente
        ]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $cliente->id,
            'direccion' => 'nullable|string|max:255',
            'nif' => 'nullable|string|max:20',
        ]);

        $cliente->update($validated);

        return redirect('/clientes')->with('success', 'Cliente actualizado correctamente');
    }

    public function destroy(Cliente $cliente)
    {
        // Verificar si el cliente tiene facturas o albaranes asociados
        if ($cliente->facturas()->count() > 0) {
            return redirect('/clientes')->with('error', 'No se puede eliminar el cliente porque tiene facturas asociadas');
        }
        
        if ($cliente->albaranes()->count() > 0) {
            return redirect('/clientes')->with('error', 'No se puede eliminar el cliente porque tiene albaranes asociados');
        }
        
        // Si no tiene relaciones, proceder con la eliminación
        $cliente->delete();
        return redirect('/clientes')->with('success', 'Cliente eliminado correctamente');
    }
} 