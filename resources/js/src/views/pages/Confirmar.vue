<template >
  <div class="centerx">
    <vs-prompt
      type="confirm"
      title="¿Desea continuar?"
      class="confirmar"
      :active.sync="showChecker"
      buttons-hidden
    >
      <div class="text-center icono"></div>
      <div class="text-center seguro-mensaje mt-3">¿Seguro de continuar?</div>
      <div class="text-center seguro-texto mt-3">{{accionNombre}}</div>
      <div class="flex flex-wrap mt-2">
        <div class="w-full sm:w-6/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 mt-5">
          <div class="mt-2">
            <vs-button class="float-right mr-2" type="border" @click="cancel">(Esc) Cancelar</vs-button>
          </div>
        </div>
        <div class="w-full sm:w-6/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 mt-5">
          <div class="mt-2">
            <vs-button
              :color="confirmarColorTexto"
              class="float-left ml-2"
              @click="aceptar"
            >{{confirmarButtonTexto}}</vs-button>
          </div>
        </div>
      </div>
    </vs-prompt>
  </div>
</template>
<script>
export default {
  props: {
    show: {
      type: Boolean,
      required: true
    },
    callbackOnSuccess: {
      type: Function,
      required: true
    },
    accion: {
      type: String,
      required: true
    },
    confirmarButton: {
      type: String,
      default: "Aceptar"
    },
    confirmarColor: {
      type: String,
      default: "danger"
    }
  },

  data() {
    return {};
  },
  computed: {
    showChecker: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      }
    },
    accionNombre() {
      return this.accion;
    },
    confirmarButtonTexto() {
      return this.confirmarButton;
    },
    confirmarColorTexto() {
      return this.confirmarColor;
    }
  },
  methods: {
    aceptar() {
      this.callbackOnSuccess();
      this.cancel();
    },
    cancel() {
      this.$emit("closeVerificar");
    }
  },
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", e => {
      if (e.keyCode === 27) {
        if (this.showChecker) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.cancel();
        }
      }
    });

    /*document.body.addEventListener("keyup", e => {
      if (e.keyCode === 13) {
        if (this.showChecker) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.aceptar();
        }
      }
    });*/
  }
};
</script>