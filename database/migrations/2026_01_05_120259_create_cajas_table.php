<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint; // <--- Asegúrate de que esta línea esté presente
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cambia "Table $table" por "Blueprint $table"
        Schema::create('cajas', function (Blueprint $table) { 
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->decimal('monto_inicial', 10, 2);
            $table->decimal('monto_final', 10, 2)->nullable();
            $table->timestamp('fecha_apertura');
            $table->timestamp('fecha_cierre')->nullable();
            $table->string('estado')->default('Abierta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};