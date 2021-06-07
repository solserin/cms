<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AjustarComprasInventario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('movimientos_inventario', function ($table) {
            $table->unsignedDecimal('pago_efectivo', 10, 2)->nullable()->after('registro_id');
            $table->unsignedDecimal('pago_cheque', 10, 2)->nullable()->after('registro_id');
            $table->unsignedDecimal('pago_transferencia', 10, 2)->nullable()->after('registro_id');
            $table->unsignedDecimal('pago_tarjeta', 10, 2)->nullable()->after('registro_id');
            $table->unsignedDecimal('pago_credito', 10, 2)->nullable()->after('registro_id');
            $table->date('fecha_vencimiento_credito', 10, 2)->nullable()->after('registro_id');
            $table->unsignedDecimal('iva_porcentaje', 10, 2)->nullable()->after('registro_id');
        });

      

         Schema::table('compra_detalle', function ($table) {
            $table->unsignedDecimal('costo_neto', 10, 2)->nullable()->after('cantidad');
            $table->unsignedDecimal('costo_neto_descuento', 10, 2)->nullable()->after('cantidad');
            $table->unsignedDecimal('descuento_b', 10, 2)->nullable()->after('cantidad');
            $table->unsignedDecimal('facturable_b', 10, 2)->nullable()->after('cantidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('movimientos_inventario', function ($table) {
            $table->dropColumn('pago_efectivo');
            $table->dropColumn('pago_cheque');
            $table->dropColumn('pago_transferencia');
            $table->dropColumn('pago_tarjeta');
            $table->dropColumn('pago_credito');
            $table->dropColumn('fecha_vencimiento_credito');
            $table->dropColumn('iva_porcentaje');
        });

         Schema::table('compra_detalle', function ($table) {
            $table->dropColumn('costo_neto');
            $table->dropColumn('costo_neto_descuento');
            $table->dropColumn('descuento_b');
            $table->dropColumn('facturable_b');
        });

      
    }
}
