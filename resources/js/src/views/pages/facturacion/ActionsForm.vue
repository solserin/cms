<template>
  <div class="centerx">
    <vs-popup
      class="background-header-forms forms-popups-85 consultar_cfdi"
      close="cancelar"
      :title="'CONSULTA DE CFDI 3.3'"
      :active.sync="showVentana"
      ref="formulario"
    >
      <div class="cfdi-contenido">
        <vs-table stripe class="cfdis-table" :noDataText="' '" :sst="true">
          <template slot="header">
            <h3>Datos del Receptor</h3>
          </template>
          <template slot="thead">
            <vs-th>RFC</vs-th>
            <vs-th>CLIENTE</vs-th>
            <vs-th>RAZON SOCIAL</vs-th>
          </template>
          <tbody>
            <vs-tr>
              <vs-td>
                <span class="font-semibold uppercase">{{
                  cfdi.rfc_receptor
                }}</span>
              </vs-td>
              <vs-td>
                <span class="font-semibold uppercase">{{
                  cfdi.cliente_nombre
                }}</span>
              </vs-td>
              <vs-td>
                <span class="font-semibold uppercase">{{
                  cfdi.nombre_receptor
                }}</span>
              </vs-td>
            </vs-tr>
          </tbody>
        </vs-table>
        <vs-table stripe class="cfdis-table" :noDataText="' '" :sst="true">
          <template slot="header">
            <h3>Datos del CFDI</h3>
          </template>
          <template slot="thead">
            <vs-th>FOLIO</vs-th>
            <vs-th>UUID</vs-th>
            <vs-th>TIPO</vs-th>
            <vs-th>FECHA TIMBRADO</vs-th>
          </template>
          <tbody>
            <vs-tr>
              <vs-td>
                <span class="font-semibold uppercase">{{ cfdi.id }}</span>
              </vs-td>
              <vs-td>
                <span class="font-semibold uppercase">{{ cfdi.uuid }}</span>
              </vs-td>
              <vs-td>
                <span class="font-semibold uppercase">{{
                  cfdi.tipo_comprobante_texto
                }}</span>
              </vs-td>
              <vs-td>
                <span class="font-semibold uppercase">{{
                  cfdi.fecha_timbrado_texto
                }}</span>
              </vs-td>
            </vs-tr>
          </tbody>
        </vs-table>

        <vs-table stripe class="cfdis-table" :noDataText="' '" :sst="true">
          <template slot="header">
            <h3>Datos del CFDI</h3>
          </template>
          <template slot="thead">
            <vs-th>FORMA PAGO</vs-th>
            <vs-th>MÉTODO</vs-th>
            <vs-th>TOTAL</vs-th>
          </template>
          <tbody>
            <vs-tr>
              <vs-td>
                <span class="font-semibold uppercase">{{
                  cfdi.sat_formas_pago_texto
                }}</span>
              </vs-td>
              <vs-td>
                <span class="font-semibold uppercase">{{
                  cfdi.sat_metodos_pago_texto
                }}</span>
              </vs-td>
              <vs-td>
                <span class="font-semibold uppercase">
                  {{ cfdi.total | numFormat("0,000.00") }}</span
                >
              </vs-td>
            </vs-tr>
          </tbody>
        </vs-table>

        <vs-table stripe class="cfdis-table" :noDataText="' '" :sst="true">
          <template slot="header">
            <h3>¿QUÉ NECESITA DE ESTE CFDI?</h3>
          </template>
          <template slot="thead">
            <vs-th>Ver CFDI</vs-th>
            <vs-th>Descargar</vs-th>
            <vs-th>Cancelar CFDI</vs-th>
          </template>
          <tbody>
            <vs-tr>
              <vs-td>
                <div class="py-3 px-2 text-center">
                  <img
                    width="36px"
                    src="@assets/images/qr.svg"
                    class="cursor-pointer"
                    @click="
                      openReporte(
                        'Ver CFDI',
                        '/facturacion/get_cfdi_pdf/',
                        cfdi.id,
                        'cfdi'
                      )
                    "
                  />
                </div>
              </vs-td>
              <vs-td>
                <div class="py-3 px-2 text-center">
                  <img
                    width="36px"
                    src="@assets/images/downloadcfdi.svg"
                    class="cursor-pointer"
                    @click="DownloadCFDI(cfdi.id)"
                  />
                </div>
              </vs-td>
              <vs-td>
                <div class="py-3 px-2 text-center">
                  <img
                    width="36px"
                    src="@assets/images/cancel.svg"
                    class="cursor-pointer"
                    @click="Cancelar()"
                  /></div
              ></vs-td>
            </vs-tr>
          </tbody>
        </vs-table>
      </div>
    </vs-popup>
    <Password
      :show="openPassword"
      :callback-on-success="callback"
      @closeVerificar="closePassword"
      :accion="accionNombre"
    ></Password>
    <Reporteador
      :header="'consultar CFDIs'"
      :show="openReportesLista"
      :listadereportes="ListaReportes"
      :request="request"
      @closeReportes="openReportesLista = false"
    ></Reporteador>
  </div>
</template>
<script>
//componente de password
import Password from "@pages/confirmar_password";
import facturacion from "@services/facturacion";
import Reporteador from "@pages/Reporteador";
export default {
  components: {
    Password,
    Reporteador,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    //para saber que tipo de formulario es
    id_cfdi: {
      type: Number,
      required: true,
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cerrarVentana();
        };
        (async () => {
          await this.get_cfdi_id();
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
    getFolio: {
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
      cfdi: [],
      openReportesLista: false,
      folio_id: "",
      ListaReportes: [],
      request: {
        folio_id: "",
        email: "",
        destinatario: "",
      },
      openPassword: false,
      accionNombre: "Timbrar CFDI",
      callback: Function,
    };
  },
  methods: {
    closePassword() {
      this.openPassword = false;
    },
    openReporte(nombre_reporte = "", link = "", parametro = "", tipo = "") {
      this.ListaReportes = [];
      this.request.folio_id = this.cfdi.id;
      this.request.email = this.cfdi.cliente_email;
      this.request.destinatario = this.cfdi.cliente_nombre;
      this.ListaReportes.push({
        nombre: nombre_reporte,
        url: link,
      });
      this.openReportesLista = true;
    },

    DownloadCFDI(folio) {
      (async () => {
        this.$vs.loading();
        try {
          let res = await facturacion.get_cfdi_download(folio);
          const downloadUrl = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement("a");
          link.href = downloadUrl;
          link.setAttribute("download", "CFDI Folio " + this.cfdi.id + ".zip"); //any other extension
          document.body.appendChild(link);
          link.click();
          link.remove();
          this.$vs.loading.close();
          this.$vs.notify({
            title: "Descargar CFDI 3.3",
            text: "Se ha descargado el CFDI correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
          });
        } catch (error) {
          /**error al cargar vendedores */
          this.$vs.notify({
            title: "Error",
            text:
              "Ha ocurrido un error al tratar de descargar el CFDI seleccionado.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-right",
            time: "9000",
          });
          this.$vs.loading.close();
          this.cerrarVentana();
        }
      })();
    },
    cerrarVentana() {
      this.$emit("closeVentana");
    },
    async get_cfdi_id() {
      this.$vs.loading();
      try {
        let res = await facturacion.get_cfdi_id(this.getFolio);
        this.cfdi = res.data[0];
        this.$vs.loading.close();
      } catch (error) {
        /**error al cargar vendedores */
        this.$vs.notify({
          title: "Error",
          text:
            "Ha ocurrido un error al tratar de cargar el CFDI seleccionado.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "9000",
        });
        this.$vs.loading.close();
        this.cerrarVentana();
      }
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
    Cancelar() {
      try {
        (async () => {
          /**ingreso */
          if (this.cfdi.id > 0) {
            this.callback = await this.cancelar_cfdi_folio;
            this.openPassword = true;
          } else {
            this.$vs.notify({
              title: "Error",
              text: "Seleccione 1 CFDI",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "8000",
            });
          }
        })();
      } catch (error) {}
    },
    async cancelar_cfdi_folio() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await facturacion.cancelar_cfdi_folio(this.cfdi);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Timbrar CFDI 3.3",
            text: "Se ha cancelado el CFDI correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
          });
          //this.cerrarVentana();
        } else {
          this.$vs.notify({
            title: "Timbrar CFDI 3.3",
            text: "Error al cancelar el CFDI, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 4000,
          });
        }

        this.$vs.loading.close();
      } catch (err) {
        if (err.response) {
          console.log("timbrar_cfdi -> err.response", err.response);
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
            //checo si existe cada error
            this.errores = err.response.data.error;
            this.$vs.notify({
              title: "Timbrar CFDI 3.3",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 5000,
            });
          } else if (err.response.status == 409) {
            this.$vs.notify({
              title: "Timbrar CFDI 3.3",
              text: err.response.data.error,
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 30000,
            });
          }
        }
        this.$vs.loading.close();
      }
    },
  },
  created() {},
};
</script>
