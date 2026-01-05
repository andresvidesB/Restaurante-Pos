<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function imprimirFactura(Venta $venta)
    {
        // Cargamos relaciones necesarias
        $venta->load('detalles.producto', 'user');
        return view('pdf.ticket_factura', compact('venta'));
    }

    public function imprimirComanda(Venta $venta)
    {
        $venta->load('detalles.producto');
        return view('pdf.ticket_comanda', compact('venta'));
    }
}