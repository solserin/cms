<template >
  <div class="centerx">
    <vs-popup
      class="reportes_show_list aet-popup"
      title="Documentos Disponibles de La Venta"
      :active.sync="showVentana"
      ref="lista_reportes"
    >
      <h3 class="text-base text-center pt-1 pb-6">
        <span class="uppercase bold">Seleccione el documento que necesita</span>
      </h3>
      <vs-tabs alignment="center" position="top" v-model="activeTab">
        <vs-tab label="DOCUMENTOS DEL CONTRATO" icon="supervisor_account"></vs-tab>
        <vs-tab label="LISTA DE PAGOS" icon="supervisor_account"></vs-tab>
      </vs-tabs>

      <div class="tab-content mt-3" v-show="activeTab==0">
        <div class="flex flex-wrap">
          <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center py-3">
            <img
              class="cursor-pointer"
              src="@assets/images/reportes/pdf_download.svg"
              @click="openReporte('Solicitud de venta','/inventarios/cementerio/documento_solicitud','')"
            />
            <h4 class="py-3 capitalize">hoja de solicitud</h4>
          </div>
          <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center py-3">
            <img
              class="cursor-pointer"
              src="@assets/images/reportes/pdf_download.svg"
              @click="openReporte('Convenio','/inventarios/cementerio/documento_convenio','')"
            />
            <h4 class="py-3 capitalize">convenio</h4>
          </div>
          <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center py-3">
            <img
              class="cursor-pointer"
              src="@assets/images/reportes/pdf_download.svg"
              @click="openReporte('Titulo de propiedad','/inventarios/cementerio/documento_titulo','')"
            />
            <h4 class="py-3 capitalize">título de propiedad</h4>
          </div>
          <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center py-3">
            <img
              class="cursor-pointer"
              src="@assets/images/reportes/pdf_download.svg"
              @click="openReporte('Estado de cuenta','/inventarios/cementerio/documento_estado_de_cuenta_cementerio','')"
            />
            <h4 class="py-3 capitalize">estado de cuenta</h4>
          </div>
        </div>
      </div>
      <div class="tab-content mt-1" v-show="activeTab==1">
        <vs-table
          @search="handleSearch"
          @change-page="handleChangePage"
          @sort="handleSort"
          :max-items="100"
          :data="ListaReportes"
          stripe
          noDataText="0 Resultados"
        >
          <template slot="thead">
            <vs-th>Documento</vs-th>
            <vs-th>Seleccionar</vs-th>
          </template>
          <template slot-scope="{data}">
            <vs-tr>
              <vs-td>
                <span class="font-semibold">1</span>
              </vs-td>
              <vs-td>ccc</vs-td>
              <template class="expand-user" slot="expand"></template>
            </vs-tr>
          </template>
        </vs-table>
      </div>
      <Reporteador
        :header="'consultar reporte de venta'"
        :show="openReportesLista"
        :listadereportes="ListaReportes"
        :request="request"
        @closeReportes="openReportesLista=false;"
      ></Reporteador>
    </vs-popup>
  </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import cementerio from "@services/cementerio";
export default {
  components: {
    Reporteador
  },
  props: {
    show: {
      type: Boolean,
      required: true
    },
    id_venta: {
      type: Number,
      required: true,
      default: 0
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.$refs["lista_reportes"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };
      }
    },
    id_venta: function(newValue, oldValue) {
      if (newValue > 0) {
        this.ListaReportes = [];
        let self = this;
        if (cementerio.cancel) {
          cementerio.cancel("Operation canceled by the user.");
        }
        this.$vs.loading();
        cementerio
          .get_venta_id(newValue)
          .then(res => {
            this.datosVenta = res.data;
            this.$vs.loading.close();
          })
          .catch(err => {
            this.$vs.loading.close();
            if (err.response) {
              if (err.response.status == 403) {
                this.$vs.notify({
                  title: "Permiso denegado",
                  text:
                    "Verifique sus permisos con el administrador del sistema.",
                  iconPack: "feather",
                  icon: "icon-alert-circle",
                  color: "warning",
                  time: 4000
                });
              }
            }
          });
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
    }
  },
  data() {
    return {
      datosVenta: [],
      activeTab: 0,
      ListaReportes: [],
      request: {
        venta_id: "",
        email: ""
      },
      openReportesLista: false
    };
  },
  methods: {
    cancelar() {
      this.activeTab = 0;
      this.$emit("closeListaReportes");
      return;
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
    openReporte(nombre_reporte = "", link = "", parametro = "") {
      this.ListaReportes = [];
      this.ListaReportes.push({
        nombre: nombre_reporte,
        url: link
      });
      //estado de cuenta
      this.request.email = this.datosVenta.cliente_email;
      this.request.venta_id = this.datosVenta.id;
      this.openReportesLista = true;
      this.$vs.loading.close();
    }
  },
  created() {},
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", e => {
      if (e.keyCode === 27) {
        if (this.showVentana) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.cancelar();
        }
      }
    });
  }
};
</script>