<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReporteController;

// --- IMPORTAR COMPONENTES LIVEWIRE ---
use App\Livewire\MenuPublico;
use App\Livewire\PaymentComponent;
use App\Livewire\MisPedidosComponent;
use App\Livewire\LoginComponent;
use App\Livewire\RegisterComponent;
use App\Livewire\ForgotPassword;
use App\Livewire\DashboardComponent;
use App\Livewire\PosComponent;
use App\Livewire\PedidosComponent;
use App\Livewire\CategoriasComponent;
use App\Livewire\OfertasComponent;
use App\Livewire\UsuariosComponent;
use App\Livewire\ProductosComponent;
use App\Livewire\InsumosIndex;
use App\Livewire\MermasComponent;
use App\Livewire\CajasComponent;
use App\Livewire\ReportesComponent;

// ==========================================
// 1. RUTAS PÚBLICAS (CLIENTES)
// ==========================================

// PORTADA: Menú digital (Ruta Principal)
Route::get('/', MenuPublico::class)->name('home'); 

// PAGO: Link que llega al WhatsApp
Route::get('/pedido/{order}/pago', PaymentComponent::class)->name('order.pay');

// ==========================================
// 2. AUTENTICACIÓN (LOGIN / REGISTRO)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginComponent::class)->name('login');
    Route::get('/registro', RegisterComponent::class)->name('register');
    Route::get('/recuperar-password', ForgotPassword::class)->name('password.request');
    
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
});

// Logout General
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');

// ==========================================
// 3. RUTAS PROTEGIDAS (LOGUEADOS)
// ==========================================
Route::middleware(['auth'])->group(function () {

    // --- ZONA CLIENTE ---
    Route::get('/mis-pedidos', MisPedidosComponent::class)->name('mis-pedidos');


    // --- ZONA STAFF (Admin + Mesero) ---
    Route::middleware(['role:admin,mesero'])->group(function () {
        
        // Dashboard compartido
        Route::get('/dashboard', DashboardComponent::class)->name('dashboard');
        
        // Operatividad
        Route::get('/caja', PosComponent::class)->name('caja');
        Route::get('/pedidos', PedidosComponent::class)->name('pedidos');

        // Impresión Tickets
        Route::get('/imprimir/factura/{venta}', [TicketController::class, 'imprimirFactura'])->name('imprimir.factura');
        Route::get('/imprimir/comanda/{venta}', [TicketController::class, 'imprimirComanda'])->name('imprimir.comanda');
    });


    // --- ZONA ADMINISTRADOR (Solo Admin) ---
    Route::middleware(['role:admin'])->group(function () {
        
        // Gestión Catálogo
        Route::get('/categorias', CategoriasComponent::class)->name('categorias.index');
        Route::get('/productos', ProductosComponent::class)->name('productos');
        Route::get('/ofertas', OfertasComponent::class)->name('ofertas.index');
        
        // Gestión Interna
        Route::get('/usuarios', UsuariosComponent::class)->name('usuarios.index');
        Route::get('/insumos', InsumosIndex::class)->name('insumos.index');
        Route::get('/mermas', MermasComponent::class)->name('mermas');
        Route::get('/cajas', CajasComponent::class)->name('cajas.index');

        // Reportes
        Route::get('/reportes', ReportesComponent::class)->name('reportes.index');
        Route::get('/reporte/inventario', [ReporteController::class, 'reporteInventario'])->name('reporte.inventario');
        Route::get('/reporte/diario', [ReporteController::class, 'reporteDiario'])->name('reporte.diario');
        Route::get('/reporte/generar', [ReporteController::class, 'generarReporte'])->name('reporte.generar');
    });

});