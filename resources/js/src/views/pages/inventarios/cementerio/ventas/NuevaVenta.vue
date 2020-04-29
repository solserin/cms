<template >
  <div class="centerx">
    <vs-popup
      fullscreen
      close="cancelar"
      title="Venta de Propiedades del Cementerio"
      :active.sync="showVentana"
      @close="cancelar"
    >
      <!--inicio venta-->
      <vx-card class="pt-5">
        <template slot="no-body">
          <div class="venta-details">
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 mt-5">
                <!--mapa del cementerio-->
                <div mt-5>
                  <Mapa
                    :disabled="false"
                    :idAreaInicial="idAreaInicial"
                    @getDatosTipoPropiedad="getDatosTipoPropiedad"
                  ></Mapa>
                </div>

                <!--fin del mapa del cementerio-->
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="ShoppingCartIcon" class="mr-2 mb-5" svgClasses="w-5 h-5" />
                  <span
                    class="font-bold text-primary"
                  >FORMULARIO DE REGISTRO DE VENTAS DEL CEMENTERIO</span>
                </h3>
                <div class="flex flex-wrap">
                  <div class="w-full">
                    <h3 class="text-xl">
                      <feather-icon icon="MapPinIcon" class="mr-2" svgClasses="w-5 h-5" />Seleccione 1 Área del Cementerio e Ingrese los Datos de la Venta
                    </h3>
                    <p class="my-2">
                      <span v-if="this.datosAreas.id">
                        <span
                          class="pr-3 pl-3 text-white bg-success propiedad_tipo"
                          v-if="this.datosAreas.tipo_propiedades_id == 1 || this.datosAreas.tipo_propiedades_id == 2 || this.datosAreas.tipo_propiedades_id == 5 || this.datosAreas.tipo_propiedades_id == 6"
                        >Propiedades {{this.datosAreas['tipo_propiedad'].tipo+' '+this.datosAreas.propiedad_indicador}}</span>
                        <span
                          class="pr-3 pl-3 text-white bg-success propiedad_tipo"
                          v-if="this.datosAreas.tipo_propiedades_id == 3 "
                        >Propiedades {{this.datosAreas['tipo_propiedad'].tipo+' Columna '+this.datosAreas.propiedad_indicador}}</span>
                        <span
                          class="pr-3 pl-3 text-white bg-success propiedad_tipo"
                          v-if="this.datosAreas.tipo_propiedades_id == 4"
                        >Propiedades {{this.datosAreas['tipo_propiedad'].tipo+' Terraza '+this.datosAreas.propiedad_indicador}}</span>
                      </span>
                      <span
                        class="pr-3 pl-3 text-white bg-danger propiedad_tipo"
                        v-else
                      >No se ha seleccionado ningún área</span>
                    </p>
                    <p class="flex items-center flex-wrap" v-if="this.datosAreas.id">
                      <span
                        class="leading-none font-medium text-primary mr-4 mt-2"
                      >Precio Actual: $ {{this.datosAreas['tipo_propiedad'].precios[0].precio_neto | numFormat('0,000.00')}} Pesos.</span>
                      <span
                        class="pl-4 mr-2 mt-2 border border-solid d-theme-border-grey-light border-t-0 border-b-0 border-r-0"
                      >Capacidad {{this.datosAreas['tipo_propiedad'].capacidad}} Persona (s)</span>
                    </p>
                    <vs-divider />
                  </div>
                </div>
                <div class="flex flex-wrap mt-1">
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      <span v-if="this.datosAreas.tipo_propiedades_id">
                        <span v-if="this.datosAreas.tipo_propiedades_id == 4">Fila</span>
                        <span v-else>Ubicación</span>
                      </span>
                      <span v-else>Seleccione un Área</span>
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <v-select
                      :options="filas"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.filas"
                      class="mb-4 sm:mb-0 pb-1 pt-1"
                      v-validate:fila_validacion_computed.immediate="'required'"
                      name="fila_validacion"
                      data-vv-as=" "
                    >
                      <div slot="no-options">No Se Ha Seleccionado Ningún Área</div>
                    </v-select>
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('fila_validacion') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores['filas.value']"
                      >{{errores['filas.value'][0]}}</span>
                    </div>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      <span v-if="this.datosAreas.tipo_propiedades_id == 4">Ubicación</span>
                      <span v-else>No Aplica</span>
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <v-select
                      :options="lotes"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.lotes"
                      class="mb-4 sm:mb-0 pb-1 pt-1"
                      :disabled="this.datosAreas.tipo_propiedades_id!=4"
                      v-validate:ubicacion_validacion_computed.immediate="'required'"
                      name="ubicacion_validacion"
                      data-vv-as=" "
                    >
                      <div slot="no-options">Seleccione 1 Área</div>
                    </v-select>
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('ubicacion_validacion') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores['lotes.value']"
                      >{{errores['lotes.value'][0]}}</span>
                    </div>
                  </div>
                </div>
                <vs-divider />
                <div class="flex flex-wrap mt-1">
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      <span>Control de Venta</span>
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <v-select
                      :options="ventasAntiguedad"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.ventaAntiguedad"
                      class="mb-4 sm:mb-0 pb-1 pt-1"
                      v-validate:antiguedad_validacion_computed.immediate="'required'"
                      name="antiguedad_validacion"
                      data-vv-as=" "
                    >
                      <div slot="no-options">Seleccione 1</div>
                    </v-select>
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('antiguedad_validacion') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores['ventaAntiguedad.value']"
                      >{{errores['ventaAntiguedad.value'][0]}}</span>
                    </div>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm font-bold">Tipo de Venta</label>
                    <div class="mt-3">
                      <vs-radio
                        vs-name="tipoVenta"
                        v-model="form.venta_referencia_id"
                        :vs-value="1"
                        class="mr-4"
                      >Uso inmediato</vs-radio>
                      <vs-radio
                        vs-name="tipoVenta"
                        v-model="form.venta_referencia_id"
                        :vs-value="2"
                        class="mr-4"
                      >A futuro</vs-radio>
                    </div>
                  </div>
                  <div class="w-full px-2 mt-2">
                    <p class="text-xs">
                      <span class="text-danger font-medium">Ojo:</span>
                      Debe seleccionar en caso de que la venta que se esté registrando haya sido realizada fuera del control del sistema, ya que ese tipo de ventas cuenta con un control especial de números de órden.
                    </p>
                  </div>
                </div>
                <vs-divider />
                <div class="flex flex-wrap mt-1">
                  <div class="w-full px-2 pb-3">
                    <h3 class="text-xl">
                      <feather-icon icon="ShoppingCartIcon" class="mr-2" svgClasses="w-5 h-5" />Ingrese los datos de la venta
                    </h3>
                  </div>

                  <!--nombre del cliente-->
                  <div
                    class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 cursor-pointer"
                  >
                    <label class="text-sm opacity-75 font-bold cursor-pointer">
                      Nombre del Cliente
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      size="large"
                      icon="search"
                      readonly
                      v-validate="'required'"
                      name="cliente_id"
                      data-vv-as=" "
                      type="text"
                      class="w-full pb-1 pt-1 cursor-pointer"
                      placeholder="DEBE SELECCIONAR UN CLIENTE PARA REALIZAR LA VENTA."
                      v-model="form.cliente"
                      maxlength="100"
                      @click="openBuscador=true"
                    />
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('cliente_id') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.cliente_id"
                      >{{errores.cliente_id[0]}}</span>
                    </div>
                  </div>
                  <!--fin nombre del cliente-->
                  <!--vendedor-->
                  <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      <span>Venta realizada por:</span>
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <v-select
                      :options="vendedores"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.vendedor"
                      class="pb-1 pt-1"
                      v-validate:vendedor_validacion_computed.immediate="'required'"
                      name="vendedor"
                      data-vv-as=" "
                    >
                      <div slot="no-options">Seleccione un vendedor</div>
                    </v-select>
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('vendedor') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores['vendedor.value']"
                      >{{errores['vendedor.value'][0]}}</span>
                    </div>
                  </div>
                  <!--Fin de vendedor-->

                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      Fecha de la Venta
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <datepicker
                      :language="spanishDatepicker"
                      :disabled-dates="disabledDates"
                      name="fecha_venta"
                      data-vv-as=" "
                      v-validate="'required'"
                      format="yyyy-MM-dd"
                      placeholder="Fecha de la Venta"
                      v-model="form.fecha_venta"
                      class="w-full pb-1 pt-1"
                    ></datepicker>
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('fecha_venta') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.fecha_venta"
                      >{{errores.fecha_venta[0]}}</span>
                    </div>
                  </div>

                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      Núm. Solicitud
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      v-validate:num_solicitud_validacion_computed.immediate="'required'"
                      name="num_solicitud"
                      data-vv-as=" "
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder=" Núm. Solicitud"
                      v-model="form.num_solicitud"
                      :disabled="(tipo_venta)"
                      maxlength="12"
                    />
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('num_solicitud') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.num_solicitud"
                      >{{errores.num_solicitud[0]}}</span>
                    </div>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      Núm. Convenio
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      v-validate:num_convenio_validacion_computed.immediate="'required'"
                      name="num_convenio"
                      data-vv-as=" "
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Núm. Convenio"
                      v-model="form.convenio"
                      :disabled="!((capturar_num_convenio))"
                      maxlength="16"
                    />
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('num_convenio') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.convenio"
                      >{{errores.convenio[0]}}</span>
                    </div>
                  </div>

                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      Núm. Título
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      v-validate:num_titulo_validacion_computed.immediate="'required'"
                      name="num_titulo"
                      data-vv-as=" "
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Núm. Título"
                      v-model="form.titulo"
                      :disabled="!((tipo_venta*capturar_num_titulo)+capturar_num_titulo)"
                      maxlength="16"
                    />
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('num_titulo') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.titulo"
                      >{{errores.titulo[0]}}</span>
                    </div>
                  </div>

                  <vs-divider />
                </div>
              </div>
            </div>

            <!--datos del titular y beneficiarios-->
            <div class="flex flex-wrap px-2">
              <div class="w-full pt-3 pb-3 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="UserCheckIcon" class="mr-2" svgClasses="w-5 h-5" />Información del Titular
                </h3>
              </div>
              <!--datos del titular-->
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Nombre completo
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="titular"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el nombre del titular"
                  v-model="form.titular"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('titular') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.titular"
                  >{{errores.titular[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Fecha de Nacimiento
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <datepicker
                  :language="spanishDatepicker"
                  :disabled-dates="disabledDates"
                  name="fecha_nacimiento"
                  data-vv-as=" "
                  v-validate="'required'"
                  format="yyyy-MM-dd"
                  placeholder="Fecha de Nacimiento"
                  v-model="form.fecha_nac"
                  class="w-full pb-1 pt-1"
                ></datepicker>
                <div>
                  <span class="text-danger text-sm">{{ errors.first('fecha_nacimiento') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.fecha_nac"
                  >{{errores.fecha_nac[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Domicilio Completo
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="domicilio"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="150"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Domicilio Completo"
                  v-model="form.domicilio"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('domicilio') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.domicilio"
                  >{{errores.domicilio[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Ciudad
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="ciudad"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="45"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese la ciudad"
                  v-model="form.ciudad"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('ciudad') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.ciudad">{{errores.ciudad[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Estado
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="estado"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="45"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el estado"
                  v-model="form.estado"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('estado') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.estado">{{errores.estado[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">Tél. Domicilio</label>
                <vs-input
                  maxlength="25"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el teléfono del domicilio"
                  v-model="form.tel_domicilio"
                />
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Celular
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="celular"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required|min:10'"
                  maxlength="25"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese un número de celular"
                  v-model="form.celular"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('celular') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.celular"
                  >{{errores.celular[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">Tél. Oficina</label>
                <vs-input
                  maxlength="25"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese un teléfono de oficina"
                  v-model="form.tel_oficina"
                />
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">RFC</label>
                <vs-input
                  maxlength="13"
                  name="rfc"
                  data-vv-validate-on="blur"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="e.j. MELM8305281H0"
                  v-model="form.rfc"
                  v-validate="'min:12|max:13'"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('rfc') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.rfc">{{errores.rfc[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">Email</label>
                <vs-input
                  name="email"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'email'"
                  maxlength="75"
                  type="email"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el email"
                  v-model="form.email"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('email') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.email">{{errores.email[0]}}</span>
                </div>
              </div>

              <!--fin de datos del titular-->

              <vs-divider />

              <div class="w-full pt-3 pb-3 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="UserCheckIcon" class="mr-2" svgClasses="w-5 h-5" />Titular Sustituto
                </h3>
              </div>
              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Nombre del titular sustituto
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="titular_sustituto"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="150"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Nombre del titular sustituto"
                  v-model="form.titular_sustituto"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('titular_sustituto') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.titular_sustituto"
                  >{{errores.titular_sustituto[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Parentesco con el titular sustituto
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="parentesco_titular_sustituto"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="45"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ej. Hermano"
                  v-model="form.parentesco_titular_sustituto"
                />
                <div>
                  <span
                    class="text-danger text-sm"
                  >{{ errors.first('parentesco_titular_sustituto') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.parentesco_titular_sustituto"
                  >{{errores.parentesco_titular_sustituto[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Teléfono de contacto
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="telefono_titular_sustituto"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="45"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese un teléfono"
                  v-model="form.telefono_titular_sustituto"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('telefono_titular_sustituto') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.telefono_titular_sustituto"
                  >{{errores.telefono_titular_sustituto[0]}}</span>
                </div>
              </div>

              <vs-divider />
            </div>
            <!--fin de datos del titular y beneficiarios-->

            <div class="w-full pt-3 pb-3 px-2">
              <h3 class="text-xl">
                <feather-icon icon="UsersIcon" class="mr-2" svgClasses="w-5 h-5" />Información de los Beneficiarios
              </h3>
            </div>
            <div v-if="form.beneficiarios.length>0">
              <div
                :key="index"
                v-for="(beneficiario, index) in form.beneficiarios"
                class="flex flex-wrap px-2"
              >
                <!--datos de los beneficiarios-->

                <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
                  <label class="text-sm opacity-75">
                    <span class="font-bold">Beneficiario {{(index+1)}} - Nombre completo</span>
                    <span class="text-danger text-sm">(*)</span>
                  </label>
                  <vs-input
                    :name="'beneficiario'+index"
                    data-vv-as=" "
                    data-vv-validate-on="blur"
                    v-validate="'required'"
                    maxlength="75"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ingrese el nombre del benericiario"
                    v-model="beneficiario.nombre"
                  />
                  <div class="pb-2">
                    <span class="text-danger text-sm">{{ errors.first('beneficiario'+index) }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger text-sm"
                      v-if="errores['beneficiarios.'+index+'.nombre']"
                    >{{errores['beneficiarios.'+index+'.nombre'][0]}}</span>
                  </div>
                </div>
                <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                  <label class="text-sm opacity-75">
                    <span class="font-bold">Beneficiario {{(index+1)}} - Parentesco</span>
                    <span class="text-danger text-sm">(*)</span>
                  </label>
                  <vs-input
                    :name="'parentesco'+index"
                    data-vv-as=" "
                    data-vv-validate-on="blur"
                    v-validate="'required'"
                    maxlength="35"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Parentesco"
                    v-model="beneficiario.parentesco"
                  />
                  <div class="pb-2">
                    <span class="text-danger text-sm">{{ errors.first('parentesco'+index) }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger text-sm"
                      v-if="errores['beneficiarios.'+index+'.parentesco']"
                    >{{errores['beneficiarios.'+index+'.parentesco'][0]}}</span>
                  </div>
                </div>
                <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                  <label class="text-sm opacity-75">
                    <span class="font-bold">Beneficiario {{(index+1)}} - Teléfono</span>
                    <span class="text-danger text-sm">(*)</span>
                  </label>
                  <vs-input
                    :name="'telefono'+index"
                    data-vv-as=" "
                    data-vv-validate-on="blur"
                    v-validate="'required|min:10'"
                    maxlength="35"
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Teléfono"
                    v-model="beneficiario.telefono"
                  />
                  <div class="pb-2">
                    <span class="text-danger text-sm">{{ errors.first('telefono'+index) }}</span>
                  </div>
                  <div class="mt-2">
                    <span
                      class="text-danger text-sm"
                      v-if="errores['beneficiarios.'+index+'.telefono']"
                    >{{errores['beneficiarios.'+index+'.telefono'][0]}}</span>
                  </div>
                </div>
                <div class="w-full sm:w-12/12 md:w-1/12 lg:w-1/12 xl:w-1/12 px-2">
                  <vs-button
                    class="mt-8 float-right"
                    type="flat"
                    color="danger"
                    size="small"
                    icon="remove_circle_outline"
                    @click="remover_beneficiario(index)"
                  >Quitar</vs-button>
                </div>
                <!--fin de datos de los beneficiarios-->
              </div>
            </div>

            <div v-else class="flex flex-wrap pt-4 px-2">
              <div class="w-full px-2">
                <span
                  class="pr-3 pl-3 text-white bg-danger propiedad_tipo"
                >No se ha capturado ningún beneficiario</span>
              </div>
            </div>

            <div class="flex flex-wrap pt-4 px-2">
              <div class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2">
                <div class="mt-5">
                  <p class="text-sm">
                    <span class="text-danger font-medium">Ojo:</span>
                    En este apartado puede agregar cada uno de los beneficiarios que tenga derecho el titular de este contrato.
                  </p>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <div class="mt-5">
                  <vs-button
                    type="flat"
                    size="small"
                    class="float-right"
                    color="success"
                    icon="add_circle_outline"
                    @click="agregar_beneficiario()"
                  >Agregar</vs-button>
                </div>
              </div>
            </div>
            <vs-divider />

            <!--checkout-->
            <div class="flex flex-wrap my-6">
              <div class="w-full sm:w-12/12 md:w-6/12 px-2">
                <div class="w-full pt-3 pb-3 px-2">
                  <h3 class="text-xl">
                    <feather-icon icon="CalendarIcon" class="mr-2" svgClasses="w-5 h-5" />Programación de Pagos
                  </h3>
                </div>
                <div class="flex flex-wrap">
                  <!--precios-->
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      <span>Plan de Venta</span>
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <v-select
                      :options="planesVenta"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.planVenta"
                      class="pb-1 pt-1"
                      v-validate:plan_de_venta_computed.immediate="'required'"
                      name="plan_venta"
                      data-vv-as=" "
                    >
                      <div slot="no-options">No Se Ha Seleccionado Ningún Área</div>
                    </v-select>
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('plan_venta') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores['planVenta.value']"
                      >{{errores['planVenta.value'][0]}}</span>
                    </div>
                  </div>

                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      $ Precio Neto de la Propiedad
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      name="precio_neto"
                      data-vv-as=" "
                      v-validate="'required|numeric'"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Precio Neto de la Propiedad"
                      v-model="form.planVenta.precio_neto"
                      readonly
                    />
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('precio_neto') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.precio_neto"
                      >{{errores.precio_neto[0]}}</span>
                    </div>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      $ Descuento Neto
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      name="descuento_neto"
                      data-vv-as=" "
                      v-validate="'numeric|min_value:0|max_value:'+this.form.planVenta.precio_neto"
                      maxlength="7"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="$ 0.00"
                      v-model="form.descuento"
                    />
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('descuento_neto') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.descuento"
                      >{{errores.descuento[0]}}</span>
                    </div>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      $ Total a Pagar
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      name="total_pagar"
                      data-vv-as=" "
                      v-validate="'numeric|min_value:0'"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="$ 0.00"
                      v-model="form.precio_neto"
                      readonly
                    />
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('total_pagar') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.precio_neto"
                      >{{errores.precio_neto[0]}}</span>
                    </div>
                    <div class="mt-2"></div>
                  </div>
                  <div class="w-full px-2 pt-3 pb-3">
                    <div>
                      <p class="text-sm">
                        <span class="text-danger font-medium leading-6">Ojo:</span>
                        Desde este apartado puede capturar pagos y enganches de manera directa. Si no desea registrar pagos durante la captura de la esta venta, puede seleccionar la opción de
                        <span
                          class="text-danger font-medium leading-6"
                        >"Pagar después"</span>.
                      </p>
                    </div>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      $ Cuota Inicial Mínima
                      <span class="font-medium text-danger">10%</span> del Valor Total
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      name="cuota_inicial"
                      data-vv-as=" "
                      v-validate="'required|numeric|min_value:'+(cuota_inicial)+'|max_value:'+(maxima_cuota_inicial)"
                      maxlength="7"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="$ 0.00"
                      v-model="form.enganche_inicial"
                      :disabled="plan_venta"
                    />
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('cuota_inicial') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.enganche_inicial"
                      >{{errores.enganche_inicial[0]}}</span>
                    </div>
                    <div class="mt-2"></div>
                  </div>

                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-bold">
                      <span>¿Guardar Cuota Inicial como cobrada?</span>
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <v-select
                      :options="opcionesPagar"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.opcionPagar"
                      class="pb-1 pt-1"
                      v-validate:plan_de_venta_computed.immediate="'required'"
                      name="plan_venta"
                      data-vv-as="Plan de Venta"
                      :disabled="opcionPagar_validacion_computed"
                    >
                      <div slot="no-options">Seleccione una opción</div>
                    </v-select>
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('plan_venta') }}</span>
                    </div>
                    <div class="mt-2"></div>
                  </div>

                  <!--datos del pago inicial-->
                  <div class="w-full" v-if="this.form.opcionPagar.value==1">
                    <div class="flex flex-wrap">
                      <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                        <label class="text-sm opacity-75 font-bold">
                          <span>Formas de Pago</span>
                          <span class="text-danger text-sm">(*)</span>
                        </label>
                        <v-select
                          :options="formasPago"
                          :clearable="false"
                          :dir="$vs.rtl ? 'rtl' : 'ltr'"
                          v-model="form.formaPago"
                          class="pb-1 pt-1"
                          v-validate:plan_de_venta_computed.immediate="'required'"
                          name="formas_pago"
                          data-vv-as="Plan de Venta"
                        >
                          <div slot="no-options">Seleccione una Forma de Pago</div>
                        </v-select>
                        <div>
                          <span class="text-danger text-sm">{{ errors.first('formas_pago') }}</span>
                        </div>
                        <div class="mt-2"></div>
                      </div>

                      <div
                        class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                        v-if="this.form.formaPago.value!=1"
                      >
                        <label class="text-sm opacity-75 font-bold">
                          Banco
                          <span class="text-danger text-sm">(*)</span>
                        </label>
                        <vs-input
                          data-vv-validate-on="blur"
                          v-validate:banco_computed="'required'"
                          name="banco_validacion"
                          data-vv-as=" "
                          type="text"
                          class="w-full pb-1 pt-1"
                          placeholder="Nombre del Banco"
                          v-model="form.banco"
                          maxlength="36"
                        />
                        <div>
                          <span class="text-danger text-sm">{{ errors.first('banco_validacion') }}</span>
                        </div>
                        <div class="mt-2">
                          <span
                            class="text-danger text-sm"
                            v-if="this.errores.banco"
                          >{{errores.banco[0]}}</span>
                        </div>
                      </div>

                      <div
                        class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                        v-if="this.form.formaPago.value==3"
                      >
                        <label class="text-sm opacity-75 font-bold">Núm. Operación</label>
                        <vs-input
                          name="Nombre"
                          type="text"
                          class="w-full pb-1 pt-1"
                          placeholder="Número de Operacion"
                          v-model="form.num_operacion"
                          maxlength="36"
                        />
                        <div class="mt-2">
                          <span
                            class="text-danger text-sm"
                            v-if="this.errores.num_operacion"
                          >{{errores.num_operacion[0]}}</span>
                        </div>
                      </div>

                      <div
                        class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                        v-if="this.form.formaPago.value==2"
                      >
                        <label class="text-sm opacity-75 font-bold">Número de Cheque</label>
                        <vs-input
                          name="Nombre"
                          type="text"
                          class="w-full pb-1 pt-1"
                          placeholder="Número de Cheque"
                          v-model="form.num_cheque"
                          maxlength="7"
                        />
                        <div class="mt-2"></div>
                      </div>

                      <div
                        class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2"
                        v-if="this.form.formaPago.value==4 ||  this.form.formaPago.value==5"
                      >
                        <label class="text-sm opacity-75 font-bold">Últimos 4 Digitos</label>
                        <vs-input
                          name="ultimos_digitos"
                          data-vv-as=" "
                          data-vv-validate-on="blur"
                          v-validate="'min:4|max:4|numeric'"
                          type="text"
                          class="w-full pb-1 pt-1"
                          placeholder="Últimos 4 Dígitos de la tarjeta"
                          maxlength="4"
                          v-model="form.ultimosdigitos"
                        />
                        <div>
                          <span class="text-danger text-sm">{{ errors.first('ultimos_digitos') }}</span>
                        </div>
                        <div class="mt-2">
                          <span
                            class="text-danger text-sm"
                            v-if="this.errores.ultimosdigitos"
                          >{{errores.ultimosdigitos[0]}}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!--fin de datos del pago inicial--->

                  <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 my-5">
                    <div
                      class="pb-6 text-center"
                      v-if="this.form.planVenta.precio_neto==this.form.descuento"
                    >
                      <span
                        class="bg-danger text-white font-medium pr-3 pl-3"
                      >Ojo, está haciendo un descuento del 100%, verifique si desea continuar.</span>
                    </div>
                    <vs-button
                      icon-pack="feather"
                      icon="icon-database"
                      color="success"
                      class="w-full"
                      @click="acceptAlert()"
                    >Guardar Venta</vs-button>
                  </div>
                  <div class="w-full px-2 pt-3 pb-3">
                    <div>
                      <p class="text-sm">
                        <span class="text-danger font-medium leading-6">Ojo:</span>
                        Se recomienda revisar la Información capturada antes de mandar
                        <span
                          class="text-danger font-medium leading-6"
                        >Guardar la venta</span>, si ya revisó que todo está correcto puede proceder.
                      </p>
                    </div>
                  </div>

                  <!--fin de precios-->
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 px-2">
                <div class="w-full pt-3 pb-3">
                  <h3 class="text-xl">
                    <feather-icon icon="ShoppingCartIcon" class="mr-2" svgClasses="w-5 h-5" />Resumen de la Venta
                  </h3>
                </div>
                <!--resumen de la venta-->
                <div class="flex flex-wrap mt-6">
                  <div class="w-full sm:w-12/12 ml-auto md:w-12/12 lg:w-12/12 xl:w-12/12">
                    <div class="flex flex-wrap">
                      <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                        <span class="text-gray-100 font-bold">Ubicación</span>
                      </div>
                      <div
                        class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right text-gray-900 font-medium"
                      >
                        <span v-if="this.datosAreas.id">
                          <span
                            v-if="this.datosAreas.tipo_propiedades_id == 1 || this.datosAreas.tipo_propiedades_id == 2 || this.datosAreas.tipo_propiedades_id == 3 || this.datosAreas.tipo_propiedades_id == 5 || this.datosAreas.tipo_propiedades_id == 6"
                          >
                            <span
                              v-if="this.form.filas.value!=''"
                            >Propiedad {{this.datosAreas['tipo_propiedad'].tipo+' Ubicación '+this.form.filas.label}}</span>
                            <span v-else class="text-danger">Seleccione una ubicación</span>
                          </span>
                          <span v-else>
                            <span
                              v-if="this.form.filas.value!='' && this.form.lotes.value!=''"
                            >Propiedad {{this.datosAreas['tipo_propiedad'].tipo+' Ubicación '+this.form.lotes.label}}</span>
                            <span v-else class="text-danger">Seleccione una ubicación</span>
                          </span>
                        </span>
                        <span v-else class="text-danger">Seleccione un Área del Cementerio</span>
                      </div>
                    </div>
                    <vs-divider />
                    <div class="flex flex-wrap">
                      <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                        <span class="text-gray-100 font-bold">Vendedor</span>
                      </div>
                      <div
                        class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right text-gray-900 font-medium"
                      >
                        <span v-if="this.form.vendedor.value!=''">{{this.form.vendedor.label}}</span>
                        <span v-else class="text-danger">Seleccione un Vendedor</span>
                      </div>
                    </div>
                    <vs-divider />
                    <div class="flex flex-wrap">
                      <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                        <span class="text-gray-100 font-bold">Tipo Venta</span>
                      </div>
                      <div
                        class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right text-gray-900 font-medium"
                      >
                        <span v-if="this.form.venta_referencia_id==1">Uso inmediato</span>
                        <span v-else>A futuro</span>
                      </div>
                    </div>
                    <vs-divider />
                    <div class="flex flex-wrap">
                      <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                        <span class="text-gray-100 font-bold">Plan de Venta</span>
                      </div>
                      <div
                        class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right text-gray-900 font-medium"
                      >
                        <span v-if="this.form.planVenta.value>=0">
                          <span
                            v-if="this.form.planVenta.value==0"
                          >Pago Único de {{this.form.precio_neto | numFormat('0,000.00')}} Pesos</span>
                          <span
                            v-else
                          >Pago Inicial de {{this.form.enganche_inicial | numFormat('0,000.00')}} Pesos. Más {{this.form.planVenta.value}} Mensualidades de {{((this.form.precio_neto-this.form.enganche_inicial)/this.form.planVenta.value) | numFormat('0,000.00')}} Pesos</span>
                        </span>
                        <span v-else class="text-danger">Seleccione un Plan de Venta</span>
                      </div>
                    </div>
                    <vs-divider />

                    <div class="flex flex-wrap">
                      <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                        <span class="text-gray-100 font-bold">Sub Total</span>
                      </div>
                      <div class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right">
                        <!--obtencion del precio total menos iva-->
                        <span
                          class="text-gray-900 font-medium"
                        >{{(this.form.precio_neto*.84) | numFormat('0,000.00')}} Pesos</span>
                      </div>
                    </div>
                    <vs-divider />

                    <div class="flex flex-wrap">
                      <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                        <span class="text-gray-100 font-bold">Descuento</span>
                      </div>
                      <div class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right">
                        <span
                          class="text-gray-900 font-medium"
                        >{{this.form.descuento | numFormat('0,000.00')}} Pesos</span>
                      </div>
                    </div>
                    <vs-divider />

                    <div class="flex flex-wrap">
                      <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                        <span class="text-gray-100 font-bold">IVA</span>
                      </div>
                      <div class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right">
                        <!--obtencion del  iva-->
                        <span
                          class="text-gray-900 font-medium"
                        >{{(this.form.precio_neto*.16) | numFormat('0,000.00')}} Pesos</span>
                      </div>
                    </div>
                    <vs-divider />
                    <div class="flex flex-wrap text-success">
                      <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                        <span class="text-gray-100 font-bold">Total a Pagar</span>
                      </div>
                      <div class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right">
                        <span
                          class="text-gray-900 font-medium"
                        >{{this.form.precio_neto | numFormat('0,000.00')}} Pesos</span>
                      </div>
                    </div>
                    <vs-divider />
                  </div>
                </div>
                <!--fin del resumen de la venta-->
              </div>
            </div>
            <!--fin del checkout-->
            <vs-divider />
          </div>
        </template>
      </vx-card>
      <!--fin venta-->
    </vs-popup>
    <Password
      :show="operConfirmar"
      :callback-on-success="callback"
      @closeVerificar="closeChecker"
      :accion="accionNombre"
    ></Password>
    <Confirmar
      :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarSinPassword=false"
      :accion="accionConfirmarSinPassword"
      :confirmarButton="botonConfirmarSinPassword"
    ></Confirmar>

    <ConfirmarAceptar
      :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar"
      @closeVerificar="openConfirmarAceptar=false"
      :accion="'He revisado la información y quiero guardar la venta'"
      :confirmarButton="'Guardar Venta'"
    ></ConfirmarAceptar>

    <ClientesBuscador :show="openBuscador" @closeBuscador="openBuscador=false"></ClientesBuscador>
  </div>
</template>
<script>
import Mapa from "../Mapa";
import Confirmar from "@pages/Confirmar";
//componente de password
import Password from "@pages/confirmar_password";
import cementerio from "@services/cementerio";
import usuarios from "@services/Usuarios";
import vSelect from "vue-select";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import ClientesBuscador from "@pages/clientes/searcher.vue";

/**VARIABLES GLOBALES */
import { alfabeto } from "@/VariablesGlobales";

export default {
  components: {
    "v-select": vSelect,
    Password,
    Datepicker,
    Mapa,
    Confirmar,
    ConfirmarAceptar,
    ClientesBuscador
  },
  props: {
    show: {
      type: Boolean,
      required: true
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.get_ventas_referencias_propiedades();
        this.get_vendedores();
        this.get_sat_formas_pago();
        this.get_antiguedades();
        this.idAreaInicial = 29;
      } else {
        /**forzando el cambio de area para que se actualice al reabrir la ventana */
        if (this.idAreaInicial > 2) {
          this.idAreaInicial -= 1;
        } else {
          this.idAreaInicial += 1;
        }
      }
    },

    //aqui obtengo los datos necesarios para poder saber cuantas filas tiene una propiedad
    "form.filas": function(newValue, oldValue) {
      if (newValue.value != "") {
        if (this.datosAreas.tipo_propiedades_id == 4) {
          this.lotes = [];
          this.lotes.push({ label: "Seleccione 1", value: "" });
          this.datosAreas["filas_columnas"].forEach(element => {
            //aqui obtengo los datos para poder llamar la funcion que me traera los
            //valores necesarios para poder cargar los lotes que existen segun cada fila
            if (element.fila == this.form.filas.value) {
              //aqui tengo los valores para saber en que columna empieza y acaba cada fila
              for (let index = 1; index <= this.datosAreas.columnas; index++) {
                //aqui se debe crear unicamente los lotes que estan en cada fila segun la terraza
                if (
                  index >= element.empieza_columna &&
                  index <= element.fin_columna
                ) {
                  this.lotes.push({
                    label:
                      "Fila " +
                      this.alfabeto[this.form.filas.value - 1] +
                      " - Lote " +
                      index,
                    value: index
                  });
                }
              }
              //la primero opcion

              this.form.lotes = this.lotes[1];
              return;
            }
          });
        } else {
          this.lotes = [];
          this.lotes.push({ label: "Seleccione 1", value: "" });
          this.form.lotes = { label: "Seleccione 1", value: "" };
        }
      } else {
        this.lotes = [];
        this.lotes.push({ label: "Seleccione 1", value: "" });
        this.form.lotes = { label: "Seleccione 1", value: "" };
      }
    },
    //watchs con mapa
    datosAreas: function(newValue, oldValue) {
      //creo las posibles opciones para filas y modulos
      if (newValue != []) {
        //actualizo el id del tipo de propiedad
        this.form.propiedades_id = this.datosAreas.tipo_propiedades_id;
        //creo uniplex, duplex, triplex y cuadruplex sin nicho
        this.filas = [];
        this.filas.push({ label: "Seleccione 1", value: "" });
        this.form.filas = this.filas[0];
        if (this.datosAreas.tipo_propiedades_id != 4) {
          this.lotes = [];
          this.lotes.push({ label: "Seleccione 1", value: "" });
          this.form.lotes = { label: "Seleccione 1", value: "" };
          //al encontrar el numero de filas que les corresponden segun la propiedad creo las filas para que la seleccione el usuario
          let indicador_propiedad = "";
          if (
            this.datosAreas.tipo_propiedades_id != 4 ||
            this.datosAreas.tipo_propiedades_id == 3
          ) {
            indicador_propiedad = "Módulo ";
          } else if (this.datosAreas.tipo_propiedades_id == 3) {
            indicador_propiedad = "Fila ";
          }
          for (let index = 1; index <= this.datosAreas.filas; index++) {
            this.filas.push({
              label: indicador_propiedad + " - " + index,
              value: index
            });
          }
        } else {
          //filas para las terrazas
          for (let index = 1; index <= this.datosAreas.filas; index++) {
            this.filas.push({
              label: "Fila - " + this.alfabeto[index - 1],
              value: index
            });
          }
        }
        //la primero opcion
        this.form.filas = this.filas[1];
        //cargo los precios

        if (this.form.venta_referencia_id > 0) this.cargarPlanes();
      }
    },

    "form.venta_referencia_id": function(newValue, oldValue) {
      if (this.form.venta_referencia_id > 0) this.cargarPlanes();
    },

    "form.planVenta": function(newValue, oldValue) {
      //actualizo el precio actual de la propiedad para mostrar el total neto
      if (newValue.value == 0) {
        this.form.precio_neto = newValue.precio_neto - this.form.descuento;
        this.form.enganche_inicial = newValue.precio_neto - this.form.descuento;
      } else if (newValue.value > 0) {
        this.form.precio_neto = newValue.precio_neto - this.form.descuento;
        this.form.enganche_inicial =
          (newValue.precio_neto - this.form.descuento) / 10;
      }
    },
    "form.descuento": function(newValue, oldValue) {
      //actualizo el total a pagar
      if (isNaN(newValue)) {
        //no es numero y marca error, pone el valor en cero
        this.form.precio_neto = "Error";
      } else {
        //si es numero y poner un valor bueno
        if (Number(newValue) > Number(this.form.planVenta.precio_neto)) {
          //error de captura ps el descuento no sobrepasa el precio
          this.form.precio_neto = "Error";
        } else {
          //todo bien
          this.form.precio_neto = Number(
            Number(this.form.planVenta.precio_neto) - Number(newValue)
          );
        }

        //ajusto la cuota inicial si es de contado
        if (this.form.planVenta.value == 0) {
          this.form.enganche_inicial = this.form.precio_neto;
        }
      }
    },

    "form.precio_neto": function(newValue, oldValue) {
      if (newValue == 0) {
        //la venta es 100% gratis
        this.form.opcionPagar = {
          label: "Pagar Después",
          value: 0
        };
      }
    }
    //fin de watchs con mapa
  },
  computed: {
    banco_computed: function() {
      if (this.form.formaPago.value != 1) {
        return this.form.banco;
      } else return true;
    },
    opcionPagar_validacion_computed: function() {
      if (this.form.precio_neto == 0) {
        return true;
      } else return false;
    },
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      }
    },
    tipo_venta: function() {
      //definiendo el tipo de uso, si es true es venta de uso inmediato si es false es venta de uso futuro
      if (this.form.venta_referencia_id == 1) {
        //uso inmediato
        return true;
      } else {
        //a futuro
        return false;
      }
    },

    capturar_num_convenio: function() {
      if (this.form.ventaAntiguedad.value > 1) {
        return true;
      } else {
        return false;
      }
    },
    capturar_num_titulo: function() {
      if (this.form.ventaAntiguedad.value == 3) {
        return true;
      } else {
        return false;
      }
    },

    plan_venta: function() {
      if (this.form.planVenta.value == 0) {
        return true;
      } else {
        return false;
      }
    },

    cuota_inicial: function() {
      //se define la cuota inicial segun el precio de la propiedad y el plan de venta
      if (this.form.planVenta.value == 0) {
        //si el plan de venta es a contado
        return this.form.precio_neto;
      } else {
        //si el plan de venta no es a contado debe dar como minimo el 10% de lo que vale la propiedad ya con descuento
        return this.form.precio_neto * 0.1;
      }
    },

    maxima_cuota_inicial: function() {
      //se define la cuota inicial maxima para las mensualidades
      if (this.form.planVenta.value == 0) {
        //si el plan de venta es a contado
        return this.form.precio_neto;
      } else {
        //si el plan de venta no es a contado debe dar como minimo el 10% de lo que vale la propiedad ya con descuento
        return this.form.precio_neto * 0.5;
      }
    },

    //validaciones calculadas
    //valido que elija un plan de venta
    plan_de_venta_computed: function() {
      return this.form.planVenta.value;
    },
    fila_validacion_computed: function() {
      return this.form.filas.value;
    },
    ubicacion_validacion_computed: function() {
      if (this.form.propiedades_id == 4) {
        //terrazas
        return this.form.lotes.value;
      } else {
        return true;
      }
    },

    antiguedad_validacion_computed: function() {
      return this.form.ventaAntiguedad.value;
    },

    vendedor_validacion_computed: function() {
      return this.form.vendedor.value;
    },
    fecha_venta_validacion_computed: function() {
      return this.form.fecha_venta;
    },

    num_solicitud_validacion_computed: function() {
      //checo que el dato venta a futuro este activo
      if (this.form.venta_referencia_id == 2) {
        return this.form.num_solicitud;
      } else return true;
    },

    num_convenio_validacion_computed: function() {
      //checo que el dato venta a futuro este activo y que sea de venta antes del sistema
      if (this.form.ventaAntiguedad.value >= 2) {
        return this.form.convenio;
      } else return true;
    },
    num_titulo_validacion_computed: function() {
      //checo que el dato venta a futuro este activo
      if (this.form.ventaAntiguedad.value == 3) {
        return this.form.titulo;
      } else return true;
    },
    fecha_nacimiento_validacion_computed: function() {
      return this.form.fecha_nac;
    },
    banco_validacion_computed: function() {
      if (this.form.opcionPagar.value == 1 && this.form.formaPago.value != 1) {
        //quiere decir que selecciono la opcion de si pagar
        return this.form.banco;
      } else {
        return true;
      }
    },
    //fin de validaciones calculadas
    //crear ubicacion
    crear_ubicacion_computed: function() {
      if (this.datosAreas.tipo_propiedades_id == 4) {
        //ubicacion para cuadriplex de terrazas
        //id del tipo de propiedad - id de la propiedad - num fila - num columna
        return (
          this.form.tipo_propiedades_id +
          "-" +
          this.datosAreas.id +
          "-" +
          this.form.filas.value +
          "-" +
          this.form.lotes.value
        );
      } else {
        //id del tipo de propiedad - id de la propiedad - num fila - 1
        return (
          this.form.tipo_propiedades_id +
          "-" +
          this.datosAreas.id +
          "-" +
          this.form.filas.value +
          "-" +
          1
        );
      }
    }
    //fin de crear ubicacion
  },
  data() {
    return {
      openBuscador: false,
      cliente: "",
      idAreaInicial: 1,
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      alfabeto: alfabeto,
      disabledDates: {
        from: new Date()
      },
      spanishDatepicker: es,
      operConfirmar: false,
      openConfirmarSinPassword: false,

      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Guardar Venta",
      ventasReferencias: [],
      ventasAntiguedad: [],
      filas: [],
      lotes: [],
      vendedores: [],
      //variables con mapa
      planesVenta: [
        {
          label: "Seleccione 1",
          value: "",
          precio_neto: "",
          enganche_inicial: ""
        }
      ],
      datosAreas: [],
      formasPago: [],
      opcionesPagar: [
        {
          label: "Si",
          value: 1
        },
        {
          label: "Pagar Después",
          value: 0
        }
      ],
      //fin var con mapa
      form: {
        //ubicacion
        tipo_propiedades_id: 0,
        propiedades_id: 0,
        num_fila: 0,
        num_columna: 0,
        ubicacion: "",
        //fin de ubicacion
        fecha_venta: "",
        //datos del titular
        titular: "",
        domicilio: "",
        ciudad: "",
        estado: "",
        tel_domicilio: "",
        celular: "",
        tel_oficina: "",
        rfc: "",
        email: "",
        fecha_nac: "",
        /**titular substituto */
        titular_sustituto: "",
        parentesco_titular_sustituto: "",
        telefono_titular_sustituto: "",
        //
        num_solicitud: "",
        convenio: "",
        titulo: "",
        venta_referencia_id: 1,
        filas: {
          label: "Seleccione 1",
          value: ""
        },
        lotes: {
          label: "Seleccione 1",
          value: ""
        },
        //variables con mapa
        planVenta: {
          label: "Seleccione 1",
          value: "",
          precio_neto: "",
          enganche_inicial: ""
        },
        ventaAntiguedad: {
          label: "Seleccione 1",
          value: ""
        },
        enganche_inicial: "",
        precio_neto: "",
        descuento: 0,
        minima_cuota_inicial: 0,
        maxima_cuota_inicial: 0,
        vendedor: {
          label: "Seleccione 1",
          value: ""
        },
        opcionPagar: {
          label: "Pagar Después",
          value: 0
        },
        beneficiarios: [],
        index_beneficiario: 0,
        formaPago: {
          label: "Seleccione 1",
          value: ""
        },
        banco: "",
        ultimosdigitos: "",
        num_cheque: "",
        num_operacion: ""
        //fin var con mapa
      },
      errores: []
    };
  },
  methods: {
    //obtengo los datos del area seleccionada desde el mapa  "child"
    getDatosTipoPropiedad(datos) {
      //actualizo los datos que se ocupan para manejar las ubicaciones de las propiedades
      this.datosAreas = datos;
      this.form.propiedades_id = this.datosAreas.tipo_propiedades_id;
    },
    acceptAlert() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$vs.notify({
              title: "Error",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000"
            });
            return;
          } else {
            //se confirma la cntraseña
            //una vez todo validado, actualizo los ultimos datos de ubicacion
            this.form.propiedades_id = this.datosAreas.id;
            this.form.tipo_propiedades_id = this.datosAreas.tipo_propiedades_id;

            this.form.ubicacion = this.crear_ubicacion_computed;
            this.form.minima_cuota_inicial = this.cuota_inicial;
            this.form.maxima_cuota_inicial = this.maxima_cuota_inicial;
            //fin de actualizar datos de ubicacion
            this.callBackConfirmarAceptar = this.guardarVenta;
            this.openConfirmarAceptar = true;
          }
        })
        .catch(() => {});
    },

    guardarVenta() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      cementerio
        .guardarVenta(this.form)
        .then(res => {
          //console.log(res);
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Ventas de Propiedades",
              text: "Se ha guardado la venta correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000
            });
            //this.$emit("ver_pdfs_nueva_venta", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Ventas de Propiedades",
              text: "Error al guardar la venta, por favor reintente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 4000
            });
          }

          this.$vs.loading.close();
        })
        .catch(err => {
          if (err.response) {
            //console.log("guardarVenta -> err.response", err.response);
            if (err.response.status == 403) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Permiso denegado",
                text:
                  "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 4000
              });
            } else if (err.response.status == 422) {
              //checo si existe cada error
              this.errores = err.response.data.error;
              if (this.errores.ubicacion) {
                //la propiedad esa ya ha sido vendida
                this.$vs.notify({
                  title: "Seleccionar Terreno",
                  text: "Este terreno ya ha sido vendido previamente.",
                  iconPack: "feather",
                  icon: "icon-alert-circle",
                  color: "danger",
                  time: 5000
                });
              }

              this.$vs.notify({
                title: "Guardar Venta",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000
              });
              //console.log(err.response);
            }
          }
          this.$vs.loading.close();
        });
    },
    cancel() {
      this.$emit("closeVentana");
    },
    get_ventas_referencias_propiedades() {
      this.$vs.loading();
      cementerio
        .get_ventas_referencias_propiedades()
        .then(res => {
          //le agrego todos los roles
          this.ventasReferencias = [];
          this.ventasReferencias.push({ label: "Seleccione 1", value: "" });
          res.data.forEach(element => {
            /**AGREGO LOS DEMAS ROLES */
            this.ventasReferencias.push({
              label: element.tipo_venta,
              value: element.id
            });
          });
          this.$vs.loading.close();
        })
        .catch(err => {
          this.$vs.loading.close();
        });
    },
    //get vendedores
    get_vendedores() {
      cementerio
        .get_vendedores()
        .then(res => {
          //le agrego todos los usuarios vendedores
          this.vendedores = [];
          this.vendedores.push({ label: "Seleccione 1", value: "" });
          res.data.forEach(element => {
            this.vendedores.push({
              label: element.nombre,
              value: element.id
            });
          });
          this.form.vendedor = this.vendedores[0];
        })
        .catch(err => {});
    },
    get_antiguedades() {
      cementerio
        .get_antiguedades()
        .then(res => {
          //le agrego todos los usuarios vendedores
          this.ventasAntiguedad = [];
          res.data.forEach(element => {
            this.ventasAntiguedad.push({
              label: element.antiguedad,
              value: element.id
            });
          });
          this.form.ventaAntiguedad = this.ventasAntiguedad[0];
        })
        .catch(err => {});
    },
    //get formad de pago
    get_sat_formas_pago() {
      cementerio
        .get_sat_formas_pago()
        .then(res => {
          //le agrego todos los usuarios vendedores
          this.formasPago = [];
          //this.formasPago.push({ label: "Seleccione 1", value: "" });
          res.data.forEach(element => {
            this.formasPago.push({
              label: element.forma,
              value: element.id
            });
          });
          this.form.formaPago = this.formasPago[0];
          //selecciono efectivo por default
        })
        .catch(err => {});
    },

    cargarPlanes() {
      this.planesVenta = [];
      this.planesVenta.push({
        label: "Seleccione 1",
        value: "",
        precio_neto: "",
        enganche_inicial: ""
      });
      if (this.datosAreas.id) {
        this.datosAreas["tipo_propiedad"].precios.forEach(element => {
          //verifico si la compra es a futuro o de uso inmediato
          if (this.form.venta_referencia_id == 1) {
            if (element.tipo_precios_id == 1) {
              //es una venta de uso inmediato y no puede haber opciones de meses
              //precio de contado a 0 meses 1 solo pago
              this.planesVenta.push({
                label: "Pago de contado",
                value: Number(element.meses),
                precio_neto: Number(element.precio_neto),
                enganche_inicial: Number(element.enganche_inicial)
              });
            }
          } else {
            //la venta es a futuro y puede seleccionar todas los siguientes planes de venta
            /**AGREGO LOS DEMAS ROLES */
            if (element.tipo_precios_id == 1) {
              //precio de contado a 0 meses 1 solo pago
              /* this.planesVenta.push({
                label: "Pago de contado",
                value: Number(element.meses),
                precio_neto: Number(element.precio_neto),
                enganche_inicial: Number(element.enganche_inicial)
              });
              */
            } else {
              //precios de pagos a meses
              this.planesVenta.push({
                label: element.meses + " Meses",
                value: Number(element.meses),
                precio_neto: Number(element.precio_neto),
                enganche_inicial: Number(element.enganche_inicial)
              });
            }
          }
        });

        //selecciono el primero precio automaticamente
        if (this.planesVenta.length > 1) {
          this.form.planVenta = this.planesVenta[1];
        } else {
          this.form.planVenta = this.planesVenta[0];
          this.$vs.notify({
            title: "Planes de Venta",
            text:
              "No hay planes de venta que mostrar. Debe ingresarlos en la sección 'Planes de Ventas'",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-right",
            time: "10000"
          });
        }
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
      //area de la terraza 1
      this.form.lotes = this.lotes[0];
      this.form.venta_referencia_id = 0;
      this.form.num_solicitud = "";
      this.form.convenio = "";
      this.form.titulo = "";
      this.form.titular = "";
      this.form.fecha_nac = "";
      this.form.domicilio = "";
      this.form.ciudad = "";
      this.form.estado = "";
      this.form.telefono = "";
      this.form.celular = "";
      this.form.tel_oficina = "";
      this.form.rfc = "";
      this.form.email = "";
      this.form.descuento = 0;

      this.form.beneficiarios = [];
      this.form.planVenta = this.planesVenta[0];
      this.form.fecha_venta = "";
      this.form.opcionPagar = {
        label: "Pagar Después",
        value: 0
      };
      this.form.banco = "";
      this.form.num_cheque = "";
      this.form.ultimosdigitos = "";
      this.form.num_operacion = "";
    },

    closeChecker() {
      this.operConfirmar = false;
    },

    //agregar beneficiario
    agregar_beneficiario() {
      //verifico si todos los datos estan completos para dejarle agregar nuevos
      let errores_de_captura_en_datos = 0;
      /**maximo 10 beneficiarios */
      if (this.form.beneficiarios.length < 10) {
        this.form.beneficiarios.forEach(element => {
          if (
            element.nombre === "" ||
            element.parentesco === "" ||
            element.telefono === ""
          ) {
            errores_de_captura_en_datos += 1;
          }
        });
        if (errores_de_captura_en_datos > 0) {
          //no paso
          this.$vs.notify({
            title: "Error",
            text: "Verifique que todos los datos han sido capturados",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "top-center",
            time: "4000"
          });
        } else {
          //si paso la prueba de toodos los datos
          this.form.beneficiarios.push({
            nombre: "",
            parentesco: "",
            telefono: ""
          });
        }
      } else {
        //pasa de 5 beneficiarios
        this.$vs.notify({
          title: "Límite de Beneficiarios",
          text: "Ha llegado a 10, el límite de beneficiarios",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "top-center",
          time: "4000"
        });
      }
    },

    //remover beneficiario
    remover_beneficiario(index_beneficiario) {
      this.botonConfirmarSinPassword = "eliminar";
      this.accionConfirmarSinPassword =
        "¿Desea eliminar este beneficiario? Los datos quedarán eliminados del sistema?";
      this.form.index_beneficiario = index_beneficiario;
      this.callBackConfirmar = this.remover_beneficiario_callback;
      this.openConfirmarSinPassword = true;
    },
    //remover beneficiario callback quita del array al beneficiario seleccionado
    remover_beneficiario_callback() {
      this.form.beneficiarios.splice(this.form.index_beneficiario, 1);
    }
  },
  created() {}
};
</script>