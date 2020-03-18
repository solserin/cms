<template>
  <div>
    <vs-popup
      fullscreen
      close="cancelar"
      title="DISTRIBUCIÓN DE PROPIEDADES"
      :active.sync="ver"
      button-close-hidden
    >
      <!--ESTRUCTURA PARA PROPIEDADES TIPO UNIPLEX ID 1-->
      <div v-if="datosEstructura[0]">
        <div v-if="datosEstructura[0].tipo_propiedades_id==1">
          <div class="vx-col w-full">
            <vs-table
              :sst="true"
              @search="handleSearch"
              @change-page="handleChangePage"
              @sort="handleSort"
              :data="datosEstructura"
              stripe
              noDataText="0 Resultados"
            >
              <template slot="header">
                <h3 class="pb-5 text-primary">
                  <feather-icon icon="CompassIcon" class="mr-2" svgClasses="w-5 h-5" />
                  <span class="leading-none font-medium">
                    <strong>
                      UNIPLEX - MODULO
                      {{datosEstructura[0].propiedad_indicador}}
                    </strong>
                  </span>
                </h3>
              </template>
              <template slot="thead">
                <vs-th>MÓDULO</vs-th>
                <vs-th>STATUS</vs-th>
                <vs-th>TITULAR</vs-th>
                <vs-th>SERVICIOS</vs-th>
              </template>

              <template slot-scope="{data}">
                <vs-tr :key="tr" v-for="tr in (datosEstructura[0].filas)">
                  <vs-td>{{datosEstructura[0].propiedad_indicador +' '+ tr}}</vs-td>
                  <vs-td>
                    <span class="flex items-center px-2 py-1 rounded">
                      <div class="h-3 w-3 rounded-full mr-2" :class="'bg-success'"></div>Disponible
                    </span>
                  </vs-td>
                  <vs-td>d</vs-td>
                  <vs-td>
                    <div class="flex flex-start">
                      <vs-button
                        title="Activar"
                        icon-pack="feather"
                        size="large"
                        icon="icon-shield"
                        color="success"
                        type="flat"
                      ></vs-button>
                    </div>
                  </vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>
        </div>
        <div v-if="datosEstructura[0].tipo_propiedades_id==2">
          <div class="vx-col w-full">
            <div class="flex items-end">
              <feather-icon icon="CompassIcon" class="mr-2" svgClasses="w-5 h-5" />
              <span class="leading-none font-medium">
                <strong>
                  DUPLEX - MODULO
                  {{datosEstructura[0].propiedad_indicador}}
                </strong>
              </span>
            </div>
          </div>
        </div>
        <div v-if="datosEstructura[0].tipo_propiedades_id==3">
          <div class="vx-col w-full">
            <div class="flex items-end">
              <feather-icon icon="CompassIcon" class="mr-2" svgClasses="w-5 h-5" />
              <span class="leading-none font-medium">
                <strong>
                  NICHOS - COLUMNA
                  {{datosEstructura[0].propiedad_indicador}}
                </strong>
              </span>
            </div>
          </div>
        </div>
        <div v-if="datosEstructura[0].tipo_propiedades_id==4">
          <div class="vx-col w-full">
            <div class="flex items-end">
              <feather-icon icon="CompassIcon" class="mr-2" svgClasses="w-5 h-5" />
              <span class="leading-none font-medium">
                <strong>
                  CUADRIPLEX - TERRAZA
                  {{datosEstructura[0].propiedad_indicador}}
                </strong>
              </span>
            </div>
          </div>
        </div>
      </div>
    </vs-popup>
  </div>
</template>

<script>
//get cementerio services
import cementerio from "@services/cementerio";
export default {
  props: {
    propiedad_id: {
      type: Number,
      required: true
    },
    show: {
      type: Boolean,
      required: true
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.get_estructura();
      } else {
        this.ver = false;
      }
    }
  },
  computed: {
    showPdf: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      }
    },
    datos: {
      get() {
        return this.propiedad_id;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  data() {
    return {
      ver: false,
      //datos para la estrucutura
      datosEstructura: []
    };
  },
  methods: {
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
    cancel() {
      this.datosEstructura = [];
      this.ver = false;
    },
    //funcion que obtiene los datos necesarios
    get_estructura() {
      this.$vs.loading();
      cementerio
        .getDistribucionById(this.datos)
        .then(res => {
          console.log((this.datosEstructura = res.data));
          this.datosEstructura = res.data;
          this.ver = true;
          this.$vs.loading.close();
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
      if (e.keyCode === 27) {
        if (this.showPdf) {
          //CIERRO EL pdf viewer AL PRESONAR ESC
          this.cancel();
          this.$emit("closeVisor");
        }
      }
    });
  }
};
</script>
<style lang="css" scoped>
iframe {
  display: block; /* iframes are inline by default */
  background: #000;
  border: none; /* Reset default border */
  height: 100vh; /* Viewport-relative units */
  width: 100%;
}
</style>
