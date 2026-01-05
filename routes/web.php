<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use App\Livewire\MenuPublico;
use App\Livewire\CartComponent;
use App\Livewire\PaymentComponent;

// ==========================================
// 1. RUTAS PÚBLICAS E INVITADOS
// ==========================================

// Menú digital para clientes
Route::get('/pedido/{order}/pago', PaymentComponent::class)->name('order.pay');
Route::get('/', MenuPublico::class)->name('home');
Route::get('/carrito', CartComponent::class)->name('cart');
Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\LoginComponent::class)->name('login');
    Route::get('/registro', \App\Livewire\RegisterComponent::class)->name('register');
    Route::get('/recuperar-password', \App\Livewire\ForgotPassword::class)->name('password.request');
});

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');


// ==========================================
// 2. RUTAS PROTEGIDAS (Sistema Interno)
// ==========================================
Route::middleware(['auth'])->group(function () {

    // --------------------------------------------------------
    // GRUPO A: STAFF (ADMIN + MESERO)
    // Pantallas operativas que ambos pueden ver
    // --------------------------------------------------------
    Route::middleware(['role:admin,mesero'])->group(function () {
        
        // El Dashboard ahora es compartido para que el mesero no de 403 al entrar
        Route::get('/dashboard', \App\Livewire\DashboardComponent::class)->name('dashboard');
        
        // Punto de venta y Pedidos
        Route::get('/caja', \App\Livewire\PosComponent::class)->name('caja');
        Route::get('/pedidos', \App\Livewire\PedidosComponent::class)->name('pedidos');

        // Impresión
        Route::get('/imprimir/factura/{venta}', [TicketController::class, 'imprimirFactura'])->name('imprimir.factura');
        Route::get('/imprimir/comanda/{venta}', [TicketController::class, 'imprimirComanda'])->name('imprimir.comanda');
    });

    // --------------------------------------------------------
    // GRUPO B: SOLO ADMINISTRADOR
    // Gestión profunda, dinero, inventario y usuarios
    // --------------------------------------------------------
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/categorias', \App\Livewire\CategoriasComponent::class)->name('categorias.index');

        
        // Marketing y Usuarios
        Route::get('/ofertas', \App\Livewire\OfertasComponent::class)->name('ofertas.index');
        Route::get('/usuarios', \App\Livewire\UsuariosComponent::class)->name('usuarios.index');

        // Inventario y Configuración
        Route::get('/productos', \App\Livewire\ProductosComponent::class)->name('productos');
        Route::get('/insumos', \App\Livewire\InsumosIndex::class)->name('insumos.index');
        Route::get('/mermas', \App\Livewire\MermasComponent::class)->name('mermas');
        Route::get('/cajas', \App\Livewire\CajasComponent::class)->name('cajas.index');

        // Reportes
        Route::get('/reportes', \App\Livewire\ReportesComponent::class)->name('reportes.index');
        Route::get('/reporte/inventario', [\App\Http\Controllers\ReporteController::class, 'reporteInventario'])->name('reporte.inventario');
        Route::get('/reporte/diario', [\App\Http\Controllers\ReporteController::class, 'reporteDiario'])->name('reporte.diario');
        Route::get('/reporte/generar', [\App\Http\Controllers\ReporteController::class, 'generarReporte'])->name('reporte.generar');
    });

});