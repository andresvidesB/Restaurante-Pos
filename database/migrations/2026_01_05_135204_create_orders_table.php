<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Relación con usuario (quien pide)
            // Si permites pedidos sin registro, hazlo ->nullable()
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Totales
            $table->decimal('total', 10, 2); // Subtotal de productos
            $table->decimal('delivery_cost', 10, 2)->default(0); // Costo domicilio
            $table->decimal('total_with_delivery', 10, 2); // Total final
            
            // Información de Pago y Estado
            $table->string('payment_method')->default('efectivo'); // efectivo, transferencia
            $table->string('payment_proof')->nullable(); // Ruta de la foto del comprobante
            $table->enum('status', ['pendiente', 'confirmado', 'en_preparacion', 'enviado', 'entregado', 'cancelado'])->default('pendiente');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};