<template>
  <div class="centerx">
    <vs-popup
      class="normal-forms background-header-forms normal"
      fullscreen
      close="cancelar"
      :title="
        getTipoformulario == 'facturar'
          ? 'Emitir CFDI 3.3'
          : 'POR DEFINIR FUNCION'
      "
      :active.sync="showVentana"
      ref="formulario"
    >
      <div class="cfdi-contenido">
        <div>
          <div class="float-left pb-2 px-2">
            <img width="36px" src="@assets/images/businessman.svg" />
            <h3 class="float-right ml-3 text-xl px-2 py-1 bg-seccion-forms">
              Información del Receptor
            </h3>
          </div>
        </div>
        <div class="w-full px-2">
          <vs-divider />
        </div>
        <div class="flex flex-wrap">
          <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              Seleccione un cliente
              <span class="texto-importante">(*)</span>
            </label>
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-1/12 lg:w-1/12 xl:w-1/12">
                <img
                  v-if="form.id_cliente == ''"
                  width="46px"
                  class="cursor-pointer p-2"
                  src="@assets/images/search.svg"
                  title="Buscar Cliente"
                  @click="openBuscadorCliente = true"
                />
                <img
                  v-else
                  width="46px"
                  class="cursor-pointer p-2"
                  src="@assets/images/minus.svg"
                  @click="quitarCliente()"
                />
              </div>
              <div class="w-full sm:w-12/12 md:w-11/12 lg:w-11/12 xl:w-11/12">
                <vs-input
                  readonly
                  v-validate.disabled="'required'"
                  name="id_cliente"
                  data-vv-as=" "
                  type="text"
                  class="w-full py-1 cursor-pointer texto-bold"
                  placeholder="SELECCIONE UN CLIENTE PARA LA FACTURA"
                  v-model="form.cliente"
                  maxlength="100"
                  ref="cliente_ref"
                />
                <div>
                  <span class="text-danger">
                    {{ errors.first("id_cliente") }}
                  </span>
                </div>
                <div class="mt-2">
                  <span class="text-danger" v-if="this.errores['id_cliente']">{{
                    errores["id_cliente"][0]
                  }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Tipo de RFC</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="tipos_rfc"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.tipo_rfc"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="tipo_rfc"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="text-danger">
                {{ errors.first("tipo_rfc") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores['tipo_rfc.value']">{{
                errores["tipo_rfc.value"][0]
              }}</span>
            </div>
          </div>

          <div
            class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2"
            v-if="form.tipo_rfc.value == 1"
          >
            <label class="text-sm opacity-75 font-bold"
              >RFC del Cliente <span class="texto-importante">(*)</span></label
            >
            <vs-input
              name="rfc"
              maxlength="150"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Ingrese el RFC"
              v-model="form.rfc"
            />
            <div>
              <span class="text-danger">
                {{ errors.first("rfc") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.rfc">{{
                errores.rfc[0]
              }}</span>
            </div>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2"
            v-else-if="form.tipo_rfc.value == 2"
          >
            <label class="text-sm opacity-75 font-bold"
              >RFC Púb. En General
              <span class="texto-importante">(*)</span></label
            >
            <vs-input
              :disabled="true"
              name="rfc"
              maxlength="150"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Ingrese el RFC"
              value="XAXX010101000"
            />
            <div>
              <span class="text-danger">
                {{ errors.first("rfc") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.rfc">{{
                errores.rfc[0]
              }}</span>
            </div>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2"
            v-else
          >
            <label class="text-sm opacity-75 font-bold"
              >RFC Púb. En General Extranjero
              <span class="texto-importante">(*)</span></label
            >
            <vs-input
              :disabled="true"
              name="rfc"
              maxlength="150"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Ingrese el RFC"
              value="XEX010101000"
            />
            <div>
              <span class="text-danger">
                {{ errors.first("rfc") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.rfc">{{
                errores.rfc[0]
              }}</span>
            </div>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2"
            v-if="form.tipo_rfc.value == 1"
          >
            <label class="text-sm opacity-75 font-bold"
              >Razón Social <span class="texto-importante">(*)</span></label
            >
            <vs-input
              name="razon_social"
              maxlength="150"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Razón social del contribuyente"
              v-model="form.razon_social"
            />
            <div>
              <span class="text-danger">
                {{ errors.first("razon_social") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.razon_social">{{
                errores.razon_social[0]
              }}</span>
            </div>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2"
            v-else
          >
            <label class="text-sm opacity-75 font-bold"
              >Razón Social <span class="texto-importante">(*)</span></label
            >
            <vs-input
              :disabled="true"
              name="razon_social"
              maxlength="150"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Razón social del contribuyente"
              value="Público en General"
            />
            <div>
              <span class="text-danger">
                {{ errors.first("razon_social") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.razon_social">{{
                errores.razon_social[0]
              }}</span>
            </div>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2"
            v-if="form.tipo_rfc.value == 1"
          >
            <label class="text-sm opacity-75 font-bold">Dirección Fiscal</label>
            <vs-input
              name="direccion_fiscal"
              maxlength="150"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Dirección fiscal del contribuyente"
              v-model="form.direccion_fiscal"
            />
            <div>
              <span class="text-danger">
                {{ errors.first("direccion_fiscal") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.direccion_fiscal">{{
                errores.direccion_fiscal[0]
              }}</span>
            </div>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2"
            v-else
          >
            <label class="text-sm opacity-75 font-bold">Dirección Fiscal</label>
            <vs-input
              :disabled="true"
              name="direccion_fiscal"
              maxlength="150"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Dirección fiscal del contribuyente"
              value="N/A"
            />
            <div>
              <span class="text-danger">
                {{ errors.first("direccion_fiscal") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.direccion_fiscal">{{
                errores.direccion_fiscal[0]
              }}</span>
            </div>
          </div>

          <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>País de Residencia</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :disabled="form.tipo_rfc.value < 3"
              :options="sat_paises"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.sat_pais"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="sat_pais"
              v-validate:sat_pais_validacion_computed.immediate="'required'"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="text-danger" v-if="errors.first('sat_pais')">
                Seleccione el país de residencia
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores['sat_pais.value']">{{
                errores["sat_pais.value"][0]
              }}</span>
            </div>
          </div>
        </div>
        <div class="w-full px-2">
          <vs-divider />
        </div>
        <div>
          <div class="float-left pb-2 px-2 mt-6">
            <img width="36px" src="@assets/images/qr.svg" />
            <h3 class="float-right ml-3 text-xl px-2 py-1 bg-seccion-forms">
              Tipo de Comprobante
            </h3>
          </div>
        </div>
        <div class="w-full px-2">
          <vs-divider />
        </div>
        <div class="flex flex-wrap">
          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Tipo de Comprobante</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              v-validate:tipo_comprobante_validacion_computed.immediate="
                'required'
              "
              :options="tipos_comprobante"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.tipo_comprobante"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="tipo_comprobante"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="text-danger" v-if="errors.first('tipo_comprobante')">
                Ingrese este dato
              </span>
            </div>
            <div class="mt-2">
              <span
                class="text-danger"
                v-if="this.errores['tipo_comprobante.value']"
                >{{ errores["tipo_comprobante.value"][0] }}</span
              >
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Método de Pago</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              v-validate:metodo_pago_validacion_computed.immediate="'required'"
              :disabled="form.tipo_comprobante.value > 1"
              :options="metodos_pago"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.metodo_pago"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="metodo_pago"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="text-danger" v-if="errors.first('metodo_pago')">
                Ingrese el método de pago
              </span>
            </div>
            <div class="mt-2">
              <span
                class="text-danger"
                v-if="this.errores['metodo_pago.value']"
                >{{ errores["metodo_pago.value"][0] }}</span
              >
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Forma de Pago</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              v-validate:forma_pago_validacion_computed.immediate="'required'"
              :disabled="form.metodo_pago.value == 2"
              :options="formas_pago"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.forma_pago"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="forma_pago"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="text-danger" v-if="errors.first('forma_pago')">
                Ingrese la forma de pago
              </span>
            </div>
            <div class="mt-2">
              <span
                class="text-danger"
                v-if="this.errores['forma_pago.value']"
                >{{ errores["forma_pago.value"][0] }}</span
              >
            </div>
          </div>

          <div
            class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2"
            v-if="form.tipo_comprobante.value == 5"
          >
            <label class="text-sm opacity-75 font-bold">
              Fecha del Pago
              <span class="texto-importante">(*)</span>
            </label>

            <flat-pickr
              name="fecha_pago"
              data-vv-as=" "
              v-validate:fechapago_validacion_computed.immediate="'required'"
              :config="configdateTimePicker"
              v-model="form.fecha_pago"
              placeholder="Fecha del Pago"
              class="w-full my-1"
            />
            <div>
              <span class="text-danger">
                {{ errors.first("fecha_pago") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.fecha_pago">{{
                errores.fecha_pago[0]
              }}</span>
            </div>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2"
            v-else
          >
            <label class="text-sm opacity-75 font-bold"> Fecha del Pago </label>
            <vs-input
              :disabled="true"
              value="N/A"
              maxlength="150"
              type="text"
              class="w-full pb-1 pt-1"
            />
          </div>
          <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Uso del CFDI</span>
              <span
                class="texto-importante"
                v-if="form.tipo_comprobante.value == 1"
                >(*)</span
              >
            </label>
            <v-select
              v-validate:uso_cfdi_validacion_computed.immediate="'required'"
              :disabled="form.tipo_comprobante.value > 1"
              :options="usos_cfdi"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.uso_cfdi"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="usos_cfdi"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="text-danger" v-if="errors.first('usos_cfdi')">
                Seleccione el uso del CFDI
              </span>
            </div>
            <div class="mt-2">
              <span
                class="text-danger"
                v-if="this.errores['usos_cfdi.value']"
                >{{ errores["usos_cfdi.value"][0] }}</span
              >
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Tipo de Relación</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="tipos_relacion"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.tipo_relacion"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="tipo_relacion"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="text-danger">
                {{ errors.first("tipo_relacion") }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="text-danger"
                v-if="this.errores['tipo_relacion.value']"
                >{{ errores["tipo_relacion.value"][0] }}</span
              >
            </div>
          </div>
        </div>

        <div class="w-full px-2">
          <vs-divider />
        </div>

        <div class="flex flex-wrap">
          <div
            class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 md:text-right"
          >
            <div class="float-left pb-2 px-2 mt-6">
              <img width="36px" src="@assets/images/clip.svg" />
              <h3 class="float-right ml-3 text-xl px-2 py-1 bg-seccion-forms">
                CFDIS Relacionados
              </h3>
            </div>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 md:text-right"
          >
            <vs-button
              class="mt-4"
              size="small"
              color="primary"
              @click="openBuscadorArticulos = true"
            >
              <img
                class="cursor-pointer img-btn"
                src="@assets/images/cfdi.svg"
              />
              <span class="texto-btn">Buscar CFDI</span>
            </vs-button>
          </div>

          <div class="w-full px-2">
            <vs-divider />
          </div>
        </div>

        <div class="flex flex-wrap">
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
                <div class="w-full mt-5">
                  <vs-table
                    class="w-full"
                    :data="form.articulos_servicios"
                    noDataText="No se han agregado documentos a relacionar"
                  >
                    <template slot="header">
                      <h3>Facturas Relacionadas al CFDI</h3>
                    </template>
                    <template slot="thead">
                      <vs-th>#</vs-th>
                      <vs-th>Folio</vs-th>
                      <vs-th>UUID</vs-th>
                      <vs-th>Razón Social</vs-th>
                      <vs-th>Fecha Timbrado</vs-th>
                      <vs-th>$ Saldo Anterior</vs-th>
                      <vs-th>$ Monto a Pagar</vs-th>
                      <vs-th>$ Saldo Insoluto</vs-th>
                      <vs-th>Núm. Parcialidad</vs-th>
                      <vs-th>Quitar</vs-th>
                    </template>
                    <template slot-scope="{ data }">
                      <vs-tr
                        :data="tr"
                        :key="indextr"
                        v-for="(tr, indextr) in data"
                      >
                        <vs-td>
                          <div class="capitalize">
                            <span class="lowercase">{{ indextr + 1 }}</span>
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].tipo }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].descripcion }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].lote }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <vs-input
                            :name="'cantidad_articulos_servicios' + indextr"
                            data-vv-as=" "
                            data-vv-validate-on="blur"
                            v-validate="'required|integer|min_value:' + 1"
                            class="w-full sm:w-6/12 md:w-4/12 lg:w-4/12 xl:w-4/12 mr-auto ml-auto mt-1 cantidad"
                            maxlength="4"
                            v-model="form.articulos_servicios[indextr].cantidad"
                          />
                          <div>
                            <span class="text-danger text-xs">
                              {{
                                errors.first(
                                  "cantidad_articulos_servicios" + indextr
                                )
                              }}
                            </span>
                          </div>
                        </vs-td>
                        <vs-td>
                          <vs-input
                            :name="'cantidad_articulos_servicios' + indextr"
                            data-vv-as=" "
                            data-vv-validate-on="blur"
                            v-validate="'required|integer|min_value:' + 1"
                            class="w-full sm:w-6/12 md:w-4/12 lg:w-4/12 xl:w-4/12 mr-auto ml-auto mt-1 cantidad"
                            maxlength="4"
                            v-model="form.articulos_servicios[indextr].cantidad"
                          />
                          <div>
                            <span class="text-danger text-xs">
                              {{
                                errors.first(
                                  "cantidad_articulos_servicios" + indextr
                                )
                              }}
                            </span>
                          </div>
                        </vs-td>
                        <vs-td>
                          <vs-input
                            :name="'cantidad_articulos_servicios' + indextr"
                            data-vv-as=" "
                            data-vv-validate-on="blur"
                            v-validate="'required|integer|min_value:' + 1"
                            class="w-full sm:w-6/12 md:w-4/12 lg:w-4/12 xl:w-4/12 mr-auto ml-auto mt-1 cantidad"
                            maxlength="4"
                            v-model="form.articulos_servicios[indextr].cantidad"
                          />
                          <div>
                            <span class="text-danger text-xs">
                              {{
                                errors.first(
                                  "cantidad_articulos_servicios" + indextr
                                )
                              }}
                            </span>
                          </div>
                        </vs-td>
                        <vs-td>
                          <vs-input
                            :name="'cantidad_articulos_servicios' + indextr"
                            data-vv-as=" "
                            data-vv-validate-on="blur"
                            v-validate="'required|integer|min_value:' + 1"
                            class="w-full sm:w-6/12 md:w-4/12 lg:w-4/12 xl:w-4/12 mr-auto ml-auto mt-1 cantidad"
                            maxlength="4"
                            v-model="form.articulos_servicios[indextr].cantidad"
                          />
                          <div>
                            <span class="text-danger text-xs">
                              {{
                                errors.first(
                                  "cantidad_articulos_servicios" + indextr
                                )
                              }}
                            </span>
                          </div>
                        </vs-td>
                        <vs-td>
                          <div
                            class=""
                            @click="remover_articulo(indextr)"
                            v-if="!fueCancelada"
                          >
                            <img
                              class="cursor-pointer img-btn"
                              src="@assets/images/minus.svg"
                            />
                          </div>
                        </vs-td>
                      </vs-tr>
                    </template>
                  </vs-table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="w-full px-2">
          <vs-divider />
        </div>
        <div class="flex flex-wrap">
          <div
            class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 md:text-right"
          >
            <div class="float-left pb-2 px-2 mt-6">
              <img width="36px" src="@assets/images/articulos.svg" />
              <h3 class="float-right ml-3 text-xl px-2 py-1 bg-seccion-forms">
                Conceptos a Facturar
              </h3>
            </div>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 md:text-right"
          >
            <vs-button
              class="mt-4"
              size="small"
              color="primary"
              @click="openBuscadorArticulos = true"
            >
              <img
                class="cursor-pointer img-btn"
                src="@assets/images/searcharticulo.svg"
              />
              <span class="texto-btn">Buscar Conceptos por Operación</span>
            </vs-button>
          </div>

          <div class="w-full px-2">
            <vs-divider />
          </div>
        </div>

        <div class="flex flex-wrap">
          <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Clave de Producto o Servicio</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              data-vv-scope="conceptos"
              :options="claves_sat"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.clave_sat"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="clave_sat"
              v-validate.disable="'required'"
              v-validate:clave_sat_validacion_computed.immediate="'required'"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span
                class="text-danger"
                v-if="errors.first('conceptos.clave_sat')"
              >
                Seleccione una clave del Sat
              </span>
            </div>
            <div class="mt-2">
              <span
                class="text-danger"
                v-if="this.errores['clave_sat.value']"
                >{{ errores["clave_sat.value"][0] }}</span
              >
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Clave de Unidad</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              data-vv-scope="conceptos"
              :options="unidades_sat"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.unidad_sat"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="unidad_sat"
              v-validate:unidad_sat_validacion_computed.immediate="'required'"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span
                class="text-danger"
                v-if="errors.first('conceptos.unidad_sat')"
              >
                Seleccione la unidad del concepto
              </span>
            </div>
            <div class="mt-2">
              <span
                class="text-danger"
                v-if="this.errores['unidad_sat.value']"
                >{{ errores["unidad_sat.value"][0] }}</span
              >
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold"
              >Cantidad <span class="texto-importante">(*)</span></label
            >
            <vs-input
              data-vv-as="cantidad"
              data-vv-scope="conceptos"
              name="cantidad"
              maxlength="6"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Cantidad a agregar"
              v-model="form.cantidad"
              v-validate="'required|integer|min_value:' + 1"
            />
            <div>
              <span class="text-danger">
                {{ errors.first("conceptos.cantidad") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.cantidad">{{
                errores.cantidad[0]
              }}</span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
            <label class="text-sm opacity-75 font-bold"
              >Descripción <span class="texto-importante">(*)</span></label
            >
            <vs-input
              data-vv-as="Descripción"
              data-vv-scope="conceptos"
              name="descripcion"
              maxlength="150"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Descripción del concepto"
              v-validate="'required'"
              v-model="form.descripcion"
            />
            <div>
              <span class="text-danger">
                {{ errors.first("conceptos.descripcion") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.descripcion">{{
                errores.descripcion[0]
              }}</span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
            <label class="text-sm opacity-75 font-bold"
              >$ Precio Neto <span class="texto-importante">(*)</span></label
            >
            <vs-input
              data-vv-as="Precio Neto"
              data-vv-scope="conceptos"
              name="precio_neto"
              maxlength="10"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Ingrese el precio neto"
              v-model="form.precio_neto"
              v-validate="'required|decimal:2|min_value:' + 0"
            />
            <div>
              <span class="text-danger">
                {{ errors.first("conceptos.precio_neto") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.precio_neto">{{
                errores.precio_neto[0]
              }}</span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>¿Aplica Descuento?</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              data-vv-scope="conceptos"
              :options="sino"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.descuento_b"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="descuento_b"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="text-danger">
                {{ errors.first("conceptos.descuento_b") }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="text-danger"
                v-if="this.errores['descuento_b.value']"
                >{{ errores["descuento_b.value"][0] }}</span
              >
            </div>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2"
            v-if="form.descuento_b.value == 1"
          >
            <label class="text-sm opacity-75 font-bold"
              >$ Precio con Descuento
              <span class="texto-importante">(*)</span></label
            >
            <vs-input
              data-vv-as="Precio con descuento"
              data-vv-scope="conceptos"
              name="precio_descuento"
              maxlength="10"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Precio con el descuento"
              v-model="form.precio_descuento"
              v-validate="
                'required|decimal:2|min_value:' +
                0 +
                '|max_value:' +
                form.precio_neto
              "
            />
            <div>
              <span class="text-danger">
                {{ errors.first("conceptos.precio_descuento") }}
              </span>
            </div>
            <div class="mt-2">
              <span class="text-danger" v-if="this.errores.precio_descuento">{{
                errores.precio_descuento[0]
              }}</span>
            </div>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2"
            v-else
          >
            <label class="text-sm opacity-75 font-bold"
              >$ Precio con Descuento
            </label>
            <vs-input
              :disabled="true"
              data-vv-as="Precio con descuento"
              data-vv-scope="conceptos"
              name="precio_descuento"
              maxlength="10"
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Precio con el descuento"
              value="N/A"
            />
          </div>
          <div
            class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 md:text-right"
          >
            <vs-button
              class="mt-4"
              size="small"
              color="success"
              @click="AgregarConcepto"
            >
              <img
                class="cursor-pointer img-btn"
                src="@assets/images/plus.svg"
              />
              <span class="texto-btn">Agregar Concepto</span>
            </vs-button>
          </div>
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
            <div class="flex flex-wrap">
              {{ conceptos }}
              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
                <div class="w-full mt-5">
                  <vs-table
                    class="w-full"
                    :data="form.conceptos"
                    noDataText="No se han agregado conceptos a facturar"
                  >
                    <template slot="header">
                      <h3>Artículos y Servicios a Facturar</h3>
                    </template>
                    <template slot="thead">
                      <vs-th>#</vs-th>
                      <vs-th>Prod/Servicio SAT</vs-th>
                      <vs-th>Unidad</vs-th>
                      <vs-th>$ Costo Neto</vs-th>
                      <vs-th>Descuento</vs-th>
                      <vs-th>Cantidad</vs-th>
                      <vs-th>Importe</vs-th>
                      <vs-th>Facturar</vs-th>
                      <vs-th>Quitar</vs-th>
                    </template>
                    <template slot-scope="{ data }">
                      <vs-tr
                        :data="tr"
                        :key="indextr"
                        v-for="(tr, indextr) in data"
                      >
                        <vs-td>
                          <div class="capitalize">
                            <span class="lowercase">{{ indextr + 1 }}</span>
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].clave_sat.label }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].unidad_sat.label }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].precio_neto }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].descripcion }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].descripcion }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].descripcion }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div class="capitalize">
                            {{ data[indextr].concepto_operacion_ver_b }}
                          </div>
                        </vs-td>
                        <vs-td>
                          <div
                            class=""
                            @click="remover_articulo(indextr)"
                            v-if="!fueCancelada"
                          >
                            <img
                              class="cursor-pointer img-btn"
                              src="@assets/images/minus.svg"
                            />
                          </div>
                        </vs-td>
                      </vs-tr>
                    </template>
                  </vs-table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="w-full px-2">
          <vs-divider />
        </div>
        <div class="flex flex-wrap">
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
            <vx-card no-radius>
              <!--checkout-->
              <div class="w-full">
                <div class="flex flex-wrap my-6">
                  <div class="w-full px-2">
                    <vs-divider />
                  </div>
                  <div class="w-full sm:w-12/12 md:w-8/12 lg:9/12">
                    <div class="flex flex-wrap">
                      <div class="w-full pt-3 pb-3 px-2">
                        <div class="float-left">
                          <img
                            class="float-left"
                            width="36px"
                            src="@assets/images/notas_add.svg"
                          />
                          <h3
                            class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
                          >
                            Notas / Observaciones Sobre la Factura
                          </h3>
                        </div>
                      </div>
                      <div
                        class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
                      >
                        <label class="text-sm opacity-75 font-bold"
                          >NOTA U OBSERVACIÓN:</label
                        >
                        <vs-textarea
                          height="240px"
                          maxlength="350"
                          size="large"
                          ref="nota"
                          type="text"
                          class="w-full pt-3 pb-3 mt-1 large_textarea"
                          placeholder="Ingrese una nota..."
                          v-model.trim="form.nota"
                        />
                      </div>
                    </div>
                    <!--fin del resumen de la venta-->
                  </div>
                  <div class="w-full sm:w-12/12 md:w-4/12 lg:3/12 px-2">
                    <div class="flex flex-wrap">
                      <div class="w-full pt-3 pb-3 px-2">
                        <div class="float-left">
                          <img
                            class="float-left"
                            width="36px"
                            src="@assets/images/payments.svg"
                          />
                          <h3
                            class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
                          >
                            Total del Comprobante
                          </h3>
                        </div>
                      </div>
                    </div>
                    <div class="flex flex-wrap">
                      <div
                        class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 text-center"
                      >
                        <label class="text-xl opacity-75">
                          Tasa IVA %
                          <span class="texto-importante">(*)</span>
                        </label>
                        <vs-input
                          :disabled="true"
                          size="large"
                          name="tasa_iva"
                          data-vv-as=" "
                          v-validate="
                            'required|decimal:2|min_value:16|max_value:16'
                          "
                          type="text"
                          class="w-full pb-1 pt-1 texto-bold cantidad"
                          placeholder="Porcentaje IVA"
                          v-model="form.tasa_iva"
                          maxlength="2"
                        />
                        <div>
                          <span class="mensaje-requerido">
                            {{ errors.first("tasa_iva") }}
                          </span>
                        </div>
                        <div class="mt-2">
                          <span
                            class="mensaje-requerido"
                            v-if="this.errores.tasa_iva"
                            >{{ errores.tasa_iva[0] }}</span
                          >
                        </div>
                      </div>

                      <div
                        class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 text-center"
                      >
                        <label class="text-xl opacity-75"
                          >$ Total a Facturar</label
                        >
                        <div class="mt-3 text-center">
                          <span class="total_contrato text-3xl font-bold">
                            $
                            {{ totalContrato | numFormat("0,000.00") }}
                          </span>
                        </div>
                      </div>

                      <div class="w-full px-2 mt-2 text-center">
                        <p class="texto-ojo">
                          <span class="text-danger font-medium">Ojo:</span>
                          Los costos de los conceptos capturados ya incluyen el
                          IVA.
                        </p>
                        <vs-divider />
                      </div>

                      <div
                        class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
                      >
                        <div class="flex flex-wrap">
                          <vs-button
                            v-if="!fueCancelada"
                            class="w-full ml-auto mr-auto mt-1"
                            @click="acceptAlert()"
                            color="success"
                            size="large"
                          >
                            <img
                              width="25px"
                              class="cursor-pointer img-btn"
                              src="@assets/images/save.svg"
                            />
                            <span class="texto-btn">Timbrar CFDI</span>
                          </vs-button>
                        </div>
                      </div>
                      <!--fin de precios-->
                    </div>
                  </div>
                </div>
              </div>
              <!--fin del checkout-->
            </vx-card>
          </div>
        </div>
      </div>
    </vs-popup>
    <ConfirmarDanger
      :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarSinPassword = false"
      :accion="accionConfirmarSinPassword"
      :confirmarButton="botonConfirmarSinPassword"
    ></ConfirmarDanger>
    <ClientesBuscador
      :show="openBuscadorCliente"
      @closeBuscador="openBuscadorCliente = false"
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
import facturacion from "@services/facturacion";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";

import clientes from "@services/clientes";
import ClientesBuscador from "@pages/clientes/searcher.vue";

/**VARIABLES GLOBALES */
import {
  configdateTimePicker,
  configdateTimePickerWithTime,
} from "@/VariablesGlobales";

export default {
  components: {
    "v-select": vSelect,
    flatPickr,
    ConfirmarDanger,
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
    id_cfdi: {
      type: Number,
      required: false,
      default: 0,
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      this.limpiarValidation();
      if (newValue == true) {
        this.$nextTick(() => {
          //this.$refs["fallecido_ref"].$el.querySelector("input").focus();
        });
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        (async () => {
          if (this.getTipoformulario == "facturar") {
            await this.get_tipos_comprobante();
            await this.get_metodos_pago();
            await this.get_sat_formas_pago();
            await this.get_tipos_relacion();
            await this.get_claves_productos_sat();
            await this.get_sat_unidades();
            await this.get_usos_cfdi();
            await this.get_sat_paises();
          }
        })();
      } else {
        /**acciones al cerrar el formulario */
      }
    },

    "form.tipo_rfc": function (newValue, oldValue) {
      if (newValue.value < 3) {
        this.form.sat_pais = this.sat_paises[151];
      } else {
        this.form.sat_pais = this.sat_paises[0];
      }
    },

    "form.tipo_comprobante": function (newValue, oldValue) {
      if (newValue.value > 1) {
        this.form.metodo_pago = this.metodos_pago[1];
        if (this.form.metodo_pago.value > 1) {
          this.form.forma_pago = this.formas_pago[0];
        }

        this.form.uso_cfdi = this.usos_cfdi[0];
      } else {
        this.form.metodo_pago = this.metodos_pago[0];
      }
    },

    /**cambiando a por definir la forma de pago cuando el metodo es ppd */
    "form.metodo_pago": function (newValue, oldValue) {
      if (newValue.value == 2) {
        this.form.forma_pago = this.formas_pago[this.formas_pago.length - 1];
      } else {
        this.form.forma_pago = this.formas_pago[0];
      }
    },

    "form.forma_pago": function (newValue, oldValue) {
      if (this.form.tipo_comprobante.value > 1) {
        /**si es de tipo pago o egreso no puede ser de tipo por definir */
        if (newValue == this.formas_pago[this.formas_pago.length - 1]) {
          //lo regreso a elegir una nueva forma de pago
          this.form.forma_pago = this.formas_pago[0];
          this.$vs.notify({
            title: "Error en forma de pago",
            text:
              "Debe seleccionar una forma de pago diferente a 'Por Definir' cuando se trata de un comprobante de Pago o Egreso.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000,
          });
        }
      }
    },
  },
  computed: {
    sat_pais_validacion_computed: function () {
      return this.form.sat_pais.value;
    },
    tipo_comprobante_validacion_computed: function () {
      return this.form.tipo_comprobante.value;
    },

    metodo_pago_validacion_computed: function () {
      return this.form.metodo_pago.value;
    },

    forma_pago_validacion_computed: function () {
      return this.form.forma_pago.value;
    },

    uso_cfdi_validacion_computed: function () {
      if (this.form.tipo_comprobante.value == 1) {
        /**si es de ingreso */
        return this.form.uso_cfdi.value;
      } else {
        return true;
      }
    },

    clave_sat_validacion_computed: function () {
      return this.form.clave_sat.value;
    },
    unidad_sat_validacion_computed: function () {
      return this.form.unidad_sat.value;
    },
    fechapago_validacion_computed: function () {
      return this.form.fecha_pago;
    },

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
    get_id_cfdi: {
      get() {
        return this.id_cfdi;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      /**variables para el control del formulario */
      tipo: "",
      /**buscador del cliente */
      openBuscadorCliente: false,
      /**control del popup de confirmar accion */
      openConfirmarSinPassword: false,
      botonConfirmarSinPassword: "",
      accionConfirmarSinPassword: "",
      callBackConfirmar: Function,
      datos_cliente: [],
      tipos_rfc: [
        {
          value: "1",
          label: "Cliente con RFC",
        },
        {
          value: "2",
          label: "Púb. General",
        },
        {
          value: "3",
          label: "Púb. General Extranjero",
        },
      ],
      tipos_comprobante: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      metodos_pago: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      formas_pago: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      tipos_relacion: [
        {
          value: "",
          label: "N/A",
        },
      ],
      claves_sat: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      unidades_sat: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      usos_cfdi: [
        {
          value: "",
          label: "Seleccione 1",
        },
      ],
      sat_paises: [
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
      /**DATOS DEL FORMULARIO */
      form: {
        /**datos del cliente */
        id_cliente: "",
        cliente: "",
        tipo_rfc: {
          value: "1",
          label: "Cliente con RFC",
        },
        rfc: "",
        razon_social: "",
        direccion_fiscal: "",
        /**FIN DE datos del cliente */

        /**TIPO DE COMPROBANTE */
        tipo_comprobante: {
          value: "",
          label: "Seleccione 1",
        },
        metodo_pago: {
          value: "",
          label: "Seleccione 1",
        },
        forma_pago: {
          value: "",
          label: "Seleccione 1",
        },
        fecha_pago: "",
        tipo_relacion: {
          value: "",
          label: "N/A",
        },
        uso_cfdi: {
          value: "",
          label: "Seleccione 1",
        },
        sat_pais: {
          value: "",
          label: "Seleccione 1",
        },

        /**CFDIS RELACIONADOS */
        cfdis_relacionados: [],
        operaciones_relacionadas: [],
        conceptos: [],
        /**articulos a agregar fuera de operacion */
        clave_sat: {
          value: "",
          label: "Seleccione 1",
        },

        unidad_sat: {
          value: "",
          label: "Seleccione 1",
        },
        cantidad: "",
        descripcion: "",
        precio_neto: "",
        descuento_b: {
          value: "0",
          label: "NO",
        },
        precio_descuento: "",
        /**FIN DE articulos a agregar fuera de operacion */
        nota: "",
        tasa_iva: 16,
      },
      errores: [],
    };
  },
  methods: {
    /**metodo para agregar articulos manualmente */
    AgregarConcepto() {
      this.$validator
        .validateAll("conceptos")
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Agregar Artículos/Servicios al CFDI",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000",
            });
          } else {
            this.form.conceptos.push({
              clave_sat: this.form.clave_sat,
              unidad_sat: this.form.unidad_sat,
              cantidad: this.form.cantidad,
              descripcion: this.form.descripcion,
              precio_neto: this.form.precio_neto,
              descuento_b: this.form.descuento_b.value,
              modifica_b: 1,
              concepto_operacion_ver_b: 1,
              concepto_operacion_id: 0,
            });
            /**reseteando el concepto */
            /*this.form.seccion = {
              value: "incluye",
              label: "plan funerario",
            };
            this.index_concepto = "";
            this.index_seccion = "";
            this.form.concepto = "";
            this.form.concepto_ingles = "";
            this.limpiarValidation();
            */
            this.$nextTick(
              () => {}
              //this.$refs["concepto"].$el.querySelector("input").focus()
            );
          }
        })
        .catch(() => {});
    },

    async get_tipos_comprobante() {
      this.$vs.loading();
      await facturacion
        .get_tipos_comprobante()
        .then((res) => {
          this.tipos_comprobante = [];
          this.tipos_comprobante.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.tipos_comprobante.push({
              label: element.tipo,
              value: element.id,
            });
          });
          this.form.tipo_comprobante = this.tipos_comprobante[0];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },
    async get_metodos_pago() {
      this.$vs.loading();
      await facturacion
        .get_metodos_pago()
        .then((res) => {
          this.metodos_pago = [];
          this.metodos_pago.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.metodos_pago.push({
              label: element.metodo,
              value: element.id,
            });
          });
          this.form.metodo_pago = this.metodos_pago[0];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },
    async get_sat_formas_pago() {
      this.$vs.loading();
      await facturacion
        .get_sat_formas_pago()
        .then((res) => {
          this.formas_pago = [];
          this.formas_pago.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.formas_pago.push({
              label: element.forma,
              value: element.id,
            });
          });
          this.form.forma_pago = this.formas_pago[0];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },

    async get_tipos_relacion() {
      this.$vs.loading();
      await facturacion
        .get_tipos_relacion()
        .then((res) => {
          this.tipos_relacion = [];
          this.tipos_relacion.push({ label: "N/A", value: "" });
          res.data.forEach((element) => {
            this.tipos_relacion.push({
              label: element.tipo,
              value: element.id,
            });
          });
          this.form.tipo_relacion = this.tipos_relacion[0];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },
    async get_claves_productos_sat() {
      this.$vs.loading();
      await facturacion
        .get_claves_productos_sat()
        .then((res) => {
          this.claves_sat = [];
          this.claves_sat.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.claves_sat.push({
              label: element.clave,
              value: element.id,
            });
          });
          this.form.clave_sat = this.claves_sat[0];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },

    async get_sat_unidades() {
      this.$vs.loading();
      await facturacion
        .get_sat_unidades()
        .then((res) => {
          this.unidades_sat = [];
          this.unidades_sat.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.unidades_sat.push({
              label: element.clave,
              value: element.id,
            });
          });
          this.form.unidad_sat = this.unidades_sat[0];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },

    async get_usos_cfdi() {
      this.$vs.loading();
      await facturacion
        .get_usos_cfdi()
        .then((res) => {
          this.usos_cfdi = [];
          this.usos_cfdi.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.usos_cfdi.push({
              label: element.uso,
              value: element.id,
            });
          });
          this.form.uso_cfdi = this.usos_cfdi[0];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },

    async get_sat_paises() {
      this.$vs.loading();
      await facturacion
        .get_sat_paises()
        .then((res) => {
          this.sat_paises = [];
          this.sat_paises.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.sat_paises.push({
              label: element.pais,
              value: element.id,
            });
          });
          this.form.sat_pais = this.sat_paises[151];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },

    clienteSeleccionado(datos) {
      /**obtiene los datos retornados del buscar cliente */
      this.form.cliente = datos.nombre;
      this.form.id_cliente = datos.id_cliente;
      this.datos_cliente = datos;

      if (datos.datos.rfc != "" && datos.datos.rfc != "N/A") {
        this.form.rfc = datos.datos.rfc;
      }
      if (datos.datos.razon_social != "" && datos.datos.razon_social != "N/A") {
        this.form.razon_social = datos.datos.razon_social;
      }

      if (
        datos.datos.direccion_fiscal != "" &&
        datos.datos.direccion_fiscal != "N/A"
      ) {
        this.form.direccion_fiscal = datos.datos.direccion_fiscal;
      }
      //alert(datos.id_cliente);
    },
    limpiarCliente() {
      this.form.id_cliente = "";
      this.form.cliente = "";
      this.form.tipo_rfc = this.tipos_rfc[0];
      this.form.rfc = "";
      this.form.razon_social = "";
      this.form.direccion_fiscal = "";
    },
    quitarCliente() {
      this.botonConfirmarSinPassword = "Cambiar cliente";
      this.accionConfirmarSinPassword =
        "¿Desea cambiar de cliente para esta factura?";
      this.callBackConfirmar = this.limpiarCliente;
      this.openConfirmarSinPassword = true;
    },

    cancel() {
      this.$emit("closeVentana");
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
    limpiarVentana() {},

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
