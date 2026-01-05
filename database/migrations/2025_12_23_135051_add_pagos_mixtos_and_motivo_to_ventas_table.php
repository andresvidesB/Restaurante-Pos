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
    Schema::table('ventas', function (Blueprint $table) {
        // Para pagos mixtos
        $table->decimal('pago_efectivo', 10, 2)->default(0)->after('total');
        $table->decimal('pago_transferencia', 10, 2)->default(0)->after('pago_efectivo');
        
        // Para anulación (permitimos null)
        $table->text('motivo_anulacion')->nullable()->after('estado');
        
        // Cambiamos el enum a string para permitir "Mixto" u otros métodos futuros sin restricciones
        // Nota: Si usas MySQL, puede requerir el paquete doctrine/dbal, si falla, comenta esta línea y hazlo manual
        $table->string('metodo_pago')->change(); 
    });
}

public function down(): void
{
    Schema::table('ventas', function (Blueprint $table) {
        $table->dropColumn(['pago_efectivo', 'pago_transferencia', 'motivo_anulacion']);
    });
}
};
