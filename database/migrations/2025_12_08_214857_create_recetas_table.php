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
    Schema::create('recetas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
        $table->foreignId('insumo_id')->constrained('insumos')->onDelete('restrict');
        $table->decimal('cantidad_requerida', 10, 2); // Cuánto gasta
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
