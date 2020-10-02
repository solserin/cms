<template>
  <div class="centerx">
    <vs-popup
      class="normal-forms venta-propiedades background-header-forms"
      fullscreen
      close="cancelar"
      :title="
        getTipoformulario == 'modificar'
          ? 'Modificar Venta de Propiedades del Cementerio'
          : 'Registrar Venta de Propiedades del Cementerio'
      "
      :active.sync="showVentana"
      ref="formulario"
    >
      <!--inicio venta-->
      <div class="venta-details">
        <div class="flex flex-wrap">
          <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 mt-5">
            <!--mapa del cementerio-->
            <div mt-5>
              <Mapa
                :disabled="
                  tiene_pagos_realizados || ventaLiquidada || fueCancelada
                "
                :idAreaInicial="idAreaInicial"
                @getDatosTipoPropiedad="getDatosTipoPropiedad"
                @respuestaDeshabilitado="respuestaDeshabilitado"
              ></Mapa>
            </div>
            <!--fin del mapa del cementerio-->
          </div>
          <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
            <div class="float-left pb-5 px-2">
              <img width="36px" src="@assets/images/location.svg" />
              <h3
                class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms"
              >Información de la ubicación y tipo de venta</h3>
            </div>

            <div class="w-full px-2">
              <vs-divider />
            </div>

            <div class="flex flex-wrap mt-1">
              <div class="w-full pb-4">
                <div class="flex flex-wrap mt-1" v-if="this.datosAreas.tipo_propiedades_id">
                  <h3 class="mt-2 text-xl px-2 py-1 bg-primary text-white">
                    Área del cementerio seleccionada
                    <span
                      class="uppercase"
                    >"{{ this.datosAreas.nombre_area }}"</span>
                  </h3>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  <span v-if="this.datosAreas.tipo_propiedades_id">
                    <span v-if="this.datosAreas.tipo_propiedades_id == 4">Fila</span>
                    <span v-else>Ubicación</span>
                  </span>
                  <span v-else>Seleccione un Área</span>
                  <span class="texto-importante">(*)</span>
                </label>
                <v-select
                  :disabled="
                    tiene_pagos_realizados || ventaLiquidada || fueCancelada
                  "
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
                  <span class="mensaje-requerido">{{ errors.first("fila_validacion") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores['filas.value']"
                  >{{ errores["filas.value"][0] }}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  <span v-if="this.datosAreas.tipo_propiedades_id == 4">Ubicación</span>
                  <span v-else>No Aplica</span>
                  <span class="texto-importante">(*)</span>
                </label>
                <v-select
                  :options="lotes"
                  :clearable="false"
                  :dir="$vs.rtl ? 'rtl' : 'ltr'"
                  v-model="form.lotes"
                  class="mb-4 sm:mb-0 pb-1 pt-1"
                  :disabled="
                    this.datosAreas.tipo_propiedades_id != 4 ||
                    tiene_pagos_realizados ||
                    ventaLiquidada ||
                    fueCancelada
                  "
                  v-validate:ubicacion_validacion_computed.immediate="
                    'required'
                  "
                  name="ubicacion_validacion"
                  data-vv-as=" "
                >
                  <div slot="no-options">Seleccione 1 Área</div>
                </v-select>
                <div>
                  <span class="mensaje-requerido">
                    {{
                    errors.first("ubicacion_validacion")
                    }}
                  </span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores['lotes.value']"
                  >{{ errores["lotes.value"][0] }}</span>
                </div>
              </div>
            </div>
            <vs-divider />
            <div class="flex flex-wrap mt-1">
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  <span>Tipo de Venta</span>
                  <span class="texto-importante">(*)</span>
                </label>
                <v-select
                  :disabled="ModificarVenta"
                  :options="ventasAntiguedad"
                  :clearable="false"
                  :dir="$vs.rtl ? 'rtl' : 'ltr'"
                  v-model="form.ventaAntiguedad"
                  class="mb-4 sm:mb-0 pb-1 pt-1"
                  v-validate:antiguedad_validacion_computed.immediate="
                    'required'
                  "
                  name="antiguedad_validacion"
                  data-vv-as=" "
                >
                  <div slot="no-options">Seleccione 1</div>
                </v-select>
                <div>
                  <span class="mensaje-requerido">
                    {{
                    errors.first("antiguedad_validacion")
                    }}
                  </span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores['ventaAntiguedad.value']"
                  >{{ errores["ventaAntiguedad.value"][0] }}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm font-bold">Tipo de Financiamiento</label>
                <div class="mt-3">
                  <vs-radio
                    vs-name="tipoFinanciamiento"
                    v-model="form.tipo_financiamiento"
                    :vs-value="1"
                    class="mr-4"
                    :disabled="
                      tiene_pagos_realizados || ventaLiquidada || fueCancelada
                    "
                  >Uso inmediato</vs-radio>
                  <vs-radio
                    vs-name="tipoFinanciamiento"
                    v-model="form.tipo_financiamiento"
                    :vs-value="2"
                    class="mr-4"
                    :disabled="
                      tiene_pagos_realizados || ventaLiquidada || fueCancelada
                    "
                  >A futuro</vs-radio>
                </div>
              </div>
              <div class="w-full px-2 mt-2">
                <p class="text-xs">
                  <span class="text-danger font-medium">Ojo:</span>
                  Debe seleccionar la modalidad de la venta que se esté
                  registrando en caso de que haya sido realizada fuera del
                  control del sistema, ya que ese tipo de ventas cuenta con un
                  control especial de números de órden.
                </p>
              </div>
            </div>
            <vs-divider />
            <div class="flex flex-wrap mt-1">
              <div class="float-left pb-3 px-2">
                <img width="36px" src="@assets/images/order.svg" />
                <h3
                  class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
                >Información del cliente y control de venta</h3>
              </div>

              <!--nombre del cliente-->
              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
                <div class="flex flex-wrap">
                  <div class="w-full px-2">
                    <label class="text-sm opacity-75 font-bold">
                      Seleccione un Cliente
                      <span class="texto-importante">(*)</span>
                    </label>
                  </div>
                  <div class="w-full sm:w-3/12 md:w-1/12 lg:w-1/12 xl:w-1/12 px-2">
                    <div v-if="fueCancelada != true">
                      <img
                        v-if="form.id_cliente == ''"
                        width="46px"
                        class="cursor-pointer p-2 mt-2"
                        src="@assets/images/search.svg"
                        @click="openBuscador = true"
                        title="Buscar Cliente"
                      />
                      <img
                        v-else
                        width="46px"
                        class="cursor-pointer p-2 mt-2"
                        src="@assets/images/minus.svg"
                        @click="quitarCliente()"
                      />
                    </div>
                    <div v-else>
                      <img
                        width="46px"
                        class="cursor-pointer p-2 mt-2"
                        src="@assets/images/minus.svg"
                      />
                    </div>
                  </div>

                  <div class="w-full sm:w-9/12 md:w-11/12 lg:w-11/12 xl:w-11/12 px-2">
                    <vs-input
                      size="large"
                      readonly
                      v-validate="'required'"
                      name="id_cliente"
                      data-vv-as=" "
                      type="text"
                      class="w-full py-1 cursor-pointer texto-bold"
                      placeholder="DEBE SELECCIONAR UN CLIENTE PARA REALIZAR LA VENTA."
                      v-model="form.cliente"
                      maxlength="100"
                      ref="cliente_ref"
                    />
                    <div>
                      <span class="mensaje-requerido">{{ errors.first("id_cliente") }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="mensaje-requerido"
                        v-if="this.errores.id_cliente"
                      >{{ errores.id_cliente[0] }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <!--fin nombre del cliente-->
              <!--vendedor-->
              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 pt-2">
                <div class="flex flex-wrap">
                  <div class="w-full px-2">
                    <label class="text-sm opacity-75 font-bold">
                      <span>Seleccione 1 vendedor:</span>
                      <span class="texto-importante">(*)</span>
                    </label>
                  </div>
                  <div class="w-full sm:w-3/12 md:w-1/12 lg:w-1/12 xl:w-1/12 px-2">
                    <img
                      width="46px"
                      class="p-2 mt-2"
                      src="@assets/images/businessman.svg"
                      title="Seleccione 1 Vendedor"
                    />
                  </div>
                  <div class="w-full sm:w-9/12 md:w-11/12 lg:w-11/12 xl:w-11/12">
                    <v-select
                      :options="vendedores"
                      :clearable="false"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      v-model="form.vendedor"
                      class="pb-1 pt-1 large_select"
                      v-validate:vendedor_validacion_computed.immediate="
                        'required'
                      "
                      name="vendedor"
                      data-vv-as=" "
                      :disabled="fueCancelada"
                    >
                      <div slot="no-options">Seleccione un vendedor</div>
                    </v-select>
                    <div>
                      <span class="mensaje-requerido">{{ errors.first("vendedor") }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="mensaje-requerido"
                        v-if="this.errores['vendedor.value']"
                      >{{ errores["vendedor.value"][0] }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <!--Fin de vendedor-->

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 pt-4">
                <label class="text-sm opacity-75 font-bold">
                  Fecha de la Venta (Año-Mes-Dia)
                  <span class="texto-importante">(*)</span>
                </label>

                <flat-pickr
                  :disabled="
                    tiene_pagos_realizados || ventaLiquidada || fueCancelada
                  "
                  name="fecha_venta"
                  data-vv-as=" "
                  v-validate:fecha_venta_validacion_computed.immediate="
                    'required'
                  "
                  :config="configdateTimePicker"
                  v-model="form.fecha_venta"
                  placeholder="Fecha de la Venta"
                  class="w-full my-1"
                />

                <div>
                  <span class="mensaje-requerido">{{ errors.first("fecha_venta") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.fecha_venta"
                  >{{ errores.fecha_venta[0] }}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 pt-4">
                <label class="text-sm opacity-75 font-bold">
                  Núm. Solicitud
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  v-validate:solicitud_validacion_computed.immediate="
                    'required'
                  "
                  name="solicitud"
                  data-vv-as=" "
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder=" Núm. Solicitud"
                  v-model="form.solicitud"
                  :disabled="tipo_venta || fueCancelada"
                />
                <div>
                  <span class="mensaje-requerido">{{ errors.first("solicitud") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.solicitud"
                  >{{ errores.solicitud[0] }}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Núm. Convenio
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  v-validate:num_convenio_validacion_computed.immediate="
                    'required'
                  "
                  name="num_convenio"
                  data-vv-as=" "
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Núm. Convenio"
                  v-model="form.convenio"
                  :disabled="!capturar_num_convenio || fueCancelada"
                />
                <div>
                  <span class="mensaje-requerido">{{ errors.first("num_convenio") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.convenio"
                  >{{ errores.convenio[0] }}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Núm. Título
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  v-validate:num_titulo_validacion_computed.immediate="
                    'required'
                  "
                  name="num_titulo"
                  data-vv-as=" "
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Núm. Título"
                  v-model="form.titulo"
                  :disabled="
                    !(tipo_venta * capturar_num_titulo + capturar_num_titulo) ||
                    fueCancelada
                  "
                />
                <div>
                  <span class="mensaje-requerido">{{ errors.first("num_titulo") }}</span>
                </div>
                <div class="mt-2">
                  <span class="mensaje-requerido" v-if="this.errores.titulo">{{ errores.titulo[0] }}</span>
                </div>
              </div>

              <vs-divider />
            </div>
          </div>
        </div>
        <!--datos del titular y beneficiarios-->
        <div class="flex flex-wrap px-2">
          <div class="w-full pt-3 pb-3 px-2">
            <div class="float-left">
              <img width="36px" src="@assets/images/sustituto.svg" />
              <h3
                class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
              >Titular Sustituto del Contrato</h3>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              Nombre del titular sustituto
              <span class="texto-importante">(*)</span>
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
              :disabled="fueCancelada"
            />
            <div>
              <span class="mensaje-requerido">{{ errors.first("titular_sustituto") }}</span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores.titular_sustituto"
              >{{ errores.titular_sustituto[0] }}</span>
            </div>
          </div>

          <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              Parentesco con el titular sustituto
              <span class="texto-importante">(*)</span>
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
              :disabled="fueCancelada"
            />
            <div>
              <span class="mensaje-requerido">
                {{
                errors.first("parentesco_titular_sustituto")
                }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores.parentesco_titular_sustituto"
              >{{ errores.parentesco_titular_sustituto[0] }}</span>
            </div>
          </div>

          <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              Teléfono de contacto
              <span class="texto-importante">(*)</span>
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
              :disabled="fueCancelada"
            />
            <div>
              <span class="mensaje-requerido">{{ errors.first("telefono_titular_sustituto") }}</span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores.telefono_titular_sustituto"
              >{{ errores.telefono_titular_sustituto[0] }}</span>
            </div>
          </div>

          <vs-divider />
        </div>
        <!--fin de datos del titular y beneficiarios-->
        <div class="flex flex-wrap px-2">
          <div class="w-full pt-3 pb-3 px-2">
            <div class="float-left">
              <img width="36px" src="@assets/images/beneficiarios.svg" />
              <h3
                class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
              >Lista de beneficiarios del contrato</h3>
            </div>
          </div>
        </div>

        <div v-if="form.beneficiarios.length > 0">
          <div
            :key="index"
            v-for="(beneficiario, index) in form.beneficiarios"
            class="flex flex-wrap px-2"
          >
            <!--datos de los beneficiarios-->

            <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
              <label class="text-sm opacity-75">
                <span class="font-bold">Beneficiario {{ index + 1 }} - Nombre completo</span>
                <span class="texto-importante">(*)</span>
              </label>
              <vs-input
                :name="'beneficiario' + index"
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
                <span class="mensaje-requerido">{{ errors.first("beneficiario" + index) }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="mensaje-requerido"
                  v-if="errores['beneficiarios.' + index + '.nombre']"
                >{{ errores["beneficiarios." + index + ".nombre"][0] }}</span>
              </div>
            </div>
            <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
              <label class="text-sm opacity-75">
                <span class="font-bold">Beneficiario {{ index + 1 }} - Parentesco</span>
                <span class="texto-importante">(*)</span>
              </label>
              <vs-input
                :name="'parentesco' + index"
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
                <span class="mensaje-requerido">{{ errors.first("parentesco" + index) }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="mensaje-requerido"
                  v-if="errores['beneficiarios.' + index + '.parentesco']"
                >
                  {{
                  errores["beneficiarios." + index + ".parentesco"][0]
                  }}
                </span>
              </div>
            </div>
            <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
              <label class="text-sm opacity-75">
                <span class="font-bold">Beneficiario {{ index + 1 }} - Teléfono</span>
                <span class="texto-importante">(*)</span>
              </label>
              <vs-input
                :name="'telefono' + index"
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
                <span class="mensaje-requerido">{{ errors.first("telefono" + index) }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="mensaje-requerido"
                  v-if="errores['beneficiarios.' + index + '.telefono']"
                >
                  {{
                  errores["beneficiarios." + index + ".telefono"][0]
                  }}
                </span>
              </div>
            </div>
            <div class="w-full sm:w-12/12 md:w-1/12 lg:w-1/12 xl:w-1/12 px-2">
              <div
                class="mt-10 float-right"
                @click="remover_beneficiario(index)"
                v-if="!fueCancelada"
              >
                <img class="cursor-pointer img-btn" src="@assets/images/minus.svg" />
                <span class="text-danger font-medium float-right pl-3 cursor-pointer">Remover</span>
              </div>
            </div>
            <!--fin de datos de los beneficiarios-->
          </div>
        </div>

        <div v-else class="w-full flex flex-wrap pt-4 px-2">
          <div class="flex flex-wrap">
            <div class="w-full px-2">
              <div class="float-left">
                <img width="26px" src="@assets/images/warning.svg" />
                <h3
                  class="float-right mt-2 ml-3 text-base text-danger font-medium"
                >Advertencia, no ha capturado beneficiarios aún.</h3>
              </div>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap pt-4 px-2">
          <div class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2">
            <div class="mt-5">
              <p class="text-sm">
                <span class="text-danger font-medium">Ojo:</span>
                En este apartado puede agregar cada uno de los beneficiarios que
                tenga derecho el titular de este contrato.
              </p>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
            <div class="mt-8 float-right" @click="agregar_beneficiario()" v-if="!fueCancelada">
              <span
                class="text-white font-medium px-2 py-1 bg-success cursor-pointer"
              >Agregar beneficiario</span>
              <img class="cursor-pointer img-btn float-right ml-3" src="@assets/images/plus.svg" />
            </div>
          </div>
        </div>
        <vs-divider />

        <!--checkout-->
        <div class="flex flex-wrap my-6">
          <div class="w-full sm:w-12/12 md:w-6/12 px-2">
            <div class="flex flex-wrap">
              <div class="w-full pt-3 pb-3 px-2">
                <div class="float-left">
                  <img width="36px" src="@assets/images/summary.svg" />
                  <h3
                    class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
                  >Información resumida de la venta</h3>
                </div>
              </div>
            </div>
            <!--resumen de la venta-->
            <div class="flex flex-wrap mt-6 dark-text font-medium">
              <div class="w-full sm:w-12/12 ml-auto md:w-12/12 lg:w-12/12 xl:w-12/12">
                <div class="flex flex-wrap">
                  <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                    <span class="text-gray-100 font-bold">Nombre del Titular</span>
                  </div>
                  <div
                    class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right text-gray-900 font-medium"
                  >
                    <span v-if="this.form.id_cliente == ''">
                      <span class="text-danger">Seleccione un cliente para esta venta</span>
                    </span>
                    <span v-else class="uppercase font-bold">{{ form.cliente }}</span>
                  </div>
                </div>
                <vs-divider />
                <div class="flex flex-wrap">
                  <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                    <span class="text-gray-100 font-bold">Ubicación</span>
                  </div>
                  <div
                    class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right text-gray-900 font-medium"
                  >
                    <span v-if="this.datosAreas.id">
                      <span
                        v-if="
                          this.datosAreas.tipo_propiedades_id == 1 ||
                          this.datosAreas.tipo_propiedades_id == 2 ||
                          this.datosAreas.tipo_propiedades_id == 3 ||
                          this.datosAreas.tipo_propiedades_id == 5 ||
                          this.datosAreas.tipo_propiedades_id == 6
                        "
                      >
                        <span v-if="this.form.filas.value != ''">
                          Propiedad
                          {{
                          this.datosAreas["tipo_propiedad"].tipo +
                          " Ubicación " +
                          this.form.filas.label
                          }}
                        </span>
                        <span v-else class="text-danger">Seleccione una ubicación</span>
                      </span>
                      <span v-else>
                        <span
                          v-if="
                            this.form.filas.value != '' &&
                            this.form.lotes.value != ''
                          "
                        >
                          Propiedad
                          {{
                          this.datosAreas["nombre_area"] +
                          " Ubicación " +
                          this.form.lotes.label
                          }}
                        </span>
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
                    <span v-if="this.form.vendedor.value != ''">{{ this.form.vendedor.label }}</span>
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
                    <span v-if="this.form.tipo_financiamiento == 1">Uso inmediato</span>
                    <span v-else>A futuro</span>
                  </div>
                </div>
                <vs-divider />
                <div class="flex flex-wrap hidden">
                  <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                    <span class="text-gray-100 font-bold">Plan de Venta</span>
                  </div>
                  <div
                    class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right text-gray-900 font-medium"
                  >
                    <span v-if="this.form.planVenta.value > 0">
                      <span v-if="this.form.planVenta.value == 1">
                        Pago Único de ${{
                        this.costo_neto_computed | numFormat("0,000.00")
                        }}
                        Pesos
                      </span>
                      <span v-else>
                        Pago Inicial de ${{
                        this.form.pago_inicial | numFormat("0,000.00")
                        }}
                        Pesos. Más
                        {{ this.form.planVenta.value }}
                        Mensualidades de ${{
                        ((this.costo_neto_computed - this.form.pago_inicial) /
                        this.form.planVenta.value)
                        | numFormat("0,000.00")
                        }}
                        Pesos Aprox.
                      </span>
                    </span>
                    <span v-else class="text-danger">Seleccione un Plan de Venta</span>
                  </div>
                </div>
                <vs-divider class="hidden" />
                <div class="flex flex-wrap">
                  <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                    <span class="text-gray-100 font-bold">Sub Total</span>
                  </div>
                  <div
                    class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right text-gray-900 font-medium"
                  >
                    <span>
                      $
                      {{ this.form.subtotal | numFormat("0,000.00") }}
                      Pesos
                    </span>
                  </div>
                </div>
                <vs-divider />
                <div class="flex flex-wrap">
                  <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                    <span class="text-gray-100 font-bold">Descuento</span>
                  </div>
                  <div
                    class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right text-gray-900 font-medium"
                  >
                    <span>
                      $
                      {{ this.form.descuento | numFormat("0,000.00") }}
                      Pesos
                    </span>
                  </div>
                </div>
                <vs-divider />
                <div class="flex flex-wrap">
                  <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                    <span class="text-gray-100 font-bold">IVA</span>
                  </div>
                  <div
                    class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right text-gray-900 font-medium"
                  >
                    <span>
                      $
                      {{ iva_computed | numFormat("0,000.00") }}
                      Pesos
                    </span>
                  </div>
                </div>
                <vs-divider />
                <div class="flex flex-wrap">
                  <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                    <span class="text-gray-100 font-bold">Total a Pagar</span>
                  </div>
                  <div
                    class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2 text-right text-gray-900 font-medium"
                  >
                    <span>
                      $
                      {{ costo_neto_computed | numFormat("0,000.00") }}
                      Pesos
                    </span>
                  </div>
                </div>
                <vs-divider />
                <div class="w-full">
                  <h3
                    class="mt-2 text-base px-2 py-1 bg-seccion-forms"
                    style="line-height: 1.6em;"
                    hidden
                  >
                    Se recomienda revisar la Información capturada antes de
                    mandar
                    <span
                      class="text-danger"
                    >Guardar la venta</span>, si ya
                    revisó que todo está correcto puede proceder.
                  </h3>

                  <h3
                    class="mt-2 text-base px-2 py-1 bg-seccion-forms mb-1"
                    style="line-height: 1.6em;"
                  >Nota/Comentario:</h3>
                  <span class="px-2">{{ form.nota }}</span>
                </div>

                <vs-divider />
              </div>
            </div>
            <!--fin del resumen de la venta-->
          </div>
          <div class="w-full sm:w-12/12 md:w-6/12 px-2">
            <div class="flex flex-wrap">
              <div class="w-full pt-3 pb-3 px-2">
                <div class="float-left">
                  <img width="36px" src="@assets/images/payments.svg" />
                  <h3
                    class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
                  >Programación de pagos</h3>
                </div>
              </div>
            </div>
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold hidden">
                  <span v-if="form.tipo_financiamiento == 1">Pago Único</span>
                  <span v-if="form.tipo_financiamiento == 2">A Futuro/A Meses</span>
                  <span class="texto-importante">(*)</span>
                </label>
                <label class="text-sm opacity-75 font-bold">
                  <span>Número de Pagos</span>
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  :disabled="
                    tiene_pagos_realizados || ventaLiquidada || fueCancelada
                  "
                  size="large"
                  v-validate.disabled="
                    'required|integer|min_value:' +
                    minimo_financiamiento +
                    '|max_value:' +
                    maximo_financiamiento
                  "
                  name="financiamiento"
                  data-vv-as=" "
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Número de pagos"
                  v-model="form.financiamiento"
                  maxlength="3"
                />
                <div>
                  <span class="mensaje-requerido">{{ errors.first("financiamiento") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.financiamiento"
                  >{{ errores.financiamiento[0] }}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Salarios Mínimos x Mantenimiento
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  size="large"
                  v-validate="'required|integer|min_value:1|max_value:150'"
                  name="salarios_minimos"
                  data-vv-as=" "
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el número de salarios"
                  v-model="form.salarios_minimos"
                  maxlength="3"
                />
                <div>
                  <span class="mensaje-requerido">{{ errors.first("salarios_minimos") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.salarios_minimos"
                  >{{ errores.salarios_minimos[0] }}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  $ Sub Total
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  :disabled="
                    tiene_pagos_realizados || ventaLiquidada || fueCancelada
                  "
                  size="large"
                  name="subtotal"
                  data-vv-as=" "
                  v-validate="'required|decimal:2|min_value:1'"
                  type="text"
                  class="w-full pb-1 pt-1 texto-bold"
                  placeholder="$ 0.00"
                  v-model="form.subtotal"
                />
                <div>
                  <span class="mensaje-requerido">{{ errors.first("subtotal") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.subtotal"
                  >{{ errores.subtotal[0] }}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  $ Descuento
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  :disabled="
                    tiene_pagos_realizados || ventaLiquidada || fueCancelada
                  "
                  size="large"
                  name="descuento"
                  data-vv-as=" "
                  v-validate="
                    'required|decimal:2|min_value:0|max_value:' +
                    this.form.subtotal
                  "
                  type="text"
                  class="w-full pb-1 pt-1 texto-bold"
                  placeholder="$ 0.00"
                  v-model="form.descuento"
                />

                <div>
                  <span class="mensaje-requerido">{{ errors.first("descuento") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.descuento"
                  >{{ errores.descuento[0] }}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Tasa IVA %
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  :disabled="
                    tiene_pagos_realizados || ventaLiquidada || fueCancelada
                  "
                  size="large"
                  name="tasa_iva"
                  data-vv-as=" "
                  v-validate="'required|decimal:2|min_value:0|max_value:25'"
                  type="text"
                  class="w-full pb-1 pt-1 texto-bold"
                  placeholder="Porcentaje IVA"
                  v-model="form.tasa_iva"
                  maxlength="2"
                />
                <div>
                  <span class="mensaje-requerido">{{ errors.first("tasa_iva") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.tasa_iva"
                  >{{ errores.tasa_iva[0] }}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  $ IVA
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  size="large"
                  name="impuestos"
                  data-vv-as=" "
                  v-validate="'required|decimal:2|min_value:0'"
                  type="text"
                  class="w-full pb-1 pt-1 texto-bold"
                  placeholder
                  v-model="iva_computed"
                  :disabled="true"
                />

                <div>
                  <span class="mensaje-requerido">{{ errors.first("impuestos") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.impuestos"
                  >{{ errores.impuestos[0] }}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  $ Total a Pagar
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  size="large"
                  name="costo_neto"
                  data-vv-as=" "
                  v-validate="'required|decimal:2|min_value:0'"
                  type="text"
                  class="w-full pb-1 pt-1 texto-bold"
                  v-model="costo_neto_computed"
                  :disabled="true"
                  readonly
                />

                <div>
                  <span class="mensaje-requerido">{{ errors.first("costo_neto") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.costo_neto"
                  >{{ errores.costo_neto[0] }}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  $ Pago Inicial
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  :disabled="
                    tiene_pagos_realizados || ventaLiquidada || fueCancelada
                  "
                  size="large"
                  name="pago_inicial"
                  data-vv-as=" "
                  v-validate="
                    'required|decimal:2|min_value:' +
                    this.valor_minimo_pago_inicial +
                    '|max_value:' +
                    this.valor_maximo_pago_inicial
                  "
                  maxlength="10"
                  type="text"
                  class="w-full pb-1 pt-1 texto-bold"
                  v-model="form.pago_inicial"
                  placeholder="$ 0.00"
                />

                <div>
                  <span class="mensaje-requerido">{{ errors.first("pago_inicial") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.pago_inicial"
                  >{{ errores.pago_inicial[0] }}</span>
                </div>
                <div class="mt-2"></div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  $ Costo Neto Pronto Pago
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  :disabled="
                    tiene_pagos_realizados || ventaLiquidada || fueCancelada
                  "
                  size="large"
                  name="costo_neto_pronto_pago"
                  data-vv-as=" "
                  v-validate="
                    'required|decimal:2|min_value:' +
                    minimo_pronto_pago +
                    '|max_value:' +
                    costo_neto_computed
                  "
                  maxlength="10"
                  type="text"
                  class="w-full pb-1 pt-1 texto-bold"
                  v-model="form.costo_neto_pronto_pago"
                  placeholder="$ 0.00"
                />

                <div>
                  <span class="mensaje-requerido">
                    {{
                    errors.first("costo_neto_pronto_pago")
                    }}
                  </span>
                </div>
                <div class="mt-2">
                  <span
                    class="mensaje-requerido"
                    v-if="this.errores.costo_neto_pronto_pago"
                  >{{ errores.costo_neto_pronto_pago[0] }}</span>
                </div>
                <div class="mt-2"></div>
              </div>

              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                <div class="flex flex-wrap py-4 mt-5">
                  <div class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2">
                    <div
                      class="float-left"
                      v-if="
                        costo_neto_computed == 0 &&
                        (!isNaN(this.form.subtotal) && this.form.subtotal > 0)
                      "
                    >
                      <h3 class="text-sm text-danger font-medium mt-1">
                        Advertencia, está haciendo un descuento del 100%,
                        verifique si desea continuar.
                      </h3>
                    </div>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                    <div
                      class="float-right cursor-pointer"
                      @click="openNotas = true"
                      v-if="!fueCancelada"
                    >
                      <img width="26px" src="@assets/images/notas_add.svg" />
                      <h3 class="float-right ml-3 mt-1 text-base font-medium">Agregar Nota</h3>
                    </div>
                  </div>
                </div>
                <div class="flex flex-wrap mt-8">
                  <vs-button
                    v-if="!fueCancelada"
                    class="w-full ml-auto mr-auto mt-5"
                    @click="acceptAlert()"
                    color="success"
                    size="small"
                  >
                    <img width="25px" class="cursor-pointer" src="@assets/images/save.svg" />
                    <span class="texto-btn" v-if="this.getTipoformulario == 'agregar'">Guardar Venta</span>
                    <span class="texto-btn" v-else>Modificar Venta</span>
                  </vs-button>
                  <vs-button
                    v-else
                    class="w-full ml-auto mr-auto mt-5"
                    color="success"
                    size="small"
                  >
                    <img width="25px" class="cursor-pointer" src="@assets/images/save.svg" />
                    <span class="texto-btn" v-if="this.getTipoformulario == 'agregar'">Guardar Venta</span>
                    <span class="texto-btn" v-else>Modificar Venta</span>
                  </vs-button>
                </div>
              </div>
              <!--fin de precios-->
            </div>
          </div>
        </div>
        <!--fin del checkout-->
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

    <Notas :nota="form.nota" :show="openNotas" @closeNotas="closeNotas"></Notas>
  </div>
</template>
<script>
/**date picker */
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";

import Mapa from "../Mapa";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import Notas from "@pages/notas";
import cementerio from "@services/cementerio";
import usuarios from "@services/Usuarios";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import ClientesBuscador from "@pages/clientes/searcher.vue";

/**VARIABLES GLOBALES */
import { alfabeto, configdateTimePicker } from "@/VariablesGlobales";

export default {
  components: {
    "v-select": vSelect,
    flatPickr,
    Password,
    Mapa,
    ConfirmarDanger,
    ConfirmarAceptar,
    ClientesBuscador,
    Notas,
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
          await this.get_vendedores();
          if (this.getTipoformulario == "agregar") {
            this.idAreaInicial = 29;
            this.form.tipo_financiamiento = 1;
          } else {
            /**pasando el valor de la venta id */
            this.form.id_venta = this.get_venta_id;
            /**se cargan los datos al formulario */
            await this.consultar_venta_id();
          }
        })();
      } else {
        this.idAreaInicial = 0;
        /**forzando el cambio de area para que se actualice al reabrir la ventana */
        /*if (this.idAreaInicial > 2) {
          this.idAreaInicial -= 1;
        } else {
          this.idAreaInicial += 1;
        }*/
      }
    },

    //aqui obtengo los datos necesarios para poder saber cuantas filas tiene una propiedad
    "form.filas": function (newValue, oldValue) {
      if (newValue.value != "") {
        if (this.datosAreas.tipo_propiedades_id == 4) {
          this.lotes = [];
          this.lotes.push({ label: "Seleccione 1", value: "" });
          this.datosAreas["filas_columnas"].forEach((element) => {
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
                    value: index,
                  });
                }
              }
              //la primero opcion
              this.form.lotes = this.lotes[1];
              return;
            }
          });
          /**revisnado si es form modificar para actualizar el lote seleccionado */
          //la primero opcion
          this.form.lotes = this.lotes[1];
          if (this.getTipoformulario == "modificar") {
            /**cuando el form es para modificar */
            /**buscando la fila que le corresponde segun la propiedad */
            if (this.datosVenta) {
              this.lotes.forEach((element) => {
                if (element.value == this.datosVenta.venta_terreno.lote_raw) {
                  this.form.lotes = element;
                  return;
                }
              });
            }
          }
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
    datosAreas: function (newValue, oldValue) {
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
              value: index,
            });
          }
        } else {
          //filas para las terrazas
          for (let index = 1; index <= this.datosAreas.filas; index++) {
            this.filas.push({
              label: "Fila - " + this.alfabeto[index - 1],
              value: index,
            });
          }
        }
        //la primero opcion
        this.form.filas = this.filas[1];
        if (this.getTipoformulario == "modificar") {
          /**cuando el form es para modificar */
          /**buscando la fila que le corresponde segun la propiedad */
          if (this.datosVenta) {
            this.filas.forEach((element) => {
              if (element.value == this.datosVenta.venta_terreno.fila_raw) {
                this.form.filas = element;
                return;
              }
            });
          }
        }
        //cargo los precios
        if (this.form.tipo_financiamiento != "") {
          /**
          el 09 de agosto 2020 se deshabilita el codigo para cargar precios del catalgo directo
          se va hacer captura d eprecios de manera manual, por solicitud de la empresa
          */
          //this.cargarPlanes();
        }
      }
    },
    "form.tipo_financiamiento": function (newValue, oldValue) {
      if (newValue != "") {
        //this.cargarPlanes();
      }
    },

    /* "form.planVenta": function (newValue, oldValue) {
      if (newValue.value != "") {
        //el plan cambio a un plan especifico
        this.form.subtotal = newValue.subtotal;
        this.form.costo_neto_pronto_pago = newValue.costo_neto_pronto_pago;
      }
    },
*/
    //fin de watchs con mapa
  },
  computed: {
    minimo_financiamiento: function () {
      if (this.form.tipo_financiamiento == 1) {
        return 1;
      } else {
        return 1;
        //return 2;
      }
    },
    maximo_financiamiento: function () {
      if (this.form.tipo_financiamiento == 1) {
        return 1;
      } else {
        return 120;
      }
    },
    /**sacando los valores para aplicar los descuentos respectivos */
    tasa_iva_porcentaje_computed: function () {
      /**calculando el iva */
      let tasa_iva = (Number(this.form.tasa_iva) / 100).toFixed(2);
      return tasa_iva;
    },
    tasa_iva_computed: function () {
      /**calculando el iva */
      let tasa_iva = (Number(this.form.tasa_iva) / 100 + 1).toFixed(2);
      return tasa_iva;
    },
    iva_computed: function () {
      /**calculando el iva */
      let iva = (
        (Number(this.form.subtotal) - Number(this.form.descuento)) *
        this.tasa_iva_porcentaje_computed
      ).toFixed(2);
      if (isNaN(iva)) {
        return 0;
      } else {
        return iva;
      }
    },
    costo_neto_computed: function () {
      /**calculando el iva */
      let costo_neto = (
        (Number(this.form.subtotal) - Number(this.form.descuento)) *
        this.tasa_iva_computed
      ).toFixed(2);
      if (isNaN(costo_neto)) {
        return 0;
      } else {
        return costo_neto;
      }
    },

    /*  costo_neto_computed: function () {
      let costo_neto = (
        Number.parseFloat(this.form.subtotal) -
        Number.parseFloat(this.form.descuento) +
        Number.parseFloat(this.form.impuestos)
      ).toFixed(2);
      this.form.costo_neto = costo_neto;
      return costo_neto;
    },
    */
    /**fin de valores para la aplicacion de toales e impuestos */
    /**minimo valor permitido del enganche */
    valor_minimo_pago_inicial: function () {
      let pago_inicial_minimo = 0;
      if (this.form.financiamiento == 1) {
        pago_inicial_minimo = this.costo_neto_computed;
      } else {
        pago_inicial_minimo = this.costo_neto_computed * 0.1;
      }
      if (isNaN(pago_inicial_minimo)) {
        return 0;
      } else {
        return pago_inicial_minimo;
      }
    },
    /**maximo valor permitido del enganche */
    valor_maximo_pago_inicial: function () {
      let pago_inicial_maximo = 0;
      if (this.form.financiamiento == 1) {
        pago_inicial_maximo = this.costo_neto_computed;
      } else {
        pago_inicial_maximo = this.costo_neto_computed * 0.7;
      }

      if (isNaN(pago_inicial_maximo)) {
        return 0;
      } else {
        return pago_inicial_maximo;
      }
    },

    /**maximo valor permitido del enganche */
    minimo_pronto_pago: function () {
      let minimo_pronto_pago = 0;
      if (this.form.financiamiento == 1) {
        minimo_pronto_pago = this.costo_neto_computed;
      } else {
        minimo_pronto_pago = 0;
      }

      if (isNaN(minimo_pronto_pago)) {
        return 0;
      } else {
        return minimo_pronto_pago;
      }
    },
    /**checando si la venta ya fue liquidada*/
    ventaLiquidada: function () {
      if (this.getTipoformulario == "modificar") {
        if (this.datosVenta.saldo_neto <= 0) {
          return true;
        } else return false;
      } else return false;
    },
    /**checando si ya hay pagos vigentes realizados en la venta, por lo cual no puede cambiar la fecha de la venta */
    tiene_pagos_realizados: function () {
      if (this.getTipoformulario == "modificar") {
        if (this.datosVenta.pagos_realizados > 0) {
          return true;
        } else return false;
      } else return false;
    },
    /**checar si tiene pagos vencidos */
    tienePagosVencidos: function () {
      if (this.getTipoformulario == "modificar") {
        if (this.datosVenta.pagos_vencidos > 0) {
          return true;
        } else return false;
      } else return false;
    },
    fueCancelada: function () {
      if (this.getTipoformulario == "modificar") {
        if (this.datosVenta.operacion_status == 0) {
          return true;
        } else return false;
      } else return false;
    },
    /**validar si es modificar el formulario */
    ModificarVenta: function () {
      if (this.getTipoformulario == "modificar") {
        return true;
      } else return false;
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

    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
    tipo_venta: function () {
      //definiendo el tipo de uso, si es true es venta de uso inmediato si es false es venta de uso futuro
      if (this.form.tipo_financiamiento == 1) {
        //uso inmediato
        return true;
      } else {
        //a futuro
        return false;
      }
    },

    capturar_num_convenio: function () {
      if (this.form.ventaAntiguedad.value > 1) {
        return true;
      } else {
        return false;
      }
    },
    capturar_num_titulo: function () {
      if (this.form.ventaAntiguedad.value == 3) {
        return true;
      } else {
        return false;
      }
    },

    plan_venta: function () {
      if (this.form.planVenta.value == 0) {
        return true;
      } else {
        return false;
      }
    },

    //validaciones calculadas
    //valido que elija un plan de venta
    plan_de_venta_computed: function () {
      return this.form.planVenta.value;
    },
    fila_validacion_computed: function () {
      return this.form.filas.value;
    },
    ubicacion_validacion_computed: function () {
      if (this.form.propiedades_id == 4) {
        //terrazas
        return this.form.lotes.value;
      } else {
        return true;
      }
    },

    antiguedad_validacion_computed: function () {
      return this.form.ventaAntiguedad.value;
    },

    vendedor_validacion_computed: function () {
      return this.form.vendedor.value;
    },
    fecha_venta_validacion_computed: function () {
      return this.form.fecha_venta;
    },

    solicitud_validacion_computed: function () {
      //checo que el dato venta a futuro este activo
      if (this.form.tipo_financiamiento == 2) {
        return this.form.solicitud;
      } else return true;
    },

    num_convenio_validacion_computed: function () {
      //checo que el dato venta a futuro este activo y que sea de venta antes del sistema
      if (this.form.ventaAntiguedad.value >= 2) {
        return this.form.convenio;
      } else return true;
    },
    num_titulo_validacion_computed: function () {
      //checo que el dato venta a futuro este activo
      if (this.form.ventaAntiguedad.value == 3) {
        return this.form.titulo;
      } else return true;
    },

    //fin de validaciones calculadas
    //crear ubicacion
    crear_ubicacion_computed: function () {
      if (this.datosAreas.tipo_propiedades_id == 4) {
        //ubicacion para cuadriplex de terrazas
        //id del tipo de propiedad - id de la propiedad - num fila - num columna
        return (
          this.datosAreas.tipo_propiedades_id +
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
          this.datosAreas.tipo_propiedades_id +
          "-" +
          this.datosAreas.id +
          "-" +
          this.form.filas.value +
          "-" +
          1
        );
      }
    },
    //fin de crear ubicacion
  },
  data() {
    return {
      openNotas: false,
      configdateTimePicker: configdateTimePicker,
      verDisponibilidad: false,
      openBuscador: false,
      idAreaInicial: 1,
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      alfabeto: alfabeto,
      openPassword: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Modificar Venta",
      ventasAntiguedad: [
        {
          label: "NUEVA VENTA",
          value: 1,
        },
        {
          label: "A/S SIN LIQUIDAR",
          value: 2,
        },
        {
          label: "A/S - LIQUIDADA",
          value: 3,
        },
      ],
      filas: [],
      lotes: [],
      vendedores: [],
      //variables con mapa
      planesVenta: [
        {
          label: "Seleccione 1",
          value: "",
          subtotal: "0.00",
          impuestos: "0.00",
          costo_neto: "0.00",
          pago_inicial: "",
          descuento_pronto_pago_b: "",
          costo_neto_pronto_pago: "",
          porcentaje_pronto_pago: "",
          costo_neto_financiamiento_normal: "",
        },
      ],
      /**datos del area seleccionada */
      datosAreas: [],
      /**para modificar, se traen los datos aqui */
      datosVenta: [],
      //fin var con mapa
      form: {
        salarios_minimos: "12",
        id_venta: "",
        /**datos del cliente seleccionado */
        cliente: "seleccione 1 cliente",
        id_cliente: "",
        //ubicacion
        tipo_propiedades_id: 0,
        propiedades_id: 0,
        ubicacion: "",
        //fin de ubicacion
        fecha_venta: "",
        /**titular substituto */
        titular_sustituto: "",
        parentesco_titular_sustituto: "",
        telefono_titular_sustituto: "",
        //
        solicitud: "",
        convenio: "",
        titulo: "",
        tipo_financiamiento: "",
        filas: {
          label: "Seleccione 1",
          value: "",
        },
        lotes: {
          label: "Seleccione 1",
          value: "",
        },

        /**datos origen */
        //variables con mapa
        planVenta: {
          label: "Seleccione 1",
          value: "",
          subtotal: "0.00",
          impuestos: "0.00",
          costo_neto: "0.00",
          pago_inicial: "",
          descuento_pronto_pago_b: "",
          costo_neto_pronto_pago: "",
          porcentaje_pronto_pago: "",
          costo_neto_financiamiento_normal: "",
        },
        ventaAntiguedad: {
          label: "NUEVA VENTA",
          value: 1,
        },
        /**muestra el enganche original con el que se hizo la venta */
        importe: "",
        tasa_iva: 16,
        impuestos: "",
        financiamiento: "",

        pago_inicial_origen: "",
        subtotal: "",
        descuento: "",
        costo_neto: "",
        pago_inicial: "",
        costo_neto_pronto_pago: "",
        vendedor: {
          label: "Seleccione 1",
          value: "",
        },
        beneficiarios: [],
        index_beneficiario: 0,
        nota: "",
        //fin var con mapa
      },
      errores: [],
    };
  },
  methods: {
    closeNotas(nota) {
      this.form.nota = nota;
      this.openNotas = false;
    },
    //obtengo los datos del area seleccionada desde el mapa  "child"
    getDatosTipoPropiedad(datos) {
      //actualizo los datos que se ocupan para manejar las ubicaciones de las propiedades
      this.datosAreas = datos;
      this.form.propiedades_id = this.datosAreas.tipo_propiedades_id;
    },
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
            //una vez todo validado, actualizo los ultimos datos de ubicacion
            this.form.propiedades_id = this.datosAreas.id;
            this.form.tipo_propiedades_id = this.datosAreas.tipo_propiedades_id;
            this.form.ubicacion = this.crear_ubicacion_computed;
            this.form.costo_neto = this.costo_neto_computed;
            this.form.impuestos = this.iva_computed;
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
        let res = await cementerio.guardar_venta(this.form);
        console.log("guardar_venta -> res", res);
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
          console.log("guardar_venta -> err.response", err.response);
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
            if (this.errores.ubicacion) {
              //la propiedad esa ya ha sido vendida
              this.$vs.notify({
                title: "Seleccionar Terreno",
                text: "Este terreno ya ha sido vendido previamente.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000,
              });
            }

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
        let res = await cementerio.modificar_venta(this.form);
        console.log("modificar_venta -> res.data", res.data);
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
          console.log("modificar_venta -> err.response", err.response);
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
    //get vendedores
    async get_vendedores() {
      try {
        let res = await cementerio.get_vendedores();
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

    cargarPlanes() {
      this.form.planVenta = this.planesVenta[0];
      this.planesVenta = [];
      this.planesVenta.push({
        label: "Seleccione 1",
        value: "",
        subtotal: "0.00",
        impuestos: "0.00",
        costo_neto: "0.00",
        pago_inicial: "",
        descuento_pronto_pago_b: "",
        costo_neto_pronto_pago: "",
        porcentaje_pronto_pago: "",
        costo_neto_financiamiento_normal: "",
      });
      if (this.datosAreas.id) {
        this.datosAreas["tipo_propiedad"].precios.forEach((element) => {
          if (element.status == 1) {
            /**cargando solo los activos */
            //verifico si la compra es a futuro o de uso inmediato
            if (this.form.tipo_financiamiento == 1) {
              if (element.financiamiento == 1) {
                //es una venta de uso inmediato y no puede haber opciones de meses
                //precio de contado a 0 meses 1 solo pago
                this.planesVenta.push({
                  label: element.tipo_financiamiento,
                  value: Number(element.financiamiento),
                  subtotal: Number(element.subtotal),
                  impuestos: Number(element.impuestos),
                  costo_neto: Number(element.costo_neto),
                  pago_inicial: Number(element.pago_inicial),
                  descuento_pronto_pago_b: Number(
                    element.descuento_pronto_pago_b
                  ),
                  costo_neto_pronto_pago: Number(
                    element.costo_neto_pronto_pago
                  ),
                  porcentaje_pronto_pago: Number(
                    element.porcentaje_pronto_pago
                  ),
                  costo_neto_financiamiento_normal: Number(
                    element.costo_neto_financiamiento_normal
                  ),
                });
              }
            } else {
              //la venta es a futuro y puede seleccionar todas los siguientes planes de venta
              //precios de pagos a meses
              if (element.financiamiento > 1) {
                this.planesVenta.push({
                  label: element.tipo_financiamiento,
                  value: Number(element.financiamiento),
                  subtotal: Number(element.subtotal),
                  impuestos: Number(element.impuestos),
                  costo_neto: Number(element.costo_neto),
                  pago_inicial: Number(element.pago_inicial),
                  descuento_pronto_pago_b: Number(
                    element.descuento_pronto_pago_b
                  ),
                  costo_neto_pronto_pago: Number(
                    element.costo_neto_pronto_pago
                  ),
                  porcentaje_pronto_pago: Number(
                    element.porcentaje_pronto_pago
                  ),
                  costo_neto_financiamiento_normal: Number(
                    element.costo_neto_financiamiento_normal
                  ),
                });
              }
            }
          }
        });

        //selecciono el primero precio automaticamente
        if (this.getTipoformulario == "agregar") {
          /**tipo de formulario agregar */
          this.seleccionarPlanVenta();
        } else {
          /**logica para traer los datos originales de la venta */
          let plan_encontrado = false;
          this.planesVenta.forEach((element) => {
            if (
              element.value == this.datosVenta.financiamiento &&
              element.costo_neto == this.datosVenta.total &&
              element.costo_neto_pronto_pago ==
                this.datosVenta.costo_neto_pronto_pago
            ) {
              /**en caso de que se encuentre el orignal */
              this.form.planVenta = element;
              plan_encontrado = true;
              return;
            }
          });

          if (plan_encontrado == false) {
            if (
              this.form.planVenta.value == "" &&
              this.form.tipo_financiamiento ==
                this.datosVenta.venta_terreno.tipo_financiamiento
            ) {
              /**no se encontro el plan y se debe de agregar uno como de origen */
              let planVenta = {
                label:
                  this.datosVenta.financiamiento == 1
                    ? "Pago Único/Uso Inmediato (Plan Original)"
                    : "Pago a " +
                      this.datosVenta.financiamiento +
                      " Meses/a Futuro(origen venta)",
                value: Number(this.datosVenta.financiamiento),
                subtotal: Number(this.datosVenta.subtotal),
                impuestos: Number(this.datosVenta.impuestos),
                costo_neto: Number(this.datosVenta.total),
                pago_inicial: Number(
                  this.datosVenta.pagos_programados.length > 0
                    ? this.datosVenta.pagos_programados[0].monto_programado
                    : 0
                ),
                descuento_pronto_pago_b: Number(
                  this.datosVenta.descuento_pronto_pago_b
                ),
                costo_neto_pronto_pago: Number(
                  this.datosVenta.costo_neto_pronto_pago
                ),
                porcentaje_pronto_pago: 0,
                costo_neto_financiamiento_normal: Number(
                  this.datosVenta.costo_neto_financiamiento_normal
                ),
              };
              this.planesVenta.push(planVenta);
              this.form.planVenta = planVenta;
            } else {
              this.seleccionarPlanVenta();
            }
          }

          this.form.descuento = this.datosVenta.descuento;
          /**seleccionando el pago inicial */
          if (this.datosVenta.pagos_programados.length > 0) {
            /**tiene pagos programado y debemos seleccionar el pago programado para tomar el pago inicial */
            this.form.pago_inicial = this.datosVenta.pagos_programados[0].monto_programado;
          } else {
            this.form.pago_inicial = 0;
          }
        }
      } //fin if datos area
    },
    seleccionarPlanVenta() {
      //selecciono el primero precio automaticamente
      if (this.planesVenta.length > 1) {
        this.form.planVenta = this.planesVenta[1];
      } else {
        this.form.planVenta = this.planesVenta[0];
        this.$vs.notify({
          title: "Planes de Venta",
          text:
            "No hay planes de venta que mostrar. Debe ingresarlos en la sección 'Planes de Venta en módulo de Cementerio > Venta de Terrenos'",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "12000",
        });
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
      this.form.ventaAntiguedad = this.ventasAntiguedad[0];
      //area de la terraza
      this.form.lotes = this.lotes[0];
      this.form.tipo_financiamiento = "";
      this.form.solicitud = "";
      this.form.convenio = "";
      this.form.titulo = "";
      this.form.titular_sustituto = "";
      this.form.parentesco_titular_sustituto = "";
      this.form.telefono_titular_sustituto = "";
      this.form.cliente = "";
      this.form.id_cliente = "";
      this.form.beneficiarios = [];
      //this.form.planVenta = this.planesVenta[0];
      this.form.fecha_venta = "";
      this.form.vendedor = { label: "Seleccione 1", value: "" };
      this.form.tasa_iva = 16;
      this.form.descuento = "";
      this.form.subtotal = "";
      this.form.impuestos = "";
      this.form.costo_neto_pronto_pago = "";
      this.form.pago_inicial = "";
      this.idAreaInicial = 0;
      this.form.nota = "";
    },

    closePassword() {
      this.openPassword = false;
    },

    //agregar beneficiario
    agregar_beneficiario() {
      //verifico si todos los datos estan completos para dejarle agregar nuevos
      let errores_de_captura_en_datos = 0;
      /**maximo 10 beneficiarios */
      if (this.form.beneficiarios.length < 10) {
        this.form.beneficiarios.forEach((element) => {
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
            position: "bottom-right",
            time: "9000",
          });
        } else {
          //si paso la prueba de toodos los datos
          this.form.beneficiarios.push({
            nombre: "",
            parentesco: "",
            telefono: "",
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
          position: "bottom-right",
          time: "9000",
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
        let res = await cementerio.consultar_venta_id(this.form.id_venta);
        this.datosVenta = res.data[0];

        /**actualizo el tipo_financimiamiento para que cargue los planes */
        this.form.tipo_financiamiento = this.datosVenta.venta_terreno.tipo_financiamiento;
        this.idAreaInicial = this.datosVenta.venta_terreno.propiedades_id;
        /**se comienza a llenar la informacion de los datos */
        this.ventasAntiguedad.forEach((element) => {
          if (element.value == this.datosVenta.antiguedad_operacion_id) {
            this.form.ventaAntiguedad = element;
            return;
          }
        });

        this.form.id_cliente = this.datosVenta.cliente_id;
        this.form.cliente = this.datosVenta.nombre;

        /**verificando si existe el vendedor o si no para crearlo, podria no existir en caso de que haya sido cancelado */
        this.vendedores.forEach((element) => {
          if (element.value == this.datosVenta.venta_terreno.vendedor.id) {
            this.form.vendedor = element;
          }
        });
        if (this.form.vendedor.value == "") {
          let vendedor = {
            value: this.datosVenta.venta_terreno.vendedor.id,
            label:
              "(" +
              this.datosVenta.venta_terreno.vendedor.nombre +
              ", vendedor de origen)",
          };
          this.vendedores.push(vendedor);
          this.form.vendedor = vendedor;
          /**se agrega el original y se selecciona */
        }
        //fin seleccionar vendedor
        /**fecha de la venta */
        var partes_fecha = this.datosVenta.fecha_operacion.split("-");
        //yyyy-mm-dd
        this.form.fecha_venta = new Date(
          partes_fecha[0],
          partes_fecha[1] - 1,
          partes_fecha[2]
        );
        /**numeros de control */
        this.form.solicitud = this.datosVenta.numero_solicitud;
        this.form.convenio = this.datosVenta.numero_convenio;
        this.form.titulo = this.datosVenta.numero_titulo;

        /**datos del titular sustituto */
        this.form.titular_sustituto = this.datosVenta.titular_sustituto;
        this.form.parentesco_titular_sustituto = this.datosVenta.parentesco_titular_sustituto;
        this.form.telefono_titular_sustituto = this.datosVenta.telefono_titular_sustituto;

        /**beneficairios */
        this.form.beneficiarios = this.datosVenta.beneficiarios;
        this.form.financiamiento = this.datosVenta.financiamiento;
        this.form.subtotal = this.datosVenta.subtotal;
        this.form.descuento = this.datosVenta.descuento;
        this.form.tasa_iva =
          Number(this.datosVenta.tasa_iva) <= 0 ? 16 : this.datosVenta.tasa_iva;
        this.form.pago_inicial = this.datosVenta.pagos_programados[0].monto_programado;
        this.form.costo_neto_pronto_pago = this.datosVenta.costo_neto_pronto_pago;
        this.form.nota = this.datosVenta.nota;

        /**mostrando los datos relacionados al pago */

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
    respuestaDeshabilitado() {
      if (this.tienePagosVencidos) {
        this.$vs.notify({
          title: "Seleccionar Área del Cementerio",
          text:
            "No está permitido cambiar la ubicación de la propiedad mientras no esté al corriente con los pagos establecidos.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "10000",
        });
      } else if (this.ventaLiquidada) {
        this.$vs.notify({
          title: "Seleccionar Área del Cementerio",
          text:
            "No está permitido cambiar la ubicación de la propiedad una vez ya liquidado el total de la cuenta.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "10000",
        });
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
