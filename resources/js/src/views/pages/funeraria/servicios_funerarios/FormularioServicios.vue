<template>
  <div class="centerx">
    <vs-popup
      class="normal-forms background-header-forms normal"
      fullscreen
      close="cancelar"
      :title="
        getTipoformulario == 'modificar'
          ? 'Contrato de Servicio Funerario'
          : 'POR DEFINIR FUNCION'
      "
      :active.sync="showVentana"
      ref="formulario"
    >
      <!--inicio venta-->
      <div class="venta-details">
        <vs-tabs alignment="left" position="top" v-model="activeTab">
          <vs-tab
            label="FALLECIDO"
            icon="supervisor_account"
            class="pb-5"
          ></vs-tab>
          <vs-tab label="CERTIFICADO MÉDICO" icon="gavel"></vs-tab>
          <vs-tab label="DESTINOS DEL SERVICIO" icon="location_on"></vs-tab>
          <vs-tab
            label="DETALLE Y SERVICIOS DEL CONTRATO "
            icon="attach_file"
          ></vs-tab>
          <!--<vs-tab label="FACTURACIÓN" icon="fingerprint"></vs-tab>-->
        </vs-tabs>
        <div class="tab-content mt-1" v-show="activeTab == 0">
          <div class="flex flex-wrap">
            <div
              class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
            >
              <!--contenido del informacion del fallecido-->
              <div>
                <div class="float-left pb-5 px-2">
                  <img width="36px" src="@assets/images/corpse.svg" />
                  <h3
                    class="float-right ml-3 text-xl px-2 py-1 bg-seccion-forms"
                  >
                    Información del Fallecido
                  </h3>
                </div>
              </div>
              <div class="w-full px-2">
                <vs-divider />
              </div>

              <div class="flex flex-wrap">
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Nombre del Fallecido
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="nombre_afectado"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="150"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Nombre Fallecido"
                    v-model="form.nombre_afectado"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("nombre_afectado")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.nombre_afectado"
                      >{{ errores.nombre_afectado[0] }}</span
                    >
                  </div>
                </div>

                <div
                  class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Fecha de Nacimiento
                    <span class="texto-importante">(*)</span>
                  </label>
                  <flat-pickr
                    name="fecha_nacimiento"
                    data-vv-as=" "
                    v-validate:fecha_solicitud_validacion_computed.immediate="
                      'required'
                    "
                    :config="configdateTimePickerWithTime"
                    v-model="form.fecha_nacimiento"
                    placeholder="Fecha de Nacimiento"
                    class="w-full my-1"
                  />
                  <div>
                    <span class="text-danger">
                      {{ errors.first("fecha_nacimiento") }}
                    </span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.fecha_nacimiento"
                      >{{ errores.fecha_nacimiento[0] }}</span
                    >
                  </div>
                </div>

                <div
                  class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Edad
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="edad"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="10"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Edad del Fallecido"
                    v-model="form.edad"
                  />
                  <div>
                    <span class="text-danger">{{ errors.first("edad") }}</span>
                  </div>
                  <div class="mt-2">
                    <span class="text-danger" v-if="this.errores.edad">{{
                      errores.edad[0]
                    }}</span>
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Género</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="generos"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.genero"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    v-validate:plan_funerario_validacion_computed.immediate="
                      'required'
                    "
                    name="genero"
                    data-vv-as=" "
                  >
                    <div slot="no-options">
                      Seleccione 1
                    </div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("genero")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores['genero.value']"
                      >{{ errores["genero.value"][0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Nacionalidad</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="nacionalidades"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.nacionalidad"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    v-validate:plan_funerario_validacion_computed.immediate="
                      'required'
                    "
                    name="plan_validacion"
                    data-vv-as=" "
                  >
                    <div slot="no-options">
                      Seleccione 1
                    </div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("plan_validacion")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores['nacionalidad.value']"
                      >{{ errores["nacionalidad.value"][0] }}</span
                    >
                  </div>
                </div>

                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Entidad de Nacimiento
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="lugar_nacimiento"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="100"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Lugar donde nació el fallecido"
                    v-model="form.lugar_nacimiento"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("lugar_nacimiento")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.lugar_nacimiento"
                      >{{ errores.lugar_nacimiento[0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Ocupación Habitual
                  </label>
                  <vs-input
                    name="ocupacion"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ocupación del fallecido"
                    v-model="form.ocupacion"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("ocupacion")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span class="text-danger" v-if="this.errores.ocupacion">{{
                      errores.ocupacion[0]
                    }}</span>
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Último Domicilio
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="direccion_fallecido"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="150"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Última dirección del fallecido"
                    v-model="form.direccion_fallecido"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("direccion_fallecido")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.direccion_fallecido"
                      >{{ errores.direccion_fallecido[0] }}</span
                    >
                  </div>
                </div>

                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Estado Civil</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="estados_civiles"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.estado_civil"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    v-validate:plan_funerario_validacion_computed.immediate="
                      'required'
                    "
                    name="estado_civil"
                    data-vv-as=" "
                  >
                    <div slot="no-options">
                      Seleccione 1
                    </div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("estado_civil")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores['estado_civil.value']"
                      >{{ errores["estado_civil.value"][0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Escolaridad</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="escolaridades"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.escolaridad"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    v-validate:plan_funerario_validacion_computed.immediate="
                      'required'
                    "
                    name="escolaridad"
                    data-vv-as=" "
                  >
                    <div slot="no-options">
                      Seleccione 1
                    </div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("plan_validacion")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores['escolaridad.value']"
                      >{{ errores["escolaridad.value"][0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Afiliado a</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="afiliaciones"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.afiliacion"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    v-validate:plan_funerario_validacion_computed.immediate="
                      'required'
                    "
                    name="afiliacion"
                    data-vv-as=" "
                  >
                    <div slot="no-options">
                      Seleccione 1
                    </div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("afiliacion")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores['afiliacion.value']"
                      >{{ errores["afiliacion.value"][0] }}</span
                    >
                  </div>
                </div>

                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Indique la afiliación
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="afiliacion_nota"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Describa esa otra afiliación"
                    v-model="form.afiliacion_nota"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("afiliacion_nota")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.afiliacion_nota"
                      >{{ errores.afiliacion_nota[0] }}</span
                    >
                  </div>
                </div>
              </div>

              <!--fin de contenido del informacion del fallecido-->
            </div>
          </div>
          <div class="flex flex-wrap mt-1 px-2">
            <div class="w-full px-2 mt-2">
              <p class="texto-ojo">
                <span class="text-danger font-medium">Ojo:</span>
                Debe seleccionar la modalidad de la venta que se esté
                registrando en caso de que haya sido realizada fuera del control
                del sistema, ya que ese tipo de ventas cuenta con un control
                especial de números de órden.
              </p>
              <vs-divider />
            </div>
          </div>
        </div>
        <div class="tab-content mt-1" v-show="activeTab == 1">
          <div class="flex flex-wrap">
            <div
              class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
            >
              <div class="float-left pb-5 px-2">
                <img width="36px" src="@assets/images/doctor.svg" />
                <h3
                  class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms capitalize"
                >
                  Información del Certificado Médico de Defunción
                </h3>
              </div>

              <div class="w-full px-2">
                <vs-divider />
              </div>
              <div class="flex flex-wrap mt-1">
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Folio del Certificado Médico
                  </label>
                  <vs-input
                    name="folio_certificado"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="45"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Número de Folio"
                    v-model="form.folio_certificado"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("folio_certificado")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.folio_certificado"
                      >{{ errores.folio_certificado[0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Fecha y Hora del Fallecimiento
                    <span class="texto-importante">(*)</span>
                  </label>
                  <flat-pickr
                    name="fechahora_defuncion"
                    data-vv-as=" "
                    v-validate:fecha_solicitud_validacion_computed.immediate="
                      'required'
                    "
                    :config="configdateTimePickerWithTime"
                    v-model="form.fechahora_defuncion"
                    placeholder="Fecha y hora del fallecimiento"
                    class="w-full my-1"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("fechahora_defuncion")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.fechahora_defuncion"
                      >{{ errores.fechahora_defuncion[0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Causa de Muerte
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="causa_muerte"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="100"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Cáusa de la muerte"
                    v-model="form.causa_muerte"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("causa_muerte")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.causa_muerte"
                      >{{ errores.causa_muerte[0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Muerte Natural</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="sino"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.muerte_natural_b"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    v-validate:plan_funerario_validacion_computed.immediate="
                      'required'
                    "
                    name="muerte_natural_b"
                    data-vv-as=" "
                  >
                    <div slot="no-options">
                      Seleccione 1
                    </div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("muerte_natural_b")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores['muerte_natural_b.value']"
                      >{{ errores["muerte_natural_b.value"][0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Enfermedad Contagiosa</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="sino"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.contagioso_b"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    v-validate:plan_funerario_validacion_computed.immediate="
                      'required'
                    "
                    name="contagioso_b"
                    data-vv-as=" "
                  >
                    <div slot="no-options">
                      Seleccione 1
                    </div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("contagioso_b")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores['contagioso_b.value']"
                      >{{ errores["contagioso_b.value"][0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Lugar de Fallecimiento</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="sitios_muerte"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.sitio_muerte"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    v-validate:plan_funerario_validacion_computed.immediate="
                      'required'
                    "
                    name="sitio_muerte"
                    data-vv-as=" "
                  >
                    <div slot="no-options">
                      Seleccione 1
                    </div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("sitio_muerte")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores['sitio_muerte.value']"
                      >{{ errores["sitio_muerte.value"][0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Indique dirección
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="lugar_muerte"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="125"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Dirección donde murió"
                    v-model="form.lugar_muerte"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("lugar_muerte")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.lugar_muerte"
                      >{{ errores.lugar_muerte[0] }}</span
                    >
                  </div>
                </div>

                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>¿Atención Médica Antes de morir?</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="sino"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.atencion_medica_b"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    v-validate:plan_funerario_validacion_computed.immediate="
                      'required'
                    "
                    name="atencion_medica_b"
                    data-vv-as=" "
                  >
                    <div slot="no-options">
                      Seleccione 1
                    </div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("atencion_medica_b")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores['atencion_medica_b.value']"
                      >{{ errores["atencion_medica_b.value"][0] }}</span
                    >
                  </div>
                </div>

                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    ¿Padecía Alguna Enfermedad?
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="enfermedades_padecidas"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="125"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Enfermedades que padecía"
                    v-model="form.enfermedades_padecidas"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("enfermedades_padecidas")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.enfermedades_padecidas"
                      >{{ errores.enfermedades_padecidas[0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Nombre del Informante
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="certificado_informante"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="125"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Nombre del informante para el certificado"
                    v-model="form.certificado_informante"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("certificado_informante")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.certificado_informante"
                      >{{ errores.certificado_informante[0] }}</span
                    >
                  </div>
                </div>

                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Teléfono del Informante
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="certificado_informante_telefono"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="45"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Teléfono del informante"
                    v-model="form.certificado_informante_telefono"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("certificado_informante_telefono")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.certificado_informante_telefono"
                      >{{ errores.certificado_informante_telefono[0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Parentesco con el fallecido
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="certificado_informante_parentesco"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="45"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ingrese un teléfono"
                    v-model="form.certificado_informante_parentesco"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("certificado_informante_parentesco")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.certificado_informante_parentesco"
                      >{{ errores.certificado_informante_parentesco[0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    Nombre del Médico Legista
                    <span class="texto-importante">(*)</span>
                  </label>
                  <vs-input
                    name="medico_legista"
                    data-vv-as=" "
                    v-validate.disabled="'required'"
                    maxlength="125"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Nombre del médico legista"
                    v-model="form.medico_legista"
                  />
                  <div>
                    <span class="text-danger">{{
                      errors.first("medico_legista")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores.medico_legista"
                      >{{ errores.medico_legista[0] }}</span
                    >
                  </div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Estado del Cuerpo</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="estados_cuerpo"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="form.estado_cuerpo"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    v-validate:plan_funerario_validacion_computed.immediate="
                      'required'
                    "
                    name="estado_cuerpo"
                    data-vv-as=" "
                  >
                    <div slot="no-options">
                      Seleccione 1
                    </div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("estado_cuerpo")
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger"
                      v-if="this.errores['estado_cuerpo.value']"
                      >{{ errores["estado_cuerpo.value"][0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="flex flex-wrap mt-1 px-2">
            <div class="w-full px-2 mt-2">
              <p class="texto-ojo">
                <span class="text-danger font-medium">Ojo:</span>
                Debe seleccionar la modalidad de la venta que se esté
                registrando en caso de que haya sido realizada fuera del control
                del sistema, ya que ese tipo de ventas cuenta con un control
                especial de números de órden.
              </p>
              <vs-divider />
            </div>
          </div>
        </div>
        <div class="tab-content mt-1" v-show="activeTab == 2">
          <div class="flex flex-wrap mt-1 px-2">
            <div class="w-full">
              <div class="flex flex-wrap mt-1 px-2">
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <!--contenido del informacion del fallecido-->
                  <div>
                    <div class="float-left pb-5 px-2">
                      <img width="36px" src="@assets/images/corpse.svg" />
                      <h3
                        class="float-right ml-3 text-xl px-2 py-1 bg-seccion-forms"
                      >
                        Datos del Embalsamiento
                      </h3>
                    </div>
                  </div>
                  <div class="w-full px-2">
                    <vs-divider />
                  </div>
                  <div class="flex flex-wrap">
                    <div
                      class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2 text-center"
                    >
                      <label class="text-sm font-bold"
                        >¿Embalsamar Cuerpo?</label
                      >
                      <div class="mt-3">
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="1"
                          class="mr-4"
                          >SI</vs-radio
                        >
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="0"
                          class="mr-4"
                          >NO</vs-radio
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Nombre del Médico Responsable
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        <span>Seleccione al Preparador</span>
                        <span class="texto-importante">(*)</span>
                      </label>
                      <v-select
                        :options="planes_funerarios"
                        :clearable="false"
                        :dir="$vs.rtl ? 'rtl' : 'ltr'"
                        v-model="form.plan_funerario"
                        class="mb-4 sm:mb-0 pb-1 pt-1"
                        v-validate:plan_funerario_validacion_computed.immediate="
                          'required'
                        "
                        name="plan_validacion"
                        data-vv-as=" "
                      >
                        <div slot="no-options">
                          Seleccione 1
                        </div>
                      </v-select>
                      <div>
                        <span class="text-danger">{{
                          errors.first("plan_validacion")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores['plan_funerario.value']"
                          >{{ errores["plan_funerario.value"][0] }}</span
                        >
                      </div>
                    </div>
                  </div>
                  <!--fin de contenido del informacion del fallecido-->
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <div class="float-left pb-5 px-2">
                    <img width="36px" src="@assets/images/doctor.svg" />
                    <h3
                      class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms capitalize"
                    >
                      Datos de la Velación
                    </h3>
                  </div>

                  <div class="w-full px-2">
                    <vs-divider />
                  </div>
                  <div class="flex flex-wrap">
                    <div
                      class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center"
                    >
                      <label class="text-sm font-bold">¿Velar Cuerpo?</label>
                      <div class="mt-3">
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="1"
                          class="mr-4"
                          >SI</vs-radio
                        >
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="0"
                          class="mr-4"
                          >NO</vs-radio
                        >
                      </div>
                    </div>

                    <div
                      class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        <span>¿Lugar de Velación?</span>
                        <span class="texto-importante">(*)</span>
                      </label>
                      <v-select
                        :options="planes_funerarios"
                        :clearable="false"
                        :dir="$vs.rtl ? 'rtl' : 'ltr'"
                        v-model="form.plan_funerario"
                        class="mb-4 sm:mb-0 pb-1 pt-1"
                        v-validate:plan_funerario_validacion_computed.immediate="
                          'required'
                        "
                        name="plan_validacion"
                        data-vv-as=" "
                      >
                        <div slot="no-options">
                          Seleccione 1
                        </div>
                      </v-select>
                      <div>
                        <span class="text-danger">{{
                          errors.first("plan_validacion")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores['plan_funerario.value']"
                          >{{ errores["plan_funerario.value"][0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Dirección del Servicio
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="w-full mt-5">
              <div class="flex flex-wrap mt-1 px-2">
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <!--contenido del informacion del fallecido-->
                  <div>
                    <div class="float-left pb-5 px-2">
                      <img width="36px" src="@assets/images/corpse.svg" />
                      <h3
                        class="float-right ml-3 text-xl px-2 py-1 bg-seccion-forms"
                      >
                        Datos de la Cremación
                      </h3>
                    </div>
                  </div>
                  <div class="w-full px-2">
                    <vs-divider />
                  </div>

                  <div class="flex flex-wrap">
                    <div
                      class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center"
                    >
                      <label class="text-sm font-bold">¿Cremar Cuerpo?</label>
                      <div class="mt-3">
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="1"
                          class="mr-4"
                          >SI</vs-radio
                        >
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="0"
                          class="mr-4"
                          >NO</vs-radio
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Fecha de la Cremación
                        <span class="texto-importante">(*)</span>
                      </label>
                      <flat-pickr
                        name="fecha_solicitud"
                        data-vv-as=" "
                        v-validate:fecha_solicitud_validacion_computed.immediate="
                          'required'
                        "
                        :config="configdateTimePickerWithTime"
                        v-model="form.fecha_solicitud"
                        placeholder="Fecha y Hora de Solicitud"
                        class="w-full my-1"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Fecha de Entrga de Cenizas
                        <span class="texto-importante">(*)</span>
                      </label>
                      <flat-pickr
                        name="fecha_solicitud"
                        data-vv-as=" "
                        v-validate:fecha_solicitud_validacion_computed.immediate="
                          'required'
                        "
                        :config="configdateTimePickerWithTime"
                        v-model="form.fecha_solicitud"
                        placeholder="Fecha y Hora de Solicitud"
                        class="w-full my-1"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Descripción de Urna
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                  </div>
                  <!--fin de contenido del informacion del fallecido-->
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <div class="float-left pb-5 px-2">
                    <img width="36px" src="@assets/images/doctor.svg" />
                    <h3
                      class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms capitalize"
                    >
                      Datos de la Inhumación
                    </h3>
                  </div>

                  <div class="w-full px-2">
                    <vs-divider />
                  </div>
                  <div class="flex flex-wrap">
                    <div
                      class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center"
                    >
                      <label class="text-sm font-bold">¿Inhumar Cuerpo?</label>
                      <div class="mt-3">
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="1"
                          class="mr-4"
                          >SI</vs-radio
                        >
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="0"
                          class="mr-4"
                          >NO</vs-radio
                        >
                      </div>
                    </div>

                    <div
                      class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        <span>¿Lugar de Inhumación?</span>
                        <span class="texto-importante">(*)</span>
                      </label>
                      <v-select
                        :options="planes_funerarios"
                        :clearable="false"
                        :dir="$vs.rtl ? 'rtl' : 'ltr'"
                        v-model="form.plan_funerario"
                        class="mb-4 sm:mb-0 pb-1 pt-1"
                        v-validate:plan_funerario_validacion_computed.immediate="
                          'required'
                        "
                        name="plan_validacion"
                        data-vv-as=" "
                      >
                        <div slot="no-options">
                          Seleccione 1
                        </div>
                      </v-select>
                      <div>
                        <span class="text-danger">{{
                          errors.first("plan_validacion")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores['plan_funerario.value']"
                          >{{ errores["plan_funerario.value"][0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Fecha Inhumación
                        <span class="texto-importante">(*)</span>
                      </label>
                      <flat-pickr
                        name="fecha_solicitud"
                        data-vv-as=" "
                        v-validate:fecha_solicitud_validacion_computed.immediate="
                          'required'
                        "
                        :config="configdateTimePickerWithTime"
                        v-model="form.fecha_solicitud"
                        placeholder="Fecha y Hora de Solicitud"
                        class="w-full my-1"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
                    >
                      <div class="py-1">
                        <label class="text-sm opacity-75 font-bold">
                          Seleccione un Cliente
                          <span class="texto-importante">(*)</span>
                        </label>
                      </div>
                      <div class="flex flex-wrap">
                        <div
                          class="w-full sm:w-12/12 md:w-1/12 lg:w-1/12 xl:w-1/12 px-2"
                        >
                          <div v-if="fueCancelada != true">
                            <img
                              v-if="form.id_cliente == ''"
                              width="46px"
                              class="cursor-pointer p-2"
                              src="@assets/images/search.svg"
                              @click="openBuscador = true"
                              title="Buscar Cliente"
                            />
                            <img
                              v-else
                              width="46px"
                              class="cursor-pointer p-2"
                              src="@assets/images/minus.svg"
                              @click="quitarCliente()"
                            />
                          </div>
                          <div v-else>
                            <img
                              width="46px"
                              class="cursor-pointer p-2"
                              src="@assets/images/minus.svg"
                            />
                          </div>
                        </div>
                        <div
                          class="w-full sm:w-12/12 md:w-11/12 lg:w-11/12 xl:w-11/12"
                        >
                          <vs-input
                            readonly
                            v-validate.disabled="'required'"
                            name="id_cliente"
                            data-vv-as=" "
                            type="text"
                            class="w-full py-1 cursor-pointer texto-bold"
                            placeholder="DEBE SELECCIONAR UN CLIENTE PARA REALIZAR EL SERVICIO."
                            v-model="form.cliente"
                            maxlength="100"
                            ref="cliente_ref"
                          />
                          <div>
                            <span class="text-danger">{{
                              errors.first("id_cliente")
                            }}</span>
                          </div>
                          <div class="mt-2">
                            <span
                              class="text-danger"
                              v-if="this.errores.id_cliente"
                              >{{ errores.id_cliente[0] }}</span
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                    <div
                      class="hidden w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Indique ubicación
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="w-full mt-5">
              <div class="flex flex-wrap mt-1 px-2">
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <!--contenido del informacion del fallecido-->
                  <div>
                    <div class="float-left pb-5 px-2">
                      <img width="36px" src="@assets/images/corpse.svg" />
                      <h3
                        class="float-right ml-3 text-xl px-2 py-1 bg-seccion-forms"
                      >
                        Datos del Traslado
                      </h3>
                    </div>
                  </div>
                  <div class="w-full px-2">
                    <vs-divider />
                  </div>

                  <div class="flex flex-wrap">
                    <div
                      class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center"
                    >
                      <label class="text-sm font-bold">¿Velar Cuerpo?</label>
                      <div class="mt-3">
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="1"
                          class="mr-4"
                          >SI</vs-radio
                        >
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="0"
                          class="mr-4"
                          >NO</vs-radio
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Fecha del Traslado
                        <span class="texto-importante">(*)</span>
                      </label>
                      <flat-pickr
                        name="fecha_solicitud"
                        data-vv-as=" "
                        v-validate:fecha_solicitud_validacion_computed.immediate="
                          'required'
                        "
                        :config="configdateTimePickerWithTime"
                        v-model="form.fecha_solicitud"
                        placeholder="Fecha y Hora de Solicitud"
                        class="w-full my-1"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Lugar del Traslado
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                  </div>
                  <!--fin de contenido del informacion del fallecido-->
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <div class="float-left pb-5 px-2">
                    <img width="36px" src="@assets/images/doctor.svg" />
                    <h3
                      class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms capitalize"
                    >
                      Datos de la Aseguradora
                    </h3>
                  </div>

                  <div class="w-full px-2">
                    <vs-divider />
                  </div>
                  <div class="flex flex-wrap">
                    <div
                      class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center"
                    >
                      <label class="text-sm font-bold">¿Velar Cuerpo?</label>
                      <div class="mt-3">
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="1"
                          class="mr-4"
                          >SI</vs-radio
                        >
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="0"
                          class="mr-4"
                          >NO</vs-radio
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Num. Convenio
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Aseguradora
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Teléfonos
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="w-full mt-5">
              <div class="flex flex-wrap mt-1 px-2">
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <!--contenido del informacion del fallecido-->
                  <div>
                    <div class="float-left pb-5 px-2">
                      <img width="36px" src="@assets/images/corpse.svg" />
                      <h3
                        class="float-right ml-3 text-xl px-2 py-1 bg-seccion-forms"
                      >
                        Datos de la Misa
                      </h3>
                    </div>
                  </div>
                  <div class="w-full px-2">
                    <vs-divider />
                  </div>

                  <div class="flex flex-wrap">
                    <div
                      class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center"
                    >
                      <label class="text-sm font-bold">Ceremonia o Misa?</label>
                      <div class="mt-3">
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="1"
                          class="mr-4"
                          >SI</vs-radio
                        >
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="0"
                          class="mr-4"
                          >NO</vs-radio
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Fecha y Hora
                        <span class="texto-importante">(*)</span>
                      </label>
                      <flat-pickr
                        name="fecha_solicitud"
                        data-vv-as=" "
                        v-validate:fecha_solicitud_validacion_computed.immediate="
                          'required'
                        "
                        :config="configdateTimePickerWithTime"
                        v-model="form.fecha_solicitud"
                        placeholder="Fecha y Hora de Solicitud"
                        class="w-full my-1"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Iglesia o Templo
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Dirección
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                  </div>
                  <!--fin de contenido del informacion del fallecido-->
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                >
                  <div class="float-left pb-5 px-2">
                    <img width="36px" src="@assets/images/doctor.svg" />
                    <h3
                      class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms capitalize"
                    >
                      Datos de la Cadena de Custodia
                    </h3>
                  </div>

                  <div class="w-full px-2">
                    <vs-divider />
                  </div>
                  <div class="flex flex-wrap">
                    <div
                      class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center"
                    >
                      <label class="text-sm font-bold"
                        >¿Requirió Custodia?</label
                      >
                      <div class="mt-3">
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="1"
                          class="mr-4"
                          >SI</vs-radio
                        >
                        <vs-radio
                          vs-name="llamada_b"
                          v-model="form.llamada_b"
                          :vs-value="0"
                          class="mr-4"
                          >NO</vs-radio
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Nombre del Responsable
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Folio de Referencia
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                    <div
                      class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                    >
                      <label class="text-sm opacity-75 font-bold">
                        Folio de Liberación
                        <span class="texto-importante">(*)</span>
                      </label>
                      <vs-input
                        name="titular_sustituto"
                        data-vv-as=" "
                        v-validate.disabled="'required'"
                        maxlength="150"
                        type="text"
                        class="w-full pb-1 pt-1"
                        placeholder="Nombre del titular sustituto"
                        v-model="form.titular_sustituto"
                      />
                      <div>
                        <span class="text-danger">{{
                          errors.first("titular_sustituto")
                        }}</span>
                      </div>
                      <div class="mt-2">
                        <span
                          class="text-danger"
                          v-if="this.errores.titular_sustituto"
                          >{{ errores.titular_sustituto[0] }}</span
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="flex flex-wrap mt-1 px-2">
            <div class="w-full px-2 mt-2">
              <p class="texto-ojo">
                <span class="text-danger font-medium">Ojo:</span>
                Debe seleccionar la modalidad de la venta que se esté
                registrando en caso de que haya sido realizada fuera del control
                del sistema, ya que ese tipo de ventas cuenta con un control
                especial de números de órden.
              </p>
              <vs-divider />
            </div>
          </div>
        </div>
        <div class="tab-content mt-1" v-show="activeTab == 3">
          <div class="flex flex-wrap">
            <div
              class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
            >
              <div class="float-left pb-5 px-2">
                <img width="36px" src="@assets/images/corpse.svg" />
                <h3
                  class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms capitalize"
                >
                  Indique los Detalles del Contrato
                </h3>
              </div>
              <div class="w-full px-2">
                <vs-divider />
              </div>
              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
                <div class="flex flex-wrap">
                  <div
                    class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2"
                  >
                    <label class="text-sm opacity-75 font-bold">
                      <span>Destino del Cuerpo</span>
                      <span class="texto-importante">(*)</span>
                    </label>
                    <v-select
                      :options="planes_funerarios"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.plan_funerario"
                      class="mb-4 sm:mb-0 pb-1 pt-1"
                      v-validate:plan_funerario_validacion_computed.immediate="
                        'required'
                      "
                      name="plan_validacion"
                      data-vv-as=" "
                    >
                      <div slot="no-options">
                        Seleccione 1
                      </div>
                    </v-select>
                    <div>
                      <span class="text-danger">{{
                        errors.first("plan_validacion")
                      }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger"
                        v-if="this.errores['plan_funerario.value']"
                        >{{ errores["plan_funerario.value"][0] }}</span
                      >
                    </div>
                  </div>
                  <div
                    class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2"
                  >
                    <label class="text-sm opacity-75 font-bold">
                      Fecha del Contrato
                      <span class="texto-importante">(*)</span>
                    </label>

                    <flat-pickr
                      name="fecha_solicitud"
                      data-vv-as=" "
                      v-validate:fecha_solicitud_validacion_computed.immediate="
                        'required'
                      "
                      :config="configdateTimePickerWithTime"
                      v-model="form.fecha_solicitud"
                      placeholder="Fecha y Hora de Solicitud"
                      class="w-full my-1"
                    />
                    <div>
                      <span class="text-danger">{{
                        errors.first("telefono_titular_sustituto")
                      }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger"
                        v-if="this.errores.telefono_titular_sustituto"
                        >{{ errores.telefono_titular_sustituto[0] }}</span
                      >
                    </div>
                  </div>

                  <div
                    class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2"
                  >
                    <label class="text-sm opacity-75 font-bold">
                      Seleccione al Contratante
                      <span class="texto-importante">(*)</span>
                    </label>

                    <div class="flex flex-wrap">
                      <div
                        class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2"
                      >
                        <div v-if="fueCancelada != true">
                          <img
                            v-if="form.id_cliente == ''"
                            width="46px"
                            class="cursor-pointer p-2"
                            src="@assets/images/search.svg"
                            @click="openBuscador = true"
                            title="Buscar Cliente"
                          />
                          <img
                            v-else
                            width="46px"
                            class="cursor-pointer p-2"
                            src="@assets/images/minus.svg"
                            @click="quitarCliente()"
                          />
                        </div>
                        <div v-else>
                          <img
                            width="46px"
                            class="cursor-pointer p-2"
                            src="@assets/images/minus.svg"
                          />
                        </div>
                      </div>
                      <div
                        class="w-full sm:w-12/12 md:w-10/12 lg:w-10/12 xl:w-10/12 px-2"
                      >
                        <vs-input
                          readonly
                          v-validate.disabled="'required'"
                          name="id_cliente"
                          data-vv-as=" "
                          type="text"
                          class="w-full py-1 cursor-pointer texto-bold"
                          placeholder="DEBE SELECCIONAR UN CLIENTE PARA REALIZAR EL SERVICIO."
                          v-model="form.cliente"
                          maxlength="100"
                          ref="cliente_ref"
                        />
                        <div>
                          <span class="text-danger">{{
                            errors.first("id_cliente")
                          }}</span>
                        </div>
                        <div class="mt-2">
                          <span
                            class="text-danger"
                            v-if="this.errores.id_cliente"
                            >{{ errores.id_cliente[0] }}</span
                          >
                        </div>
                      </div>
                    </div>
                  </div>

                  <div
                    class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2"
                  >
                    <label class="text-sm opacity-75 font-bold">
                      Parentesco con el Fallecido
                      <span class="texto-importante">(*)</span>
                    </label>

                    <vs-input
                      name="telefono_titular_sustituto"
                      data-vv-as=" "
                      v-validate.disabled="'required'"
                      maxlength="45"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Ingrese un teléfono"
                      v-model="form.telefono_titular_sustituto"
                    />
                    <div>
                      <span class="text-danger">{{
                        errors.first("telefono_titular_sustituto")
                      }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger"
                        v-if="this.errores.telefono_titular_sustituto"
                        >{{ errores.telefono_titular_sustituto[0] }}</span
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div
              class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 mt-5"
            >
              <div class="float-left pb-5 px-2">
                <img width="36px" src="@assets/images/corpse.svg" />
                <h3
                  class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms capitalize"
                >
                  Datos del Acta de Defunción
                </h3>
              </div>
              <div class="w-full px-2">
                <vs-divider />
              </div>
              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
                <div class="flex flex-wrap">
                  <div
                    class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                  >
                    <label class="text-sm opacity-75 font-bold">
                      Folio del Acta
                      <span class="texto-importante">(*)</span>
                    </label>

                    <vs-input
                      name="telefono_titular_sustituto"
                      data-vv-as=" "
                      v-validate.disabled="'required'"
                      maxlength="45"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Ingrese un teléfono"
                      v-model="form.telefono_titular_sustituto"
                    />
                    <div>
                      <span class="text-danger">{{
                        errors.first("telefono_titular_sustituto")
                      }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger"
                        v-if="this.errores.telefono_titular_sustituto"
                        >{{ errores.telefono_titular_sustituto[0] }}</span
                      >
                    </div>
                  </div>
                  <div
                    class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                  >
                    <label class="text-sm opacity-75 font-bold">
                      Fecha de Levantamiento
                      <span class="texto-importante">(*)</span>
                    </label>

                    <flat-pickr
                      name="fecha_solicitud"
                      data-vv-as=" "
                      v-validate:fecha_solicitud_validacion_computed.immediate="
                        'required'
                      "
                      :config="configdateTimePickerWithTime"
                      v-model="form.fecha_solicitud"
                      placeholder="Fecha y Hora de Solicitud"
                      class="w-full my-1"
                    />
                    <div>
                      <span class="text-danger">{{
                        errors.first("telefono_titular_sustituto")
                      }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger"
                        v-if="this.errores.telefono_titular_sustituto"
                        >{{ errores.telefono_titular_sustituto[0] }}</span
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="flex flex-wrap mt-1 px-2">
            <div class="w-full px-2 mt-2">
              <p class="texto-ojo">
                <span class="text-danger font-medium">Ojo:</span>
                Debe seleccionar la modalidad de la venta que se esté
                registrando en caso de que haya sido realizada fuera del control
                del sistema, ya que ese tipo de ventas cuenta con un control
                especial de números de órden.
              </p>
              <vs-divider />
            </div>
          </div>
        </div>
        <div class="tab-content mt-1" v-show="activeTab == 4"></div>
      </div>
      <!--fin venta-->
    </vs-popup>
    <Password
      :show="openPassword"
      :callback-on-success="callback"
      @closeVerificar="closePassword"
      :accion="accionNombre"
    ></Password>
    <ConfirmarDanger
      :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarSinPassword = false"
      :accion="accionConfirmarSinPassword"
      :confirmarButton="botonConfirmarSinPassword"
    ></ConfirmarDanger>

    <ConfirmarAceptar
      :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar"
      @closeVerificar="openConfirmarAceptar = false"
      :accion="'He revisado la información y quiero guardar la venta'"
      :confirmarButton="'Guardar Venta'"
    ></ConfirmarAceptar>

    <ClientesBuscador
      :show="openBuscador"
      @closeBuscador="openBuscador = false"
      @retornoCliente="clienteSeleccionado"
    ></ClientesBuscador>
  </div>
</template>
<script>
/**date picker */
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import planes from "@services/planes";
import usuarios from "@services/Usuarios";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import ClientesBuscador from "@pages/clientes/searcher.vue";

/**VARIABLES GLOBALES */
import {
  alfabeto,
  configdateTimePicker,
  configdateTimePickerWithTime,
} from "@/VariablesGlobales";

export default {
  components: {
    "v-select": vSelect,
    flatPickr,
    Password,
    ConfirmarDanger,
    ConfirmarAceptar,
    ClientesBuscador,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    //para saber que tipo de formulario es
    tipo: {
      type: String,
      required: true,
    },
    id_venta: {
      type: Number,
      required: false,
      default: 0,
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      this.limpiarValidation();
      if (newValue == true) {
        this.$nextTick(() =>
          this.$refs["cliente_ref"].$el.querySelector("input").focus()
        );
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        (async () => {
          if (this.getTipoformulario == "agregar") {
            /**acciones cuando el formulario es de agregar */
          } else {
            /**es modificar */
            /**pasando el valor de la venta id */
            //this.form.id_venta = this.get_venta_id;
            /**se cargan los datos al formulario */
            //await this.consultar_venta_id();
          }
        })();
      } else {
        /**acciones al cerrar el formulario */
      }
    },
  },
  computed: {
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
    getTipoformulario: {
      get() {
        return this.tipo;
      },
      set(newValue) {
        return newValue;
      },
    },
    get_venta_id: {
      get() {
        return this.id_venta;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      activeTab: 0,
      generos: [
        {
          value: "",
          label: "Seleccione 1",
        },
        {
          value: 1,
          label: "Hombre",
        },
        {
          value: 2,
          label: "Mujer",
        },
      ],
      nacionalidades: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      estados_civiles: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      afiliaciones: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      escolaridades: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      sino: [
        {
          value: "1",
          label: "SI",
        },
        {
          value: "0",
          label: "NO",
        },
      ],
      sitios_muerte: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      estados_cuerpo: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      form: {
        /**fallecido */
        nombre_afectado: "",
        fecha_nacimiento: "",
        edad: "",
        genero: {
          value: "",
          label: "Seleccione 1",
        },
        nacionalidad: {
          value: "",
          label: "Seleccione 1",
        },
        lugar_nacimiento: "",
        ocupacion: "",
        direccion_fallecido: "",
        estado_civil: {
          value: "",
          label: "Seleccione 1",
        },
        afiliacion: {
          value: "",
          label: "Seleccione 1",
        },
        escolaridad: {
          value: "",
          label: "Seleccione 1",
        },
        afiliacion_nota: "",
        /**fin datos fallecido */

        /**certificad de defuncion */
        folio_certificado: "",
        fechahora_defuncion: "",
        causa_muerte: "",
        muerte_natural_b: {
          value: "1",
          label: "SI",
        },
        sitio_muerte: {
          value: "",
          label: "Seleccione 1",
        },
        lugar_muerte: "",
        atencion_medica_b: {
          value: "1",
          label: "SI",
        },
        contagioso_b: {
          value: "1",
          label: "SI",
        },
        enfermedades_padecidas: "",
        certificado_informante: "",
        certificado_informante_telefono: "",
        certificado_informante_parentesco: "",
        medico_legista: "",
        estado_cuerpo: {
          value: "",
          label: "Seleccione 1",
        },
        /**fin de datos dle certificado */
      },
      /**variables dle modulo */
      openNotas: false,
      configdateTimePicker: configdateTimePicker,
      configdateTimePickerWithTime: configdateTimePickerWithTime,
      verDisponibilidad: false,
      openBuscador: false,
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      alfabeto: alfabeto,
      openPassword: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Actualizar Contrato",
      errores: [],
    };
  },
  methods: {
    acceptAlert() {
      this.$validator
        .validateAll()
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Error",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "12000",
            });
            return;
          } else {
            /**aqui se hace la validacion en los totales de la venta */
            //se confirma la cntraseña
            /**actualizando los valores de total de venta */
            //fin de actualizar datos de ubicacion
            (async () => {
              if (this.getTipoformulario == "agregar") {
                this.callBackConfirmarAceptar = await this.guardar_venta;
                this.openConfirmarAceptar = true;
              } else {
                /**es modificacion */
                this.callback = await this.modificar_venta;
                this.openPassword = true;
              }
            })();
          }
        })
        .catch(() => {});
    },

    async guardar_venta() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await planes.guardar_venta(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Ventas de Propiedades",
            text: "Se ha guardado la venta correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
          });
          this.$emit("ver_pdfs_nueva_venta", res.data);
          this.cerrarVentana();
        } else {
          this.$vs.notify({
            title: "Ventas de Propiedades",
            text: "Error al guardar la venta, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 4000,
          });
        }

        this.$vs.loading.close();
      } catch (err) {
        if (err.response) {
          if (err.response.status == 403) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 4000,
            });
          } else if (err.response.status == 422) {
            //checo si existe cada error
            this.errores = err.response.data.error;
            this.$vs.notify({
              title: "Guardar Venta",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 5000,
            });
          } else if (err.response.status == 409) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Guardar Venta",
              text: err.response.data.error,
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 15000,
            });
          }
        }
        this.$vs.loading.close();
      }
    },

    async modificar_venta() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await planes.modificar_venta(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Ventas de Propiedades",
            text: "Se ha modificado la venta correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
          });
          this.$emit("ver_pdfs_nueva_venta", res.data);
          this.cerrarVentana();
        } else {
          this.$vs.notify({
            title: "Ventas de Propiedades",
            text: "Error al modificar la venta, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 4000,
          });
        }

        this.$vs.loading.close();
      } catch (err) {
        if (err.response) {
          if (err.response.status == 403) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 4000,
            });
          } else if (err.response.status == 422) {
            //checo si existe cada error
            this.errores = err.response.data.error;

            this.$vs.notify({
              title: "Modificar Venta",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 5000,
            });
          } else if (err.response.status == 409) {
            //este error es por alguna condicion que el contrano no cumple para modificar
            //la propiedad esa ya ha sido vendida
            this.$vs.notify({
              title: "Modificar información de la venta",
              text: err.response.data.error,
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 30000,
            });
          }
        }
        this.$vs.loading.close();
      }
    },
    cancel() {
      this.$emit("closeVentana");
    },
    async get_planes_funerarios() {
      try {
        this.$vs.loading();
        let res = await planes.get_planes(false, "");
        //le agrego todos los usuarios vendedores
        this.planes_funerarios = [];
        this.planes_funerarios.push({
          label: "Seleccione 1",
          plan: "",
          plan_ingles: "",
          nota: "",
          nota_ingles: "",
          value: "",
          secciones: [],
          precios: [],
        });
        res.data.forEach((element) => {
          this.planes_funerarios.push({
            label: element.plan,
            plan: element.plan,
            plan_ingles: element.plan_ingles,
            nota: element.nota,
            nota_ingles: element.nota_ingles,
            value: element.id,
            secciones: element.secciones,
            precios: element.precios,
          });
        });
        if (this.getTipoformulario == "agregar") {
          if (this.planes_funerarios.length > 1) {
            /**selecciona el primer plan que venga en el arreglo */
            this.form.plan_funerario = this.planes_funerarios[1];
          } else {
            /**selecciona el "Seleccione 1" */
            this.form.plan_funerario = this.planes_funerarios[0];
          }
        }
        this.$vs.loading.close();
      } catch (error) {
        /**error al cargar vendedores */
        this.$vs.notify({
          title: "Error",
          text:
            "Ha ocurrido un error al tratar de cargar el catálogo de vendedores, por favor reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "9000",
        });
        this.$vs.loading.close();
        this.cerrarVentana();
      }
    },
    //get vendedores
    async get_vendedores() {
      try {
        let res = await planes.get_vendedores();
        //le agrego todos los usuarios vendedores
        this.vendedores = [];
        this.vendedores.push({ label: "Seleccione 1", value: "" });
        if (this.getTipoformulario == "agregar") {
          this.form.vendedor = this.vendedores[0];
        }
        res.data.forEach((element) => {
          this.vendedores.push({
            label: element.nombre,
            value: element.id,
          });
        });
      } catch (error) {
        /**error al cargar vendedores */
        this.$vs.notify({
          title: "Error",
          text:
            "Ha ocurrido un error al tratar de cargar el catálogo de vendedores, por favor reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "9000",
        });
        this.cerrarVentana();
      }
    },

    cancelar() {
      this.botonConfirmarSinPassword = "Salir y limpiar";
      this.accionConfirmarSinPassword =
        "Esta acción limpiará los datos que capturó en el formulario.";
      this.openConfirmarSinPassword = true;
      this.callBackConfirmar = this.cerrarVentana;
    },

    cerrarVentana() {
      this.openConfirmarSinPassword = false;
      this.limpiarVentana();
      this.$emit("closeVentana");
    },
    //regresa los datos a su estado inicial
    limpiarVentana() {
      console.log("limpiar");
    },

    closePassword() {
      this.openPassword = false;
    },

    clienteSeleccionado(datos) {
      /**obtiene los datos retornados del buscar cliente */
      this.form.cliente = datos.nombre;
      this.form.id_cliente = datos.id_cliente;
      //alert(datos.id_cliente);
    },
    limpiarCliente() {
      this.form.id_cliente = "";
      this.form.cliente = "seleccione 1 cliente";
    },
    quitarCliente() {
      this.botonConfirmarSinPassword = "Cambiar cliente";
      this.accionConfirmarSinPassword =
        "¿Desea cambiar de cliente para este contrato?";
      this.callBackConfirmar = this.limpiarCliente;
      this.openConfirmarSinPassword = true;
    },
    async consultar_venta_id() {
      try {
        this.$vs.loading();
        //let res = await planes.consultar_venta_id(this.form.id_venta);
        //this.datosVenta = res.data[0];

        /**cargando la antiguedad de la venta */
        /* this.ventasAntiguedad.forEach((element) => {
          if (element.value == this.datosVenta.antiguedad_operacion_id) {
            this.form.ventaAntiguedad = element;
            return;
          }
        });
        */

        this.$vs.loading.close();
      } catch (err) {
        this.$vs.loading.close();
        if (err.response) {
          if (err.response.status == 403) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 4000,
            });
          }
        }
      }
    },
    limpiarValidation() {
      this.$validator.pause();
      this.$nextTick(() => {
        this.$validator.errors.clear();
        this.$validator.fields.items.forEach((field) => field.reset());
        this.$validator.fields.items.forEach((field) =>
          this.errors.remove(field)
        );
        this.$validator.resume();
      });
    },
  },
  created() {},
};
</script>
