<template >
  <div class="centerx">
    <vs-popup
      fullscreen
      :title="HeaderNombre"
      class="pdfs_modulos bg-grey-light h-screen"
      :active.sync="showChecker"
      @close="cancel"
    >
      <!-- <img style="width:100px;" src="@assets/images/pdf.svg" alt />-->
      <div class="flex flex-wrap">
        <div class="w-full sm:w-5/5 md:w-2/5 lg:w-1/5 xl:w-1/5 px-2 mb-8">
          <!--datos de los reportes-->
          <h1 class="text-base capitalize font-semibold text-black">reportes disponibles</h1>
          <div class="flex flex-wrap mt-8">
            <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12">
              <label class="text-sm opacity-75 font-semibold">
                <span>Seleccione un Reporte:</span>
              </label>
              <v-select
                :options="reportesDisponible"
                v-model="reporteSeleccionado"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                class="pb-1 pt-1"
                name="vendedor"
                data-vv-as=" "
              >
                <div slot="no-options">Seleccione un reporte</div>
              </v-select>
            </div>
            <!--
            <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 my-5">
              <div class="descargar cursor-pointer text-info">
                Descargar documento
                <img style="width:30px;" src="@assets/images/pdf.svg" alt />
              </div>
            </div>
            -->

            <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mt-10">
              <label class="text-sm opacity-75 font-medium">Enviar por Correo</label>
              <vs-input
                name="email"
                data-vv-as=" "
                data-vv-validate-on="blur"
                v-validate="'email'"
                maxlength="75"
                type="email"
                class="w-full pb-1 pt-1"
                placeholder="Ingrese el email"
                v-model="request_datos.email_address"
              />
              <div>
                <span class="text-danger text-sm">{{ errors.first('email') }}</span>
              </div>
              <div class="mt-2">
                <span class="text-danger text-sm" v-if="this.errores.email">{{errores.email[0]}}</span>
              </div>
            </div>

            <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-6">
              <vs-button
                icon-pack="feather"
                icon="icon-mail"
                color="success"
                class="w-full my-4"
                @click="acceptAlert()"
              >Enviar</vs-button>
            </div>
          </div>
          <!--fin de datos de los reportes-->
        </div>
        <div class="w-full sm:w-5/5 md:w-3/5 lg:w-4/5 xl:w-4/5 px-2">
          <div class="pdf_layout h-screen bg-grey-light">
            <div class="flex inline-block h-screen bg-grey-light">
              <span
                v-if="pdf_iframe_source==''"
                class="mb-auto mt-auto mr-auto ml-auto bg-blue-200"
              >Debe seleccionar un reporte para visualizar.</span>

              <iframe v-else :src="pdf_iframe_source" class="iframe_viewer"></iframe>
            </div>
          </div>
        </div>
      </div>
      <ConfirmarAceptar
        :show="openConfirmarAceptar"
        :callback-on-success="callBackConfirmar"
        @closeVerificar="openConfirmarAceptar=false"
        :accion="'Enviar el documento por correo'"
        :confirmarButton="'Enviar Documento'"
      ></ConfirmarAceptar>
    </vs-popup>
  </div>
</template>
<script>
import pdf from "@services/pdf";
import vSelect from "vue-select";
import ConfirmarAceptar from "../../../confirmarAceptar";
export default {
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == false) {
        this.pdf_iframe_source = "";
      } else {
        this.request_datos.request_parent = [];
        this.request_datos.request_parent.push(this.Request);
      }
    },
    listadereportes: function(newValue, oldValue) {
      if (newValue.length > 0) {
        this.reportesDisponible = [];
        newValue.forEach(element => {
          this.reportesDisponible.push({
            label: element.nombre,
            value: element.url
          });
        });
        this.reporteSeleccionado = this.reportesDisponible[0];
      }
    },
    reporteSeleccionado: function(newValue, oldValue) {
      this.request_datos.email_send = false;
      this.get_pdf();
    }
  },
  props: {
    show: {
      type: Boolean,
      required: true
    },
    header: {
      type: String,
      required: true
    },
    listadereportes: {
      type: Array,
      required: true,
      default: {}
    },
    request: {
      type: Object,
      required: true,
      default: []
    }
  },
  components: {
    "v-select": vSelect,
    ConfirmarAceptar
  },

  data() {
    return {
      openConfirmarAceptar: false,
      callBackConfirmar: Function,
      reportesDisponible: [],
      reporteSeleccionado: {},
      pdf_iframe_source: "",
      errores: [],
      request_datos: {
        email_address: "",
        email_send: false,
        request_parent: []
      }
    };
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
    HeaderNombre: {
      get() {
        return this.header;
      },
      set(newValue) {
        return newValue;
      }
    },
    reportes: {
      get() {
        return this.listadereportes;
      },
      set(newValue) {
        return newValue;
      }
    },
    Request: {
      get() {
        return this.request;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  methods: {
    cancel() {
      this.$emit("closeReportes");
    },

    get_pdf() {
      this.$vs.loading();
      pdf
        .get_pdf(this.reporteSeleccionado.value, this.request_datos)

        .then(res => {
          this.$vs.loading.close();
          const file = new Blob([res.data], { type: "application/pdf" });
          this.pdf_iframe_source = URL.createObjectURL(file);
          if (res.data.type != "application/pdf") {
            this.pdf_iframe_source = "";
          }
        })
        .catch(err => {
          this.pdf_iframe_source = "";
          this.pdf_iframe_source = "";
          this.$vs.loading.close();
          if (err.response) {
            if (err.response.status == 403) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Permiso denegado",
                text:
                  "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 4000
              });
            } else if (err.response.status == 422) {
              /**error de validacion */
              this.errores = JSON.parse(err.response);
            }
          }
        });
    },

    acceptAlert() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$vs.notify({
              title: "Error",
              text: "Verifique que capturo un email",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000"
            });
            return;
          } else {
            if (this.pdf_iframe_source != "") {
              if (this.request_datos.email_address != "") {
                this.openConfirmarAceptar = true;
                this.callBackConfirmar = this.send_pdf;
              }
            }
          }
        })
        .catch(() => {});
    },

    /**enviar pdf por mail */
    send_pdf() {
      this.request_datos.email_send = true;
      this.$vs.loading();
      pdf
        .send_pdf(this.reporteSeleccionado.value, this.request_datos)
        /**el uno indica que se va enviar el email */

        .then(res => {
          this.$vs.loading.close();
          if (res.data == 1) {
            this.$vs.notify({
              title: "Enviar documento por correo",
              text: "Se ha envaido el correo.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 6000
            });
            this.request_datos.email_address = "";
          } else {
            this.$vs.notify({
              title: "Enviar documento por correo",
              text: "Error al enviar el documento, por favor reintente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 6000
            });
          }
        })
        .catch(err => {
          this.$vs.loading.close();
          if (err.response) {
            if (err.response.status == 403) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Permiso denegado",
                text:
                  "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 4000
              });
            } else if (err.response.status == 422) {
              /**error de validacion */
              this.errores = err.response.data.error;
            }
          }
        });
    }
  },
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", e => {
      /*if (e.keyCode === 27) {
        if (this.showChecker) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.cancel();
        }
      }*/
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