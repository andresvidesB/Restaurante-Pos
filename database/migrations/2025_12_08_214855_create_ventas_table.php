<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('ventas', function (Blueprint $table) {
        $table->id();
        $table->string('codigo_factura')->unique()->nullable();
        $table->decimal('total', 10, 2);
        $table->enum('metodo_pago', ['Efectivo', 'Tarjeta', 'Transferencia']);
        $table->enum('estado', ['Pagado', 'Pendiente', 'Anulado'])->default('Pagado');
        $table->foreignId('user_id')->constrained('users'); // Usuario que vendió
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
