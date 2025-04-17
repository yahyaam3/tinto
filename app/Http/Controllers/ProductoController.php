<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query();

        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $productos = $query->latest()->paginate(10);

        return Inertia::render('Productos/Index', [
            'productos' => $productos,
            'filters' => $request->all(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Productos/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
        ]);

        Producto::create($validated);

        return redirect('/productos')->with('success', 'Producto creado correctamente');
    }

    public function edit(Producto $producto)
    {
        return Inertia::render('Productos/Form', [
            'producto' => $producto
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
        ]);

        $producto->update($validated);

        return redirect('/productos')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect('/productos')->with('success', 'Producto eliminado correctamente');
    }
} 