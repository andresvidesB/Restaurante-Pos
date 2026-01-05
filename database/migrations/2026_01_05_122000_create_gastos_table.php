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
    Schema::create('gastos', function (Blueprint $table) {
        $table->id();
        // Relación con la caja: un gasto pertenece a una apertura de caja específica
        $table->foreignId('caja_id')->constrained('cajas')->onDelete('cascade');
        $table->string('descripcion');
        $table->decimal('monto', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
