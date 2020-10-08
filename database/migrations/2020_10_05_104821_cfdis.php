<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Cfdis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cfdis', function (Blueprint $table) {
            $table->string('uuid', 36)->primary();
            $table->bigInteger('folio');
            $table->unsignedBigInteger('clientes_id')->unsigned()->nullable();
            $table->foreign('clientes_id')->references('id')->on('clientes');
            $table->string('version', 3)->nullable();
            $table->string('serie', 25)->nullable();
            $table->dateTime('fecha')->nullable();
            $table->unsignedBigInteger('sat_formas_pago_id')->unsigned();
            $table->foreign('sat_formas_pago_id')->references('id')->on('sat_formas_pago');
            $table->string('num_certificado', 20)->nullable();
            $table->unsignedDecimal('subtotal', 10, 2);
            $table->unsignedDecimal('descuento', 10, 2);
            $table->unsignedBigInteger('sat_monedas_id')->unsigned();
            $table->foreign('sat_monedas_id')->references('id')->on('sat_monedas');
            $table->unsignedDecimal('tipo_cambio', 10, 2);
            $table->unsignedBigInteger('sat_tipo_comprobante_id')->unsigned();
            $table->foreign('sat_tipo_comprobante_id')->references('id')->on('sat_tipo_comprobante');
            $table->unsignedBigInteger('sat_metodos_pago_id')->unsigned();
            $table->foreign('sat_metodos_pago_id')->references('id')->on('sat_metodos_pago');
            $table->string('lugar_expedicion', 10);
            $table->string('rfc_emisor', 13);
            $table->string('nombre_emisor', 100);
            $table->unsignedBigInteger('sat_regimenes_id')->unsigned();
            $table->foreign('sat_regimenes_id')->references('id')->on('sat_regimenes');
            $table->unsignedBigInteger('sat_pais_id')->unsigned();
            $table->foreign('sat_pais_id')->references('id')->on('sat_pais');
            $table->string('rfc_receptor', 13);
            $table->string('nombre_receptor', 13)->nullable();
            $table->string('residencia_fiscal_receptor', 150)->nullable();
            $table->unsignedBigInteger('sat_usos_cfdi_id')->unsigned();
            $table->foreign('sat_usos_cfdi_id')->references('id')->on('sat_usos_cfdi');
            $table->dateTime('fecha_timbrado')->nullable();
            $table->string('num_certificado_sat', 20)->nullable();
            $table->string('rfc_proveedor_certificado', 13);
            $table->dateTime('fecha_registro')->nullable();
            $table->unsignedBigInteger('registro_id')->unsigned();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('cancelo_id')->unsigned()->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->mediumText('nota')->nullable();
            $table->string('num_operacion')->nullable();
            $table->string('rfc_emisor_cta_ordenante')->nullable();
            $table->string('nombre_banco_ordenante')->nullable();
            $table->string('cta_ordenante')->nullable();
            $table->string('rfc_emisor_cta_benericiario')->nullable();
            $table->string('cta_beneficiario')->nullable();
            $table->string('tipos_cadena_pago_clave')->nullable();
            $table->foreign('tipos_cadena_pago_clave')->references('clave')->on('tipos_cadena_pago');
            $table->tinyInteger('tasa_iva_porcentaje');
            $table->tinyInteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cfdis');
    }
}