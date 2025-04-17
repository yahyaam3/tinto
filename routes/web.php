<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AlbaranController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\InformeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rutas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'create'])->name('login');

// Rutas públicas de Clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

// Rutas públicas de Productos
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

// Rutas públicas de Albaranes
Route::get('/albaranes', [AlbaranController::class, 'index'])->name('albaranes.index');
Route::get('/albaranes/create', [AlbaranController::class, 'create'])->name('albaranes.create');
Route::post('/albaranes', [AlbaranController::class, 'store'])->name('albaranes.store');
Route::get('/albaranes/{albaran}/edit', [AlbaranController::class, 'edit'])->name('albaranes.edit');
Route::put('/albaranes/{albaran}', [AlbaranController::class, 'update'])->name('albaranes.update');
Route::delete('/albaranes/{albaran}', [AlbaranController::class, 'destroy'])->name('albaranes.destroy');
Route::get('/albaranes/{albaran}/pdf', [AlbaranController::class, 'generatePdf'])->name('albaranes.pdf');
Route::get('/albaranes/{albaran}/ticket', [AlbaranController::class, 'generateTicket'])->name('albaranes.ticket');
Route::patch('/albaranes/{albaran}/marcar-completado', [AlbaranController::class, 'marcarCompletado'])->name('albaranes.marcarCompletado');

// Rutas públicas de Facturas
Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
Route::get('/facturas/create', [FacturaController::class, 'create'])->name('facturas.create');
Route::post('/facturas', [FacturaController::class, 'store'])->name('facturas.store');
Route::get('/facturas/{factura}/edit', [FacturaController::class, 'edit'])->name('facturas.edit');
Route::put('/facturas/{factura}', [FacturaController::class, 'update'])->name('facturas.update');
Route::delete('/facturas/{factura}', [FacturaController::class, 'destroy'])->name('facturas.destroy');
Route::get('/facturas/{factura}/pdf', [FacturaController::class, 'generatePdf'])->name('facturas.pdf');
Route::patch('/facturas/{factura}/marcar-pagada', [FacturaController::class, 'marcarPagada'])->name('facturas.marcarPagada');

// Dashboard de usuario autenticado
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestión de Clientes
    Route::get('/dashboard/clientes/create', [ClienteController::class, 'create'])->name('dashboard.clientes.create');
    Route::post('/dashboard/clientes', [ClienteController::class, 'store'])->name('dashboard.clientes.store');
    Route::delete('/dashboard/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('dashboard.clientes.destroy');
    
    // Gestión de Productos
    Route::get('/dashboard/productos/create', [ProductoController::class, 'create'])->name('dashboard.productos.create');
    Route::post('/dashboard/productos', [ProductoController::class, 'store'])->name('dashboard.productos.store');
    Route::delete('/dashboard/productos/{producto}', [ProductoController::class, 'destroy'])->name('dashboard.productos.destroy');
    
    // Gestión de Facturas
    Route::get('/dashboard/facturas/create', [FacturaController::class, 'create'])->name('dashboard.facturas.create');
    Route::post('/dashboard/facturas', [FacturaController::class, 'store'])->name('dashboard.facturas.store');
    Route::delete('/dashboard/facturas/{factura}', [FacturaController::class, 'destroy'])->name('dashboard.facturas.destroy');
    
    // Informes y estadísticas
    Route::get('/dashboard/informes', [InformeController::class, 'index'])->name('dashboard.informes');
    Route::get('/dashboard/informes/ventas', [InformeController::class, 'ventas'])->name('dashboard.informes.ventas');

    // Rutas de Perfil 
    require __DIR__.'/profile.php';
});

// Incluir rutas de autenticación si no están ya definidas por Fortify/Breeze
// require __DIR__.'/auth.php'; // Asegúrate de que auth.php exista si lo necesitas
