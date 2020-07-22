<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups-pagos normal-forms venta-propiedades background-header-forms articulos"
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
          <div class="flex flex-wrap px-2">
            <div class="w-full">
              <div class="float-left px-2">
                <img width="36px" src="@assets/images/image.svg" />
                <h3
                  class="float-right ml-3 text-xl px-2 py-1 bg-seccion-forms"
                >Seleccionar imagen del artículo o servicio</h3>
              </div>
            </div>
            <div class="w-full">
              <input
                ref="fileImage"
                type="file"
                name="fileToUpload"
                id="fileToUpload"
                class="hidden"
                accept="image/*"
                @change="display"
              />
              <div
                class="text-center w-5/12 sm:w-5/12 md:w-4/12 lg:w-4/12 xl:w-4/12 mr-auto ml-auto"
              >
                <img
                  class="cursor-pointer img-articulo"
                  v-if="this.form.imagen"
                  :src="form.imagen"
                  @click="imagen()"
                />
                <img
                  class="cursor-pointer img-articulo"
                  v-else
                  :src="require('@assets/images/no-image-icon.png')"
                  @click="imagen()"
                />
              </div>

              <div
                v-if="verQuitarImagen"
                :class="[
                  'w-full sm:w-12/12 px-2 mr-auto ml-auto mt-4',
                  verModificar
                    ? ' md:w-6/12 lg:w-6/12 xl:w-6/12'
                    : ' md:w-5/12 lg:w-5/12 xl:w-5/12'
                ]"
              >
                <vs-button class="w-full" color="primary" size="small" @click="quitar">
                  <span class="font-medium text-base">Dejar imagen anterior</span>
                </vs-button>
              </div>
            </div>
          </div>

          <!--fin de contenido del plan funerario-->
        </div>

        <div class="w-full sm:w-12/12 md:w-7/12 lg:w-7/12 xl:w-7/12 px-2">
          <div class="float-left pb-5 px-2">
            <img width="36px" src="@assets/images/stock.svg" />
            <h3
              class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms capitalize"
            >Información del artículo o servicio</h3>
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
                ref="descripcion"
                name="descripcion"
                data-vv-as=" "
                v-validate.disabled="'required'"
                maxlength="100"
                type="text"
                class="w-full pb-1 pt-1"
                placeholder="Ej. Ataúd metálico"
                v-model.trim="form.descripcion"
              />
              <div>
                <span class="text-danger text-sm">{{ errors.first("descripcion") }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="text-danger text-sm"
                  v-if="this.errores.descripcion"
                >{{ errores.descripcion[0] }}</span>
              </div>
            </div>
            <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
              <label class="text-sm opacity-75 font-bold">
                Descripción (Inglés)
                <span class="text-danger text-sm">(*)</span>
              </label>

              <vs-input
                ref="descripcion_ingles"
                name="descripcion_ingles"
                data-vv-as=" "
                v-validate.disabled="'required'"
                maxlength="100"
                type="text"
                class="w-full pb-1 pt-1"
                placeholder="Ej. Metalic coffin"
                v-model.trim="form.descripcion_ingles"
              />
              <div>
                <span class="text-danger text-sm">{{ errors.first("descripcion_ingles") }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="text-danger text-sm"
                  v-if="this.errores.descripcion_ingles"
                >{{ errores.descripcion_ingles[0] }}</span>
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
                v-validate:tipo_articulo_validacion_computed.immediate="
                  'required'
                "
                name="tipo_articulo"
                data-vv-as=" "
              >
                <div slot="no-options">Seleccione 1</div>
              </v-select>
              <div>
                <span class="mensaje-requerido">{{ errors.first("tipo_articulo") }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="mensaje-requerido"
                  v-if="this.errores['tipo_articulo.value']"
                >{{ errores["tipo_articulo.value"][0] }}</span>
              </div>
            </div>
            <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
              <label class="text-sm opacity-75 font-bold">
                <span>Código de barras</span>
                <span class="texto-importante">(*)</span>
              </label>

              <vs-input
                v-validate:requiere_codigo_barras.disabled="'required'"
                name="codigo_barras"
                data-vv-as=" "
                type="text"
                class="w-full py-1 cursor-pointer"
                placeholder="Ej. 495394038130"
                v-model="form.codigo_barras"
                maxlength="25"
                ref="codigo_barras"
                :disabled="!requiere_codigo_barras"
              />
              <div>
                <span class="mensaje-requerido">{{ errors.first("codigo_barras") }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="mensaje-requerido"
                  v-if="this.errores.codigo_barras"
                >{{ errores.codigo_barras[0] }}</span>
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
                v-validate:departamento_validacion_computed.immediate="
                  'required'
                "
                name="plan_validacion"
                data-vv-as=" "
              >
                <div slot="no-options">Seleccione 1</div>
              </v-select>
              <div>
                <span class="mensaje-requerido">{{ errors.first("plan_validacion") }}</span>
              </div>
              <div class="mt-2">
                <span
                  class="mensaje-requerido"
                  v-if="this.errores['plan_funerario.value']"
                >{{ errores["plan_funerario.value"][0] }}</span>
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
                v-validate:categoria_validacion_computed.immediate="'required'"
                name="categoria"
                data-vv-as=" "
              >
                <div slot="no-options">Seleccione 1</div>
              </v-select>
              <div>
                <span class="mensaje-requerido">
                  {{
                  errors.first("categoria")
                  }}
                </span>
              </div>
              <div class="mt-2">
                <span
                  class="mensaje-requerido"
                  v-if="this.errores['categoria.value']"
                >{{ errores["categoria.value"][0] }}</span>
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
              >Unidades de medida del artículo o servicio</h3>
            </div>
          </div>

          <div class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Unidad de Servicio o Producto SAT</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="unidades_sat"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.unidad_sat"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              v-validate:unidad_sat_validacion_computed.immediate="'required'"
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
                v-if="this.errores['unidad_sat.value']"
              >{{ errores["unidad_sat.value"][0] }}</span>
            </div>
          </div>

          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              Mínimo Inventario
              <span class="texto-importante">(*)</span>
            </label>
            <vs-input
              v-validate.disabled="'required|min_value:1|integer'"
              name="minimo_inventario"
              data-vv-as=" "
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Cantidad mínima de inventario"
              v-model="form.minimo_inventario"
              maxlength="5"
              :disabled="this.form.tipo_articulo.value == 2"
            />
            <div>
              <span class="mensaje-requerido">{{ errors.first("minimo_inventario") }}</span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores.minimo_inventario"
              >{{ errores.minimo_inventario[0] }}</span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              Máximo Inventario
              <span class="texto-importante">(*)</span>
            </label>
            <vs-input
              v-validate.disabled="
                'required|integer|min_value:' + this.form.minimo_inventario
              "
              name="maximo_inventario"
              data-vv-as=" "
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Cantidad máxima de inventario"
              v-model="form.maximo_inventario"
              maxlength="12"
              :disabled="this.form.tipo_articulo.value == 2"
            />
            <div>
              <span class="mensaje-requerido">{{ errors.first("maximo_inventario") }}</span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores.maximo_inventario"
              >{{ errores.maximo_inventario[0] }}</span>
            </div>
          </div>

          <div class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Grava IVA</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="opciones_sino"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.opcion_iva"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="opcion_iva"
              data-vv-as=" "
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="mensaje-requerido">
                {{
                errors.first("opcion_iva")
                }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores['opcion_iva.value']"
              >{{ errores["opcion_iva.value"][0] }}</span>
            </div>
          </div>

          <div hidden class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Manejar Caducidades</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="opciones_sino"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.opcion_caducidad"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="opcion_caducidad"
              data-vv-as=" "
              :disabled="this.form.tipo_articulo.value != 1 ? true : false"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="mensaje-requerido">
                {{
                errors.first("opcion_caducidad")
                }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores['opcion_caducidad.value']"
              >{{ errores["opcion_caducidad.value"][0] }}</span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              $ Costo Neto Compra
              <span class="texto-importante">(*)</span>
            </label>
            <vs-input
              v-validate.disabled="'required|decimal:2|min_value:1'"
              name="costo_compra"
              data-vv-as=" "
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Costo neto de compra"
              v-model="form.costo_compra"
              maxlength="12"
            />
            <div>
              <span class="mensaje-requerido">{{ errors.first("costo_compra") }}</span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores.costo_compra"
              >{{ errores.costo_compra[0] }}</span>
            </div>
          </div>
          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              $ Costo Neto Venta
              <span class="texto-importante">(*)</span>
            </label>
            <vs-input
              v-validate.disabled="
                'required|decimal:2|min_value:' + this.form.costo_compra
              "
              name="costo_venta"
              data-vv-as=" "
              type="text"
              class="w-full pb-1 pt-1"
              placeholder="Costo neto de venta"
              v-model="form.costo_venta"
              maxlength="16"
            />
            <div>
              <span class="mensaje-requerido">{{ errors.first("costo_venta") }}</span>
            </div>
            <div class="mt-2">
              <span
                class="mensaje-requerido"
                v-if="this.errores.costo_venta"
              >{{ errores.costo_venta[0] }}</span>
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
      <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 pt-6 pb-10 px-2 mr-auto ml-auto">
        <vs-button class="w-full" @click="acceptAlert()" color="primary">
          <img width="25px" class="cursor-pointer" size="small" src="@assets/images/save.svg" />
          <span class="texto-btn" v-if="this.getTipoformulario == 'agregar'">Guardar Datos</span>
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
      :accion="'He revisado la información y quiero registrar a este artículo'"
      :confirmarButton="'Guardar Artículo'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import vSelect from "vue-select";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import inventario from "@services/inventario";
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
    id_articulo: {
      type: Number,
      required: false,
      default: 0
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      this.limpiarValidation();
      if (newValue == true) {
        this.imagen_anterior = require("@assets/images/no-image-icon.png");
        //cargo nacionalidades
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() =>
          this.$refs["nombre_comercial"].$el.querySelector("input").focus()
        );

        (async () => {
          await this.get_sat_unidades();
          await this.get_categorias();
          if (this.getTipoformulario == "modificar") {
            this.title = "Modificar Artículo/Servicio del Inventario";
            /**se cargan los datos al formulario */
            await this.get_articulo_by_id(this.get_proveedor_id);
          } else {
            this.form.opcion_caducidad = this.opciones_sino[1];
            this.title = "Registrar Nuevo Artículo/Servicio al Inventario";
          }
        })();
      }
    },
    "form.departamento": function(newValue, oldValue) {
      if (newValue.value != "") {
        if (newValue.categorias) {
          this.categorias = [];
          this.categorias.push({
            value: "",
            label: "Seleccione 1"
          });
          newValue.categorias.forEach(element => {
            this.categorias.push({
              value: element.id,
              label: element.categoria
            });
          });

          if (this.categorias.length > 1) {
            this.form.categoria = this.categorias[1];
          } else {
            this.form.categoria = this.categorias[0];
          }
        }
      }
    },
    "form.tipo_articulo": function(newValue, oldValue) {
      if (newValue.value != "") {
        if (newValue.value != 1) {
          this.form.opcion_caducidad = { value: 0, label: "NO" };
        }
        if (newValue.value == 2) {
          /**servicio */
          this.form.minimo_inventario = 1;
          this.form.maximo_inventario = 1;
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
    get_articulo_id: {
      get() {
        return this.id_articulo;
      },
      set(newValue) {
        return newValue;
      }
    },
    tipo_articulo_validacion_computed: function() {
      return this.form.tipo_articulo.value;
    },
    departamento_validacion_computed: function() {
      return this.form.departamento.value;
    },
    categoria_validacion_computed: function() {
      return this.form.categoria.value;
    },
    unidad_compra_validacion_computed: function() {
      return this.form.unidad_compra.value;
    },
    unidad_venta_validacion_computed: function() {
      return this.form.unidad_venta.value;
    },
    unidad_sat_validacion_computed: function() {
      return this.form.unidad_sat.value;
    },
    verQuitarImagen: function() {
      if (this.getTipoformulario == "agregar") {
        if (
          this.form.imagen != "" &&
          this.form.imagen != this.imagen_anterior
        ) {
          return true;
        } else {
          return false;
        }
      } else {
        /**es modificar */
        if (
          this.form.imagen != "" &&
          this.form.imagen != this.datos_articulo.imagen
        ) {
          return true;
        } else {
          return false;
        }
      }
    },
    requiere_codigo_barras: function() {
      if (this.form.tipo_articulo.value != 2) {
        return true;
      } else {
        return false;
      }
    }
  },
  data() {
    return {
      imagen_anterior: "",
      title: "",
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      operConfirmar: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Modificar Artículo",
      datos_articulo: [],
      /**form */
      datos_departamentos: [],
      tipo_articulos: [
        {
          value: "1",
          label: "Artículo"
        },
        {
          value: "2",
          label: "Servicio"
        }
        /*
        {
          value: "3",
          label: "Equipo Rentable"
        }*/
      ],

      departamentos: [
        {
          value: "",
          label: "Seleccione 1",
          categorias: []
        }
      ],
      categorias: [
        {
          value: "",
          label: "Seleccione 1"
        }
      ],
      unidades_sat: [
        {
          value: "",
          label: "Seleccione 1"
        }
      ],
      opciones_sino: [
        {
          value: 1,
          label: "SI"
        },
        {
          value: 0,
          label: "NO"
        }
      ],
      form: {
        imagen: "",
        tipo_articulo: {
          value: "1",
          label: "Artículo"
        },
        departamento: {
          value: "",
          label: "Seleccione 1",
          categorias: []
        },
        categoria: {
          value: "",
          label: "Seleccione 1"
        },
        unidad_sat: {
          value: "",
          label: "Seleccione 1"
        },
        opcion_iva: {
          value: "1",
          label: "SI"
        },
        opcion_caducidad: {
          value: 0,
          label: "NO"
        },
        descripcion: "",
        descripcion_ingles: "",
        codigo_barras: "",
        factor: 1,
        minimo_inventario: 1,
        maximo_inventario: 1,
        costo_compra: "",
        costo_venta: "",
        nota: "",
        /**form */
        /**en caso de modificar */
        id_articulo_modificar: 0
      },
      errores: []
    };
  },
  methods: {
    async get_categorias() {
      this.$vs.loading();
      try {
        let res = await inventario.get_categorias();
        this.departamentos = [];
        this.departamentos.push({
          label: "Seleccione 1",
          value: "",
          categorias: []
        });

        res.data.forEach(element => {
          this.departamentos.push({
            label: element.departamento,
            value: element.id,
            categorias: element.categorias
          });
        });

        if (this.getTipoformulario == "agregar") {
          if (res.data.length > 0) {
            this.form.departamento = this.departamentos[1];
          } else {
            this.form.departamento = this.departamentos[0];
          }
        }
        this.$vs.loading.close();
      } catch (error) {
        /**error al cargar */
        this.$vs.notify({
          title: "Error",
          text:
            "Ha ocurrido un error al tratar de cargar las cateogrías, por favor reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "9000"
        });
        this.$vs.loading.close();
        this.cerrarVentana();
      }
    },

    async get_sat_unidades() {
      try {
        let res = await inventario.get_sat_unidades();
        //le agrego todos las unidades
        this.unidades_sat = [];
        this.unidades_sat.push({ label: "Seleccione 1", value: "" });
        res.data.forEach(element => {
          this.unidades_sat.push({
            label: element.descripcion + "(" + element.clave + ")",
            value: element.id
          });
        });
        if (this.getTipoformulario == "agregar") {
          if (this.unidades_sat.length > 1) {
            this.form.unidad_sat = this.unidades_sat[1];
          } else {
            this.form.unidad_sat = this.unidades_sat[0];
          }
        }
      } catch (error) {
        /**error al cargar */
        this.$vs.notify({
          title: "Error",
          text:
            "Ha ocurrido un error al tratar de cargar el catálogo de unidades, por favor reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "9000"
        });
        this.cerrarVentana();
      }
    },

    quitar() {
      if (this.getTipoformulario == "agregar") {
        this.form.imagen = this.imagen_anterior;
      } else {
        this.form.imagen = this.datos_articulo.imagen;
      }
    },
    imagen() {
      this.$refs.fileImage.click();
    },
    display: function(event) {
      // Reference to the DOM input element
      var input = event.target;
      // Ensure that you have a file before attempting to read it
      if (input.files && input.files[0]) {
        if (
          input.files[0].type == "image/png" ||
          input.files[0].type == "image/jpeg" ||
          input.files[0].type == "image/jpg"
        ) {
          // create a new FileReader to read this image and convert to base64 format
          var reader = new FileReader();
          // Define a callback function to run, when FileReader finishes its job
          reader.onload = e => {
            // Note: arrow function used here, so that "this.imageData" refers to the imageData of Vue component
            // Read image as base64 and set to imageData
            this.form.imagen = e.target.result;
          };
          // Start the reader job - read file as a data url (base64 format)
          reader.readAsDataURL(input.files[0]);
        } else {
          /**no acepta el formato */
          this.$vs.notify({
            title: "Actualizar imagen",
            text: "Error, debe seleccionar una imagen (.jpeg, .png ó .jpg).",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 4000
          });
          return;
        }
      }
    },
    async get_articulo_by_id() {
      this.$vs.loading();
      try {
        let res = await inventario.get_articulo_by_id(this.get_articulo_id);
        let datos = res.data[0];
        this.datos_articulo = datos;
        //actualizo los datos en el formulario
        this.form.imagen = datos.imagen;
        /**seleccionando el departamentos */
        this.tipo_articulos.forEach(tipo => {
          if (tipo.value == datos.tipo_articulos_id) {
            this.form.tipo_articulo = tipo;
            return;
          }
        });

        await this.departamentos.forEach(departamento => {
          departamento.categorias.forEach(categoria => {
            if (categoria.id == datos.categorias_id) {
              this.form.departamento = departamento;
              return;
            }
          });
        });
        this.categorias.forEach(categoria => {
          if (categoria.value == datos.categorias_id) {
            this.form.categoria = categoria;
            return;
          }
        });
        this.unidades_sat.forEach(unidad => {
          if (unidad.value == datos.sat_productos_servicios_id) {
            this.form.unidad_sat = unidad;
            return;
          }
        });
        this.opciones_sino.forEach(opcion => {
          if (opcion.value == datos.grava_iva_b) {
            this.form.opcion_iva = opcion;
            return;
          }
        });
        this.opciones_sino.forEach(opcion => {
          if (opcion.value == datos.caduca_b) {
            this.form.opcion_caducidad = opcion;
            return;
          }
        });
        this.form.descripcion = datos.descripcion;
        this.form.descripcion_ingles = datos.descripcion_ingles;
        this.form.codigo_barras = datos.codigo_barras;
        this.form.factor = datos.factor;
        this.form.minimo_inventario = datos.minimo;
        this.form.maximo_inventario = datos.maximo;
        this.form.costo_compra = datos.precio_compra;
        this.form.costo_venta = datos.precio_venta;
        this.form.nota = datos.nota;
        /**en caso de modificar */
        this.form.id_articulo_modificar = datos.id;
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
              title: "Guardar Artículo",
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
                this.callBackConfirmarAceptar = await this.guardar_articulo;
                this.openConfirmarAceptar = true;
              } else {
                /**modificar, se valida con password */
                this.form.id_articulo_modificar = this.get_articulo_id;
                this.callback = await this.modificar_articulo;
                this.operConfirmar = true;
              }
            })();
          }
        })
        .catch(() => {});
    },
    async guardar_articulo() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await inventario.guardar_articulo(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Registro de Artículos",
            text: "Se ha guardado el artículo correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000
          });
          this.$emit("retornar_id", res.data);
          this.cerrarVentana();
        } else {
          this.$vs.notify({
            title: "Registro de Artículos",
            text: "Error al guardar el artículo, por favor reintente.",
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
              title: "Registro de Artículos",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 12000
            });
          } else if (err.response.status == 409) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Registro de Artículos",
              text: err.response.data.error,
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 15000
            });
          }
        }
        this.$vs.loading.close();
      }
    },

    async modificar_articulo() {
      //aqui mando modoificar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await inventario.modificar_articulo(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Modificación de Artículos",
            text: "Se modificó el artículo correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000
          });
          this.$emit("retornar_id", res.data);
        } else {
          this.$vs.notify({
            title: "Modificación de Artículos",
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
              title: "Modificación de Artículos",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 5000
            });
          } else if (err.response.status == 409) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Registro de Artículos",
              text: err.response.data.error,
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 15000
            });
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
      this.form.imagen = "";
      this.form.tipo_articulo = {
        value: "1",
        label: "Artículo"
      };
      this.form.departamento = {
        value: "",
        label: "Seleccione 1",
        categorias: []
      };
      this.form.categoria = {
        value: "",
        label: "Seleccione 1"
      };
      this.form.unidad_sat = {
        value: "",
        label: "Seleccione 1"
      };
      this.form.opcion_iva = {
        value: "1",
        label: "SI"
      };
      this.form.opcion_caducidad = {
        value: 0,
        label: "NO"
      };
      this.form.descripcion = "";
      this.form.descripcion_ingles = "";
      this.form.codigo_barras = "";
      this.form.factor = 1;
      this.form.minimo_inventario = 1;
      this.form.maximo_inventario = 1;
      this.form.costo_compra = "";
      this.form.costo_venta = "";
      this.form.nota = "";
      /**en caso de modificar */
      this.form.id_articulo_modificar = 0;
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
