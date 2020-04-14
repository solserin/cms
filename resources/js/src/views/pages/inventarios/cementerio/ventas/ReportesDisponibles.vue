<template >
  <div class="centerx">
    <vs-popup
      fullscreen
      title="consultar reporte de venta"
      class="pdfs_modulos bg-grey-light h-screen"
      :active.sync="showChecker"
      @close="cancel"
    >
      <!-- <img style="width:100px;" src="@assets/images/pdf.svg" alt />-->
      <div class="flex flex-wrap">
        <div class="w-full px-2">
          <div class="flex flex-wrap">
            <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12">f</div>
            <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12">f</div>
          </div>
        </div>
        <div class="w-full mt-5 px-2">
          <div class="mt-2">
            <iframe src class="iframe_viewer"></iframe>
          </div>
        </div>
      </div>

      <!--<div class="flex flex-wrap mt-2">
        <div class="w-full sm:w-6/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 mt-5">
          <div class="mt-2">
            <vs-button class="float-right mr-2" type="border" @click="cancel">(Esc) Cancelar</vs-button>
          </div>
        </div>
      </div>-->
    </vs-popup>
  </div>
</template>
<script>
import vSelect from "vue-select";
export default {
  props: {
    show: {
      type: Boolean,
      required: true
    }
  },
  components: {
    "v-select": vSelect
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
    }
  },
  methods: {
    cancel() {
      this.$emit("closeReportes");
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
<style  scoped>
</style>