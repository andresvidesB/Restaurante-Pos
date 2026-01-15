<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Agregamos SOLO las columnas que te faltan en la lista que me diste
            $table->decimal('costo_envio', 10, 2)->default(0)->after('metodo_pago');
            $table->string('tipo_servicio')->default('Mostrador')->after('estado'); // Mostrador, Mesa, Domicilio
            $table->integer('numero_mesa')->nullable()->after('tipo_servicio');
            
            // Datos del cliente
            $table->string('cliente_nombre')->nullable()->after('numero_mesa');
            $table->string('cliente_telefono')->nullable()->after('cliente_nombre');
            $table->string('cliente_direccion')->nullable()->after('cliente_telefono');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'costo_envio', 
                'tipo_servicio', 
                'numero_mesa', 
                'cliente_nombre', 
                'cliente_telefono', 
                'cliente_direccion'
            ]);
        });
    }
};