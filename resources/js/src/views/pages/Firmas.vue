<template >
  <div class="centerx">
    <vs-popup
      title="Firma de Documentos"
      class="forms-popup popup-50"
      fullscreen
      :active.sync="showChecker"
      ref="formulario"
    >
      <!-- <img style="width:100px;" src="@assets/images/pdf.svg" alt />-->
      <div class="flex flex-wrap">
        <div class="w-full px-2">
          <div class="form-group">
            <div class="title-form-group">{{ HeaderNombre }}</div>
            <div class="form-group-content">
              <div class="flex flex-wrap">
                <div
                  class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 input-text"
                >
                  <label class=""> Seleccione 1 </label>
                  <v-select
                    :options="firmasDisponibles"
                    v-model="firmaSeleccionada"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    class="w-full"
                    name="firma"
                    data-vv-as=" "
                  >
                    <div slot="no-options">Seleccione el Firmador</div>
                  </v-select>
                </div>
                {{ documento_id }}
                {{ firmasDisponibles }}
              </div>
            </div>
          </div>
          <!--datos de los reportes-->
          <!--fin de datos de los reportes-->
        </div>
      </div>
      <ConfirmarAceptar
        :show="openConfirmarAceptar"
        :callback-on-success="callBackConfirmar"
        @closeVerificar="openConfirmarAceptar = false"
        :accion="'Enviar el documento por correo'"
        :confirmarButton="'Enviar Documento'"
      ></ConfirmarAceptar>
    </vs-popup>
  </div>
</template>
<script>
import firmas from "@services/firmas";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
export default {
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == false) {
      } else {
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancel();
        };
        /**cargo el reporte que me llega y sus respectivas areas a firmar */
        (async () => {
          await this.get_areas_firmar();
        })();
      }
    },
    id_documento: function (newValue, oldValue) {},
    firmaSeleccionada: function (newValue, oldValue) {
      (async () => {
        // await this.get_areas_firmar();
      })();
    },
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    header: {
      type: String,
      required: true,
    },
    id_documento: {
      type: Array,
      required: true,
      default: {},
    },
  },
  components: {
    "v-select": vSelect,
    ConfirmarAceptar,
  },

  data() {
    return {
      openConfirmarAceptar: false,
      callBackConfirmar: Function,
      firmasDisponibles: [],
      firmaSeleccionada: {},
      errores: [],
    };
  },
  computed: {
    showChecker: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
    HeaderNombre: {
      get() {
        return this.header;
      },
      set(newValue) {
        return newValue;
      },
    },
    documento_id: {
      get() {
        return this.id_documento;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  methods: {
    cancel() {
      this.$emit("closeFirmas");
    },
    async get_areas_firmar() {
      this.$vs.loading();
      try {
        let res = await firmas.get_areas_firmar(this.id_documento);
        let data = [];
        if (res.data.length > 0) {
          data = res.data[0];
        }
        this.firmasDisponibles = [];
        if (res.data.length > 0) {
          if (data.areas.length > 0) {
            data.areas.forEach((element) => {
              this.firmasDisponibles.push({
                label: element.area,
                value: element.id,
              });
            });
          }
        } else {
          this.firmasDisponibles.push({
            label: "Seleccione 1",
            value: "",
          });
        }
        this.firmaSeleccionada = this.firmasDisponibles[0];
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
          } else if (err.response.status == 422) {
            /**error de validacion */
            this.errores = JSON.parse(err.response);
          } else if (err.response.status == 409) {
            //este error es por alguna condicion que el contrano no cumple para modificar
            //la propiedad esa ya ha sido vendido
            this.$vs.notify({
              title: "Ver Reportes",
              text: "Ha ocurrido un error, por favor reintente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 30000,
            });
          }
        }

        this.cancel();
      }
    },

    acceptAlert() {
      this.$validator
        .validateAll()
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Error",
              text: "Verifique que capturó un email y un destinatario",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000",
            });
            return;
          } else {
          }
        })
        .catch(() => {});
    },

    /**enviar pdf por mail */
  },
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", (e) => {
      /*if (e.keyCode === 27) {
        if (this.showChecker) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.cancel();
        }
      }*/
    });
  },
};
</script>
<style  scoped>
</style>