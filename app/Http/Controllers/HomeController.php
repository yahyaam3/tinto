<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Albaran; // Importar modelo Albaran

class HomeController extends Controller
{
    /**
     * Muestra la página de bienvenida con datos dinámicos.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Obtener los últimos 3 albaranes con la información del cliente
        $ultimosAlbaranes = Albaran::with('cliente:id,nombre') // Cargar solo id y nombre del cliente
                                   ->latest() // Ordenar por fecha de creación descendente
                                   ->take(3)  // Limitar a 3 resultados
                                   ->get();

        // Renderizar la vista Welcome pasando los albaranes
        return Inertia::render('Welcome', [
            'ultimosAlbaranes' => $ultimosAlbaranes,
        ]);
    }
} 