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
              Seleccione al Contratante
              <span class="texto-importante">(*)</span>
            </label>

            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-1/12 lg:w-1/12 xl:w-1/12 px-2">
                <img
                  v-if="form.id_cliente == ''"
                  width="46px"
                  class="cursor-pointer p-2"
                  src="@assets/images/search.svg"
                  title="Buscar Cliente"
                />
                <img
                  v-else
                  width="46px"
                  class="cursor-pointer p-2"
                  src="@assets/images/minus.svg"
                />
              </div>
              <div
                class="w-full sm:w-12/12 md:w-11/12 lg:w-11/12 xl:w-11/12 px-2"
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
              </div>
            </div>
          </div>

          <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
            <label class="text-sm opacity-75 font-bold">
              <span>Tipo de RFC</span>
              <span class="texto-importante">(*)</span>
            </label>
            <v-select
              :options="sino"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="form.plan_funerario_futuro_b"
              class="mb-4 sm:mb-0 pb-1 pt-1"
              name="plan_funerario_futuro_b"
            >
              <div slot="no-options">Seleccione 1</div>
            </v-select>
            <div>
              <span class="text-danger">
                {{ errors.first("plan_funerario_futuro_b") }}
              </span>
            </div>
            <div class="mt-2">
              <span
                class="text-danger"
                v-if="this.errores['plan_funerario_futuro_b.value']"
                >{{ errores["plan_funerario_futuro_b.value"][0] }}</span
              >
            </div>
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
          if (this.getTipoformulario == "agregar") {
          } else {
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

      /**control del popup de confirmar accion */
      openConfirmarSinPassword: false,
      botonConfirmarSinPassword: "",
      accionConfirmarSinPassword: "",
      callBackConfirmar: Function,

      /**DATOS DEL FORMULARIO */
      form: {
        id_cliente: "",
      },
      errores: [],
    };
  },
  methods: {
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
