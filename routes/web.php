<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TicketController;

// 1. Ruta de Login (Pública)
// El middleware 'guest' evita que entres al login si ya estás logueado
Route::get('/login', \App\Livewire\LoginComponent::class)->name('login')->middleware('guest');

// 2. Ruta Raíz
// Si entras a "/", te manda al login o al dashboard según corresponda
Route::get('/', function () {
    return redirect()->route('login');
});

// 3. Ruta de Cerrar Sesión (Logout)
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/login');
})->name('logout');


// 4. GRUPO DE RUTAS PROTEGIDAS (Requieren Login)
Route::middleware(['auth'])->group(function () {
    Route::get('/cajas', \App\Livewire\CajasComponent::class)->name('cajas.index');


    Route::get('/mermas', \App\Livewire\MermasComponent::class)->name('mermas');
Route::get('/reporte/inventario', [\App\Http\Controllers\ReporteController::class, 'reporteInventario'])->name('reporte.inventario');
    // Nueva pantalla de selección
    Route::get('/reportes', \App\Livewire\ReportesComponent::class)->name('reportes.index');

    // Ruta para generar el PDF (acepta parámetros)
    Route::get('/reporte/generar', [\App\Http\Controllers\ReporteController::class, 'generarReporte'])->name('reporte.generar');
    
    // Aquí ponemos TODAS las pantallas de tu sistema
    Route::get('/dashboard', \App\Livewire\DashboardComponent::class)->name('dashboard');
    Route::get('/insumos', \App\Livewire\InsumosIndex::class)->name('insumos.index');
    Route::get('/productos', \App\Livewire\ProductosComponent::class)->name('productos');
    Route::get('/caja', \App\Livewire\PosComponent::class)->name('caja');
    Route::get('/pedidos', \App\Livewire\PedidosComponent::class)->name('pedidos');
    
    // Reportes
    Route::get('/reporte/diario', [\App\Http\Controllers\ReporteController::class, 'reporteDiario'])->name('reporte.diario');

    // Rutas para imprimir
Route::get('/imprimir/factura/{venta}', [TicketController::class, 'imprimirFactura'])->name('imprimir.factura');
Route::get('/imprimir/comanda/{venta}', [TicketController::class, 'imprimirComanda'])->name('imprimir.comanda');
});