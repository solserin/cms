<template >
  <div class="centerx">
    <vs-popup
      fullscreen
      close="cancelar"
      title="Venta de Propiedades del Cementerio"
      :active.sync="showVentana"
      button-close-hidden
    >
      <!--inicio venta-->
      <vx-card class="pt-5 pb-8">
        <template slot="no-body">
          <div class="venta-details">
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 mt-5">
                <!--mapa del cementerio-->
                <div mt-5>
                  <Mapa
                    class="mt-5"
                    :idAreaInicial="0"
                    @getDatosTipoPropiedad="getDatosTipoPropiedad"
                  ></Mapa>
                </div>

                <!--fin del mapa del cementerio-->
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
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
                    <label class="text-sm opacity-75 font-medium">
                      <span>Seleccione 1 Área</span>
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <v-select
                      :options="filas"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.filas"
                      class="mb-4 sm:mb-0 pb-1 pt-1"
                    >
                      <div slot="no-options">No Se Ha Seleccionado Ningún Área</div>
                    </v-select>

                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.genero"
                      >{{errores.genero[0]}}</span>
                    </div>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-medium">
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
                    >
                      <div slot="no-options">Seleccione 1 Área</div>
                    </v-select>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.genero"
                      >{{errores.genero[0]}}</span>
                    </div>
                  </div>
                </div>
                <vs-divider />
                <div class="flex flex-wrap mt-1">
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm font-medium">Venta liquidada antes del sistema</label>
                    <div class="mt-2">
                      <div class="mt-2">
                        <vs-radio
                          vs-name="ventaAntiguedad"
                          v-model="form.ventaAntesdelSistema"
                          :vs-value="true"
                          class="mr-4"
                        >Si</vs-radio>
                        <vs-radio
                          vs-name="ventaAntiguedad"
                          v-model="form.ventaAntesdelSistema"
                          :vs-value="false"
                          class="mr-4"
                        >No</vs-radio>
                      </div>
                    </div>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm font-medium">Tipo de Venta</label>
                    <div class="mt-2">
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

                  <!--precios-->
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75 font-medium">
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
                      data-vv-as="Plan de Venta"
                    >
                      <div slot="no-options">No Se Ha Seleccionado Ningún Área</div>
                    </v-select>
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('plan_venta') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.nombre"
                      >{{errores.nombre[0]}}</span>
                    </div>
                  </div>

                  <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                    <label class="text-sm opacity-75">
                      $ Total Neto
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Total Neto"
                      v-model="form.precio_neto"
                      readonly
                    />
                  </div>
                  <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                    <label class="text-sm opacity-75">
                      $ Descuento Neto
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      name="Nombre"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="$ 0.00"
                      v-model="form.descuento"
                    />
                  </div>
                  <!--fin de precios-->
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75">
                      Núm. Solicitud
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      name="Nombre"
                      data-vv-validate-on="blur"
                      v-validate="'required'"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Ingrese el nombre del usuario"
                      v-model="form.nombre"
                      :disabled="(tipo_venta)"
                    />
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('Nombre') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.nombre"
                      >{{errores.nombre[0]}}</span>
                    </div>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75">
                      Núm. Convenio
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      name="Nombre"
                      data-vv-validate-on="blur"
                      v-validate="'required'"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Ingrese el nombre del usuario"
                      v-model="form.nombre"
                      :disabled="!((!tipo_venta)*(form.ventaAntesdelSistema))"
                    />
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('Nombre') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.nombre"
                      >{{errores.nombre[0]}}</span>
                    </div>
                  </div>

                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75">
                      Núm. Título
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-input
                      name="Nombre"
                      data-vv-validate-on="blur"
                      v-validate="'required'"
                      type="text"
                      class="w-full pb-1 pt-1"
                      placeholder="Ingrese el nombre del usuario"
                      v-model="form.nombre"
                      :disabled="!((tipo_venta*form.ventaAntesdelSistema)+form.ventaAntesdelSistema)"
                    />
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('Nombre') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.nombre"
                      >{{errores.nombre[0]}}</span>
                    </div>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                    <label class="text-sm opacity-75">
                      Fecha de la Venta
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <datepicker
                      :language="spanishDatepicker"
                      :disabled-dates="disabledDates"
                      name="FechaNac"
                      data-vv-as="Fecha de la venta"
                      v-validate="'required'"
                      format="yyyy-MM-dd"
                      placeholder="Fecha de la Venta"
                      v-model="form.fecha_nac"
                      class="w-full pb-1 pt-1"
                    ></datepicker>
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('FechaNac') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.fecha_nac"
                      >{{errores.fecha_nac[0]}}</span>
                    </div>
                  </div>

                  <!--vendedor-->
                  <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                    <label class="text-sm opacity-75 font-medium">
                      <span>Venta realizada por:</span>
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <v-select
                      :options="vendedores"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.vendedor"
                      class="pb-1 pt-1"
                      v-validate:plan_de_venta_computed.immediate="'required'"
                      name="plan_venta"
                      data-vv-as="Plan de Venta"
                    >
                      <div slot="no-options">Seleccione un vendedor</div>
                    </v-select>
                    <div>
                      <span class="text-danger text-sm">{{ errors.first('plan_venta') }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.nombre"
                      >{{errores.nombre[0]}}</span>
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
                <label class="text-sm opacity-75">
                  Nombre completo
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="Nombre"
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el nombre del titular"
                  v-model="form.nombre"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('Nombre') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.nombre">{{errores.nombre[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Fecha de Nacimiento
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <datepicker
                  :language="spanishDatepicker"
                  :disabled-dates="disabledDates"
                  name="FechaNac"
                  data-vv-as="Fecha de la venta"
                  v-validate="'required'"
                  format="yyyy-MM-dd"
                  placeholder="Fecha de Nacimiento"
                  v-model="form.fecha_nac"
                  class="w-full pb-1 pt-1"
                ></datepicker>
                <div>
                  <span class="text-danger text-sm">{{ errors.first('FechaNac') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.fecha_nac"
                  >{{errores.fecha_nac[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">
                  Domicilio Completo
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="DomicilioCompleto"
                  data-vv-as="Domicilio Completo"
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el nombre del usuario"
                  v-model="form.domicilio"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('DomicilioCompleto') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.domicilio"
                  >{{errores.domicilio[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">
                  Ciudad
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="Ciudad"
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese la ciudad"
                  v-model="form.ciudad"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('Ciudad') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.ciudad">{{errores.ciudad[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">
                  Estado
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="Estado"
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el estado"
                  v-model="form.estado"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('Estado') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.estado">{{errores.estado[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">Tél. Domicilio</label>
                <vs-input
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el teléfono del domicilio"
                  v-model="form.tel_domicilio"
                />
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">
                  Celular
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="Celular"
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese un número de celular"
                  v-model="form.celular"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('Celular') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.celular"
                  >{{errores.celular[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">Tél. Oficina</label>
                <vs-input
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese un teléfono de oficina"
                  v-model="form.tel_oficina"
                />
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">RFC</label>
                <vs-input
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
                <label class="text-sm opacity-75">Email</label>
                <vs-input
                  type="email"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el email"
                  v-model="form.email"
                />
              </div>

              <!--fin de datos del titular-->

              <vs-divider />
            </div>
            <!--fin de datos del titular y beneficiarios-->

            <div class="w-full pt-3 pb-3 px-2">
              <h3 class="text-xl">
                <feather-icon icon="UsersIcon" class="mr-2" svgClasses="w-5 h-5" />Información de los Beneficiarios
              </h3>
            </div>
            <div v-if="form.beneficiarios.nombre">
            <div
              :key="index"
              v-for="(beneficiario, index) in form.beneficiarios"
              class="flex flex-wrap px-2"
            >
              <!--datos de los beneficiarios-->

              <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
                <label class="text-sm opacity-75">
                  Nombre completo
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="Nombre"
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el nombre del titular"
                  v-model="beneficiario.nombre"
                />
                <div class="pb-2">
                  <span class="text-danger text-sm">{{ errors.first('Nombre') }}</span>
                </div>

                <div :key="indexerror" v-for="(error, indexerror) in errores">
                  <div v-if="error['error'][indexerror+'.meses']">
                    <span class="text-danger text-sm">{{error['error'][indexerror+'.meses'][0]}}</span>
                  </div>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75">
                  Parentesco
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="Nombre"
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el nombre del titular"
                  v-model="beneficiario.parentesco"
                />
                <div class="pb-2">
                  <span class="text-danger text-sm">{{ errors.first('Nombre') }}</span>
                </div>
                <div :key="indexerror" v-for="(error, indexerror) in errores">
                  <div v-if="error['error'][indexerror+'.meses']">
                    <span class="text-danger text-sm">{{error['error'][indexerror+'.meses'][0]}}</span>
                  </div>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75">
                  Teléfono
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="Nombre"
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el nombre del titular"
                  v-model="beneficiario.telefono"
                />
                <div class="pb-2">
                  <span class="text-danger text-sm">{{ errors.first('Nombre') }}</span>
                </div>
                <div :key="indexerror" v-for="(error, indexerror) in errores">
                  <div v-if="error['error'][indexerror+'.meses']">
                    <span class="text-danger text-sm">{{error['error'][indexerror+'.meses'][0]}}</span>
                  </div>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-1/12 lg:w-1/12 xl:w-1/12 px-2">
                <vs-button
                  class="mt-8 float-right"
                  type="flat"
                  color="danger"
                  size="small"
                  icon="remove_circle_outline"
                >Quitar</vs-button>
              </div>
              <!--fin de datos de los beneficiarios-->
            </div>
            </div>

     
             <div v-else class="  pr-3 pl-3 text-white bg-danger">
              No se ha seleccionado ningún área
               </div>

            <div class="flex flex-wrap pt-4 px-2">
              <div class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2">
                <div class="mt-5">
                  <p class="text-base">
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
  </div>
</template>
<script>
import Mapa from "../ventas/Mapa";
//componente de password
import Password from "../../../confirmar_password";
import cementerio from "@services/cementerio";
import usuarios from "@services/Usuarios";
import vSelect from "vue-select";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";

/**VARIABLES GLOBALES */
import { alfabeto } from "@/VariablesGlobales";

export default {
  components: {
    "v-select": vSelect,
    Password,
    Datepicker,
    Mapa
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
      }
    },

    //aqui obtengo los datos necesarios para poder saber cuantas filas tiene una propiedad
    "form.filas.value": function(newValue, oldValue) {
      if (newValue != "") {
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
              this.form.lotes = { label: "Seleccione 1", value: "" };
              return;
            }
          });
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
        //creo uniplex, duplex, triplex y cuadruplex sin nicho
        this.filas = [];
        this.filas.push({ label: "Seleccione 1", value: "" });
        if (this.datosAreas.tipo_propiedades_id != 4) {
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
          this.form.filas = { label: "Seleccione 1", value: "" };
        } else {
          //filas para las terrazas
          for (let index = 1; index <= this.datosAreas.filas; index++) {
            this.filas.push({
              label: "Fila - " + this.alfabeto[index - 1],
              value: index
            });
          }
          this.form.filas = { label: "Seleccione 1", value: "" };
        }
        //cargo los precios
        this.cargarPlanes();
      }
    },

    "form.venta_referencia_id": function(newValue, oldValue) {
      this.cargarPlanes();
    },

    "form.planVenta": function(newValue, oldValue) {
      //actualizo el precio actual de la propiedad para mostrar el total neto
      this.form.precio_neto = newValue.precio_neto;
    }

    //fin de watchs con mapa
  },
  computed: {
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
        return true;
      } else {
        return false;
      }
    },

    ubicacion: function() {
      //se va creando la ubicacion
      return (
        this.datosAreas.tipo_propiedades_id +
        "-" +
        this.datosAreas.id +
        "-" +
        this.form.filas.value +
        "-" +
        this.form.lotes.value
      );
    },
    //valido que elija un plan de venta
    plan_de_venta_computed: function() {
      return this.form.planVenta.value;
    }
  },
  data() {
    return {
      alfabeto: alfabeto,
      disabledDates: {
        from: new Date()
      },
      spanishDatepicker: es,
      operConfirmar: false,
      callback: Function,
      accionNombre: "agregar nuevo usuario",
      ventasReferencias: [],
      filas: [],
      lotes: [],
      vendedores: [],
      //variables con mapa
      planesVenta: [
        {
          label: "Seleccione 1",
          value: "",
          precio_neto: ""
        }
      ],
      datosAreas: [],
      //fin var con mapa
      form: {
        ventaAntesdelSistema: false,
        //datos del titular
        nombre: "",
        a_paterno: "",
        a_materno: "",
        domicilio: "",
        ciudad: "",
        estado: "",
        tel_domicilio: "",
        celular: "",
        tel_oficina: "",
        rfc: "",
        email: "",
        fecha_nac: "",
        //
        tipo_venta_id: "",
        num_solicitud: "",
        convenio: "",
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
          precio_neto: ""
        },
        precio_neto: "",
        descuento: "",
        vendedor: {
          label: "Seleccione 1",
          value: ""
        },
        beneficiarios: []
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
    },
    acceptAlert() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            return;
          } else {
            //se confirma la cntraseña
            this.callback = this.saveUsuario;
            this.operConfirmar = true;
          }
        })
        .catch(() => {});
    },
    cancel() {
      this.$emit("closeVentana");
    },
    get_ventas_referencias_propiedades() {
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
        })
        .catch(err => {});
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
        })
        .catch(err => {});
    },

    cargarPlanes() {
      this.planesVenta = [];
      this.planesVenta.push({
        label: "Seleccione 1",
        value: "",
        precio_neto: ""
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
                value: element.meses,
                precio_neto: element.precio_neto
              });
            }
          } else {
            //la venta es a futuro y puede seleccionar todas los siguientes planes de venta
            /**AGREGO LOS DEMAS ROLES */
            if (element.tipo_precios_id == 1) {
              //precio de contado a 0 meses 1 solo pago
              this.planesVenta.push({
                label: "Pago de contado",
                value: element.meses,
                precio_neto: element.precio_neto
              });
            } else {
              //precios de pagos a meses
              this.planesVenta.push({
                label: element.meses + " Meses",
                value: element.meses,
                precio_neto: element.precio_neto
              });
            }
          }
        });

        //selecciono el primero precio automaticamente
        this.form.planVenta = this.planesVenta[1];
      }
    },

    closeChecker() {
      this.operConfirmar = false;
    },

    //agregar beneficiario
    agregar_beneficiario() {
      //verifico si todos los datos estan completos para dejarle agregar nuevos
      let errores_de_captura_en_datos = 0;
      if (this.form.beneficiarios.length < 5) {
        /*this.tipo_propiedades[index].precios.forEach(element => {
        if (
          element.meses === "" ||
          element.precio_neto === "" ||
          element.enganche_inicial === ""
        ) {
          errores_de_captura_en_datos += 1;
        }
      });*/
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
          title: "Error",
          text: "Ha llegado al límite de beneficiarios",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "top-center",
          time: "4000"
        });
      }
    }
  },
  created() {
    this.get_vendedores();
  }
};
</script>