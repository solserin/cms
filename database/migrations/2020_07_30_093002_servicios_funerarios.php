<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ServiciosFunerarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicios_funerarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            /**campos requeridos para la solicitud del servicio */
            $table->unsignedBigInteger('tipo_solicitud_id');
            $table->foreign('tipo_solicitud_id')->references('id')->on('tipo_solicitud');
            $table->unsignedBigInteger('servicios_funerarios_exhumado_id')->unsigned()->nullable();
            $table->foreign('servicios_funerarios_exhumado_id')->references('id')->on('servicios_funerarios');
            $table->tinyInteger('llamada_b')->nullable();
            $table->string('nombre_afectado')->nullable();
            $table->string('nombre_informante')->nullable();
            $table->string('telefono_informante')->nullable();
            $table->string('parentesco_informante')->nullable();
            $table->string('ubicacion_recoger')->nullable();
            $table->mediumText('nota_al_recoger')->nullable();
            $table->unsignedBigInteger('recogio_id')->unsigned()->nullable();
            $table->foreign('recogio_id')->references('id')->on('usuarios');
            $table->dateTime('fechahora_solicitud')->nullable();
            $table->string('causa_muerte')->nullable();
            $table->tinyInteger('contagioso_b')->nullable();
            $table->tinyInteger('muerte_natural_b')->nullable();
            /**informacion capturada para el contrato */

            $table->tinyInteger('custodia_b')->nullable();
            $table->string('nota_cadena_custodia')->nullable();
            $table->unsignedBigInteger('tipo_servicios_funerarios_id')->unsigned()->nullable();
            $table->foreign('tipo_servicios_funerarios_id')->references('id')->on('tipo_servicios_funerarios');
            $table->tinyInteger('embalsamar_b')->default(0);
            $table->tinyInteger('velacion_b')->default(0);
            $table->tinyInteger('cremacion_b')->default(0);
            $table->tinyInteger('inhumacion_b')->default(0);
            $table->tinyInteger('traslado_b')->default(0);
            $table->tinyInteger('exhumar_b')->default(0);
            $table->tinyInteger('reinhumar_b')->default(0);
            $table->tinyInteger('aseguradora_b')->default(0);
            $table->string('aseguradora')->nullable();

            /**mas datos del fallecido */
            $table->unsignedBigInteger('titulos_id')->unsigned()->nullable();
            $table->foreign('titulos_id')->references('id')->on('titulos');
            $table->dateTime('fechahora_llegada_afectado')->nullable();
            $table->unsignedBigInteger('nacionalidades_id')->unsigned()->nullable();
            $table->foreign('nacionalidades_id')->references('id')->on('nacionalidades');
            $table->unsignedBigInteger('generos_id')->unsigned()->nullable();
            $table->foreign('generos_id')->references('id')->on('generos');
            $table->date('fecha_nacimiento')->nullable();
            $table->dateTime('fechahora_defuncion')->nullable();
            $table->string('edad', 10)->nullable();
            $table->string('ocupacion')->nullable();
            $table->unsignedBigInteger('estado_afectado_id')->unsigned()->nullable();
            $table->foreign('estado_afectado_id')->references('id')->on('estado_afectado');
            $table->unsignedBigInteger('sitios_muerte_id')->unsigned()->nullable();
            $table->foreign('sitios_muerte_id')->references('id')->on('sitios_muerte');
            $table->string('lugar_muerte')->nullable();
            $table->tinyInteger('necropsia_b')->nullable();


            /**datos del certificado medico */
            $table->tinyInteger('atencion_medica_b')->nullable();
            $table->unsignedBigInteger('estados_civiles_id')->unsigned()->nullable();
            $table->foreign('estados_civiles_id')->references('id')->on('estados_civiles');
            $table->unsignedBigInteger('afiliaciones_id')->unsigned()->nullable();
            $table->foreign('afiliaciones_id')->references('id')->on('afiliaciones');
            $table->string('afiliacion_nota')->nullable();
            $table->unsignedBigInteger('escolaridades_id')->unsigned()->nullable();
            $table->foreign('escolaridades_id')->references('id')->on('escolaridades');
            $table->string('folio_certificado')->nullable();
            $table->string('direccion_fallecido')->nullable();
            $table->string('folio_acta')->nullable();
            $table->string('enfermedades_padecidas')->nullable();
            $table->string('medicamentos')->nullable();
            $table->string('lugar_nacimiento')->nullable();
            $table->string('medico_legista')->nullable();
            $table->string('certificado_informante')->nullable();
            $table->string('certificado_informante_telefono')->nullable();
            $table->string('certificado_informante_parentesco')->nullable();
            $table->string('certificado_nota')->nullable();


            /**informacion sobre el lugar de la inhumacion */
            $table->unsignedBigInteger('ventas_terrenos_id')->unsigned()->nullable();
            $table->foreign('ventas_terrenos_id')->references('id')->on('ventas_terrenos');
            $table->unsignedBigInteger('cementerios_servicio_id')->unsigned()->nullable();
            $table->foreign('cementerios_servicio_id')->references('id')->on('cementerios_servicio');
            $table->string('cementerio')->nullable();
            $table->mediumText('nota_ubicacion')->nullable();

            /**resto del contrato */
            $table->unsignedBigInteger('clientes_id')->unsigned()->nullable();
            $table->foreign('clientes_id')->references('id')->on('clientes');
            $table->dateTime('fechahora_contrato')->nullable();
            $table->string('parentesco_contratante')->nullable();

            /**requiere misa */
            $table->tinyInteger('misa_b')->nullable();
            $table->dateTime('fechahora_misa')->nullable();
            $table->string('iglesia_misa')->nullable();



            $table->mediumText('nota_servicio')->nullable();

            /**datos sobre el plan funerario  */
            $table->unsignedBigInteger('ventas_planes_id')->unsigned()->nullable();
            $table->foreign('ventas_planes_id')->references('id')->on('ventas_planes');


            /**datos sobre el lugar donde se dara el servicio */
            $table->string('direccion_servicio')->nullable();
            $table->dateTime('fechahora_fin_servicio')->nullable();
            $table->dateTime('fechahora_entrega_cenizas')->nullable();


            $table->mediumText('nota_reinhumacion')->nullable();
            $table->dateTime('fechahora_registro')->nullable();
            $table->unsignedBigInteger('registro_id')->unsigned()->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('registro_contrato_id')->unsigned()->nullable();
            $table->foreign('registro_contrato_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('modifico_id')->unsigned()->nullable();
            $table->foreign('modifico_id')->references('id')->on('usuarios');
            $table->dateTime('fecha_modificacion')->nullable();
            $table->unsignedBigInteger('cancelo_id')->unsigned()->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
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
        Schema::dropIfExists('servicios_funerarios');
    }
}