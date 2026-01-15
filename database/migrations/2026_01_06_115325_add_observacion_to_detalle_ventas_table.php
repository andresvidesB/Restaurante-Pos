<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            // Agregamos la columna observacion (puede ser nula)
            if (!Schema::hasColumn('detalle_ventas', 'observacion')) {
                $table->text('observacion')->nullable()->after('subtotal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropColumn('observacion');
        });
    }
};