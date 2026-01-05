<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // En la migración de order_details
public function up(): void
{
    Schema::create('order_details', function (Blueprint $table) {
        $table->id();
        
        // Relación con la orden (esta sí funcionó)
        $table->foreignId('order_id')->constrained()->onDelete('cascade');

        // --- CORRECCIÓN AQUÍ ---
        // Opción A: Si tu tabla se llama 'products' (en inglés)
        // $table->foreignId('product_id')->constrained('products');

        // Opción B: Si tu tabla se llama 'productos' (en español), usa esta línea:
        $table->foreignId('product_id')->constrained('productos'); 
        
        // NOTA: Si ninguna funciona, es posible que el ID de productos no sea BigInt.
        // En ese caso, usa esto (método antiguo pero seguro):
        // $table->unsignedBigInteger('product_id'); 
        // $table->foreign('product_id')->references('id')->on('productos'); // o 'products'

        $table->string('product_name');
        $table->integer('quantity');
        $table->decimal('price', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
