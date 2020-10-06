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
