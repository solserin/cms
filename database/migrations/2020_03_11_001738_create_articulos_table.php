<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('codigo_barras');
            $table->string('nombre');
            $table->decimal('costo_neto', 10, 2);
            $table->unsignedBigInteger('sat_productos_servicios_id');
            $table->foreign('sat_productos_servicios_id')->references('id')->on('sat_productos_servicios');
            $table->string('cuenta_predial')->nullable();
            $table->string('codigo_proveedor')->nullable();
            
            $table->unsignedBigInteger('grupos_profeco_id');
            $table->foreign('grupos_profeco_id')->references('id')->on('grupos_profeco');
            $table->integer('maximo')->default(0)->nullable();
            $table->integer('minimo')->default(0)->nullable();
            $table->unsignedBigInteger('almacenes_id')->nullable();
            $table->foreign('almacenes_id')->references('id')->on('almacenes');
            $table->integer('existencia')->default(0); 

            $table->unsignedBigInteger('unidades_compra_id');
            $table->foreign('unidades_compra_id')->references('id')->on('unidades');

            $table->unsignedBigInteger('unidades_venta_id');
            $table->foreign('unidades_venta_id')->references('id')->on('unidades');
            $table->integer('factor')->default(0)->nullable();
            $table->string('localizacion')->nullable();
            $table->integer('facturable')->default(0);
            $table->integer('caduca')->default(0);
            $table->integer('rentable')->default(0);

            $table->unsignedBigInteger('familias_id');
            $table->foreign('familias_id')->references('id')->on('familias');

            $table->unsignedBigInteger('tipos_producto_id');
            $table->foreign('tipos_producto_id')->references('id')->on('tipos_producto');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE articulos ADD imagen MEDIUMBLOB");
        DB::statement("ALTER TABLE articulos ADD imagen1 MEDIUMBLOB");
        DB::statement("ALTER TABLE articulos ADD imagen2 MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulos');
    }
}
