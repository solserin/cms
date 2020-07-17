<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups-pagos normal-forms venta-propiedades background-header-forms"
      fullscreen
      close="cancelar"
      :title="title"
      :active.sync="showVentana"
      ref="formulario"
    >
      <div class="flex flex-wrap px-2">
        <!--articulo-->
        <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
          <!--contenido del plan funerario-->
          <div class="float-left px-2">
            <img width="36px" src="@assets/images/image.svg" />
            <h3 class="float-right ml-3 text-xl px-2 py-1 bg-seccion-forms">
              Seleccionar imagen del artículo o servicio
            </h3>
          </div>
          <div class="w-full mt-16" v-if="verLista">ver imagen</div>
          <div class="w-full mt-16" v-else>
            no
          </div>
          <!--fin de contenido del plan funerario-->
        </div>

        <div class="w-full sm:w-12/12 md:w-7/12 lg:w-7/12 xl:w-7/12 px-2">
          <div class="float-left pb-5 px-2">
            <img width="36px" src="@assets/images/stock.svg" />
            <h3
              class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms capitalize"
            >
              Información del artículo o servicio
            </h3>
          </div>

          <div class="w-full px-2">
            <vs-divider />
          </div>
          <div class="flex flex-wrap mt-1">
            <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
              <label class="text-sm opacity-75 font-bold">
                Descripción
                <span class="text-danger text-sm">(*)</span>
              </label>

              <vs-input
                ref="nombre_comercial"
                name="nombre_comercial"
                data-vv-as=" "
                v-validate.disabled="'required'"
                maxlength="100"
                type="text"
                class="w-full pb-1 pt-1"
                placeholder="Ej. Funeraria Aeternus"
                v-model.trim="form.nombre_comercial"
              />
              <div>
                <span class="text-danger text-sm">{{
                  errors.first("nombre_comercial")
                }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="text-danger text-sm"
                  v-if="this.errores.nombre_comercial"
                  >{{ errores.nombre_comercial[0] }}</span
                >
              </div>
            </div>
            <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
              <label class="text-sm opacity-75 font-bold">
                Descripción (Inglés)
                <span class="text-danger text-sm">(*)</span>
              </label>

              <vs-input
                ref="nombre_comercial"
                name="nombre_comercial"
                data-vv-as=" "
                v-validate.disabled="'required'"
                maxlength="100"
                type="text"
                class="w-full pb-1 pt-1"
                placeholder="Ej. Funeraria Aeternus"
                v-model.trim="form.nombre_comercial"
              />
              <div>
                <span class="text-danger text-sm">{{
                  errors.first("nombre_comercial")
                }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="text-danger text-sm"
                  v-if="this.errores.nombre_comercial"
                  >{{ errores.nombre_comercial[0] }}</span
                >
              </div>
            </div>
            <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
              <label class="text-sm opacity-75 font-bold">
                <span>Tipo de Artículo a Inventariar</span>
                <span class="texto-importante">(*)</span>
              </label>
              <v-select
                :options="tipo_articulos"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="form.tipo_articulo"
                class="mb-4 sm:mb-0 pb-1 pt-1"
                v-validate:plan_funerario_validacion_computed.immediate="
                  'required'
                "
                name="plan_validacion"
                data-vv-as=" "
              >
                <div slot="no-options">Seleccione 1</div>
              </v-select>
              <div>
                <span class="mensaje-requerido">{{
                  errors.first("plan_validacion")
                }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="mensaje-requerido"
                  v-if="this.errores['plan_funerario.value']"
                  >{{ errores["plan_funerario.value"][0] }}</span
                >
              </div>
            </div>
            <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
              <label class="text-sm opacity-75 font-bold">
                <span>Código de barras</span>
                <span class="texto-importante">(*)</span>
              </label>
              <vs-input
                readonly
                v-validate="'required'"
                name="id_cliente"
                data-vv-as=" "
                type="text"
                class="w-full py-1 cursor-pointer texto-bold"
                placeholder="Ingrese el código de barras del artículo"
                v-model="form.cliente"
                maxlength="50"
                ref="cliente_ref"
              />
              <div>
                <span class="mensaje-requerido">{{
                  errors.first("id_cliente")
                }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="mensaje-requerido"
                  v-if="this.errores.id_cliente"
                  >{{ errores.id_cliente[0] }}</span
                >
              </div>
            </div>
            <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
              <label class="text-sm opacity-75 font-bold">
                <span>Departamentos</span>
                <span class="texto-importante">(*)</span>
              </label>
              <v-select
                :options="departamentos"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="form.departamento"
                class="mb-4 sm:mb-0 pb-1 pt-1"
                v-validate:plan_funerario_validacion_computed.immediate="
                  'required'
                "
                name="plan_validacion"
                data-vv-as=" "
              >
                <div slot="no-options">Seleccione 1</div>
              </v-select>
              <div>
                <span class="mensaje-requerido">{{
                  errors.first("plan_validacion")
                }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="mensaje-requerido"
                  v-if="this.errores['plan_funerario.value']"
                  >{{ errores["plan_funerario.value"][0] }}</span
                >
              </div>
            </div>
            <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
              <label class="text-sm opacity-75 font-bold">
                <span>Categorías</span>
                <span class="texto-importante">(*)</span>
              </label>
              <v-select
                :options="categorias"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="form.categoria"
                class="mb-4 sm:mb-0 pb-1 pt-1"
                v-validate:antiguedad_validacion_computed.immediate="'required'"
                name="antiguedad_validacion"
                data-vv-as=" "
              >
                <div slot="no-options">Seleccione 1</div>
              </v-select>
              <div>
                <span class="mensaje-requerido">
                  {{ errors.first("antiguedad_validacion") }}
                </span>
              </div>
              <div class="mt-2">
                <span
                  class="mensaje-requerido"
                  v-if="this.errores['ventaAntiguedad.value']"
                >
                  {{ errores["ventaAntiguedad.value"][0] }}
                </span>
              </div>
            </div>
          </div>

          <vs-divider />
        </div>
        <div class="flex flex-wrap mt-1">
          <div class="w-full">
            <div class="float-left pb-3 px-2">
              <img width="36px" src="@assets/images/measuring.svg" />
              <h3
                class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
              >
                Unidades de medida del artículo o servicio
              </h3>
            </div>
          </div>

          <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Unidad de Compra</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="categorias"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.categoria"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              v-validate:antiguedad_validacion_computed.immediate="'required'"
              name="antiguedad_validacion"
              data-vv-as=" "
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="mensaje-requerido">
                {{ errors.first("antiguedad_validacion") }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores['ventaAntiguedad.value']"
              >
                {{ errores["ventaAntiguedad.value"][0] }}
              </span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Unidad de Venta</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="categorias"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.categoria"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              v-validate:antiguedad_validacion_computed.immediate="'required'"
              name="antiguedad_validacion"
              data-vv-as=" "
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="mensaje-requerido">
                {{ errors.first("antiguedad_validacion") }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores['ventaAntiguedad.value']"
              >
                {{ errores["ventaAntiguedad.value"][0] }}
              </span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              Cantidad Factor
              <span class="texto-importante">(*)</span>
            </label>
            <vs-input
              v-validate:solicitud_validacion_computed.immediate="'required'"
              name="solicitud"
              data-vv-as=" "
              type="text"
              class="w-full pb-1 pt-1"
              placeholder=" Núm. Solicitud"
              v-model="form.solicitud"
              :disabled="fueCancelada"
              maxlength="12"
            />
            <div>
              <span class="mensaje-requerido">{{
                errors.first("solicitud")
              }}</span>
            </div>
            <div class="mt-2">
              <span class="mensaje-requerido" v-if="this.errores.solicitud">{{
                errores.solicitud[0]
              }}</span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              Mínimo Inventario
              <span class="texto-importante">(*)</span>
            </label>
            <vs-input
              v-validate:solicitud_validacion_computed.immediate="'required'"
              name="solicitud"
              data-vv-as=" "
              type="text"
              class="w-full pb-1 pt-1"
              placeholder=" Núm. Solicitud"
              v-model="form.solicitud"
              :disabled="fueCancelada"
              maxlength="12"
            />
            <div>
              <span class="mensaje-requerido">{{
                errors.first("solicitud")
              }}</span>
            </div>
            <div class="mt-2">
              <span class="mensaje-requerido" v-if="this.errores.solicitud">{{
                errores.solicitud[0]
              }}</span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              Máximo Inventario
              <span class="texto-importante">(*)</span>
            </label>
            <vs-input
              v-validate:solicitud_validacion_computed.immediate="'required'"
              name="solicitud"
              data-vv-as=" "
              type="text"
              class="w-full pb-1 pt-1"
              placeholder=" Núm. Solicitud"
              v-model="form.solicitud"
              :disabled="fueCancelada"
              maxlength="12"
            />
            <div>
              <span class="mensaje-requerido">{{
                errors.first("solicitud")
              }}</span>
            </div>
            <div class="mt-2">
              <span class="mensaje-requerido" v-if="this.errores.solicitud">{{
                errores.solicitud[0]
              }}</span>
            </div>
          </div>

          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Unidad de Servicio o Producto SAT</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="categorias"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.categoria"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              v-validate:antiguedad_validacion_computed.immediate="'required'"
              name="antiguedad_validacion"
              data-vv-as=" "
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="mensaje-requerido">
                {{ errors.first("antiguedad_validacion") }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores['ventaAntiguedad.value']"
              >
                {{ errores["ventaAntiguedad.value"][0] }}
              </span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Grava IVA</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="categorias"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.categoria"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              v-validate:antiguedad_validacion_computed.immediate="'required'"
              name="antiguedad_validacion"
              data-vv-as=" "
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="mensaje-requerido">
                {{ errors.first("antiguedad_validacion") }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores['ventaAntiguedad.value']"
              >
                {{ errores["ventaAntiguedad.value"][0] }}
              </span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Grava ISR</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="categorias"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.categoria"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              v-validate:antiguedad_validacion_computed.immediate="'required'"
              name="antiguedad_validacion"
              data-vv-as=" "
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="mensaje-requerido">
                {{ errors.first("antiguedad_validacion") }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores['ventaAntiguedad.value']"
              >
                {{ errores["ventaAntiguedad.value"][0] }}
              </span>
            </div>
          </div>

          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Manejar Caducidades</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="categorias"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.categoria"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              v-validate:antiguedad_validacion_computed.immediate="'required'"
              name="antiguedad_validacion"
              data-vv-as=" "
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="mensaje-requerido">
                {{ errors.first("antiguedad_validacion") }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores['ventaAntiguedad.value']"
              >
                {{ errores["ventaAntiguedad.value"][0] }}
              </span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              $ Costo Neto Compra
              <span class="texto-importante">(*)</span>
            </label>
            <vs-input
              v-validate:solicitud_validacion_computed.immediate="'required'"
              name="solicitud"
              data-vv-as=" "
              type="text"
              class="w-full pb-1 pt-1"
              placeholder=" Núm. Solicitud"
              v-model="form.solicitud"
              :disabled="fueCancelada"
              maxlength="12"
            />
            <div>
              <span class="mensaje-requerido">{{
                errors.first("solicitud")
              }}</span>
            </div>
            <div class="mt-2">
              <span class="mensaje-requerido" v-if="this.errores.solicitud">{{
                errores.solicitud[0]
              }}</span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              $ Costo Neto Venta
              <span class="texto-importante">(*)</span>
            </label>
            <vs-input
              v-validate:num_convenio_validacion_computed.immediate="'required'"
              name="num_convenio"
              data-vv-as=" "
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Núm. Convenio"
              v-model="form.convenio"
              maxlength="16"
            />
            <div>
              <span class="mensaje-requerido">{{
                errors.first("num_convenio")
              }}</span>
            </div>
            <div class="mt-2">
              <span class="mensaje-requerido" v-if="this.errores.convenio">{{
                errores.convenio[0]
              }}</span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
            <vs-textarea
              height="200px"
              maxlength="350"
              size="large"
              ref="nota"
              type="text"
              class="w-full pt-3 pb-3"
              placeholder="Ingrese una nota..."
              v-model.trim="form.nota"
            />
          </div>
          <vs-divider />
        </div>
        <!--fin articulo-->
      </div>

      <div class="flex flex-wrap px-2">
        <div class="w-full px-2">
          <div class="mt-2">
            <p class="text-center">
              <span class="text-danger font-medium">Ojo:</span>
              Por favor revise la información ingresada, si todo es correcto de
              click en "Botón de Abajo”.
            </p>
          </div>
        </div>
      </div>
      <div
        class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 pt-6 pb-10 px-2 mr-auto ml-auto"
      >
        <vs-button class="w-full" @click="acceptAlert()" color="primary">
          <img
            width="25px"
            class="cursor-pointer"
            size="small"
            src="@assets/images/save.svg"
          />
          <span class="texto-btn" v-if="this.getTipoformulario == 'agregar'"
            >Guardar Datos</span
          >
          <span class="texto-btn" v-else>Modificar Datos</span>
        </vs-button>
      </div>

      <!--fin proveedor-->
    </vs-popup>
    <Password
      :show="operConfirmar"
      :callback-on-success="callback"
      @closeVerificar="closeChecker"
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
      :accion="'He revisado la información y quiero registrar a este proveedor'"
      :confirmarButton="'Guardar Proveedor'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import vSelect from "vue-select";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import proveedores from "@services/proveedores";

import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
/**VARIABLES GLOBALES */

export default {
  components: {
    "v-select": vSelect,
    Password,
    ConfirmarDanger,
    ConfirmarAceptar
  },
  props: {
    show: {
      type: Boolean,
      required: true
    },
    tipo: {
      type: String,
      required: true
    },
    id_proveedor: {
      type: Number,
      required: false,
      default: 0
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      this.limpiarValidation();
      if (newValue == true) {
        //cargo nacionalidades
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() =>
          this.$refs["nombre_comercial"].$el.querySelector("input").focus()
        );
        if (this.getTipoformulario == "modificar") {
          this.title = "Modificar Artículo/Servicio del Inventario";
          /**se cargan los datos al formulario */
          (async () => {
            await this.get_proveedor_by_id(this.get_proveedor_id);
          })();
        } else {
          this.title = "Registrar Nuevo Artículo/Servicio al Inventario";
        }
      }
    }
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
    getTipoformulario: {
      get() {
        return this.tipo;
      },
      set(newValue) {
        return newValue;
      }
    },
    get_proveedor_id: {
      get() {
        return this.id_proveedor;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  data() {
    return {
      title: "",
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      operConfirmar: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Modificar Proveedor",
      /**form */

      tipo_articulos: [
        {
          value: "1",
          label: "Artículo"
        },
        {
          value: "2",
          label: "Servicio"
        },
        {
          value: "3",
          label: "Equipo Rentable"
        }
      ],

      departamentos: [
        {
          value: "",
          label: "Seleccione 1"
        }
      ],
      categorias: [
        {
          value: "",
          label: "Seleccione 1"
        }
      ],
      form: {
        tipo_articulo: {
          value: "1",
          label: "Artículo"
        },
        departamento: {
          value: "",
          label: "Seleccione 1"
        },
        categoria: {
          value: "",
          label: "Seleccione 1"
        },
        /**form */
        /**en caso de modificar */
        id_proveedor_modificar: 0,
        nombre_comercial: "",
        razon_social: "",
        direccion: "",
        nombre_contacto: "",
        telefono: "",
        email: "",
        nota: ""
      },
      errores: []
    };
  },
  methods: {
    async get_proveedor_by_id() {
      /**trae la informacion de el proveedor por id */
      this.$vs.loading();
      try {
        let res = await proveedores.get_proveedor_by_id(this.get_proveedor_id);
        let datos = res.data[0];
        //actualizo los datos en el formulario
        this.form.nombre_comercial = datos.nombre_comercial;
        this.form.razon_social = datos.razon_social;
        this.form.direccion = datos.direccion;
        this.form.nombre_contacto = datos.nombre_contacto;
        this.form.telefono = datos.telefono;
        this.form.email = datos.email;
        this.form.nota = datos.nota;
        this.form.id_proveedor_modificar = datos.id;
        this.$vs.loading.close();
      } catch (error) {
        this.$vs.loading.close();
        this.$vs.notify({
          title: "Modificar Proveedor",
          text: "Ocurrió un error al traer la informacion, reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "4000"
        });
        this.cerrarVentana();
      }
    },
    acceptAlert() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$vs.notify({
              title: "Guardar Proveedor",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000"
            });
          } else {
            this.errores = [];
            (async () => {
              if (this.getTipoformulario == "agregar") {
                this.callBackConfirmarAceptar = await this.guardar_proveedor;
                this.openConfirmarAceptar = true;
              } else {
                /**modificar, se valida con password */
                this.form.id_proveedor_modificar = this.get_proveedor_id;
                this.callback = await this.modificar_proveedor;
                this.operConfirmar = true;
              }
            })();
          }
        })
        .catch(() => {});
    },
    async guardar_proveedor() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await proveedores.guardar_proveedor(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Registro de Proveedores",
            text: "Se ha guardado el proveedor correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000
          });
          this.$emit("retornar_id", res.data);
          this.cerrarVentana();
        } else {
          this.$vs.notify({
            title: "Registro de Proveedores",
            text: "Error al guardar el proveedor, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 4000
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
              time: 12000
            });
          } else if (err.response.status == 422) {
            //checo si existe cada error
            this.errores = err.response.data.error;
            this.$vs.notify({
              title: "Registro de Proveedores",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 12000
            });
            //console.log(err.response);
          }
        }
        this.$vs.loading.close();
      }
    },

    async modificar_proveedor() {
      //aqui mando modoificar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await proveedores.modificar_proveedor(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Modificación de Proveedores",
            text: "Se modificó el proveedor correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000
          });
          this.$emit("retornar_id", res.data);
        } else {
          this.$vs.notify({
            title: "Modificación de Proveedores",
            text: "No se han realizado cambios, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "warning",
            time: 4000
          });
        }
        this.cerrarVentana();
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
              time: 4000
            });
          } else if (err.response.status == 422) {
            //checo si existe cada error
            this.errores = err.response.data.error;
            this.$vs.notify({
              title: "Modificación de Proveedores",
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
      }
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
    limpiarVentana() {
      this.form.nombre_comercial = "";
      this.form.razon_social = "";
      this.form.direccion = "";
      this.form.nombre_contacto = "";
      this.form.telefono = "";
      this.form.email = "";
      this.form.nota = "";
    },
    limpiarValidation() {
      this.$validator.pause();
      this.$nextTick(() => {
        this.$validator.errors.clear();
        this.$validator.fields.items.forEach(field => field.reset());
        this.$validator.fields.items.forEach(field =>
          this.errors.remove(field)
        );
        this.$validator.resume();
      });
    },
    closeChecker() {
      this.operConfirmar = false;
    }
  },
  created() {}
};
</script>