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
            <vs-th>Ver Operación</vs-th>
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
                    src="@assets/images/pdf.svg"
                    class="cursor-pointer"
                    @click="verCfdi"
                  />
                </div>
              </vs-td>
              <vs-td>
                <div class="py-3 px-2 text-center">
                  <img
                    width="36px"
                    src="@assets/images/qr.svg"
                    class="cursor-pointer"
                    @click="verCfdi"
                  />
                </div>
              </vs-td>
              <vs-td>
                <div class="py-3 px-2 text-center">
                  <img
                    width="36px"
                    src="@assets/images/downloadcfdi.svg"
                    class="cursor-pointer"
                    @click="verCfdi"
                  />
                </div>
              </vs-td>
              <vs-td>
                <div class="py-3 px-2 text-center">
                  <img
                    width="36px"
                    src="@assets/images/cancel.svg"
                    class="cursor-pointer"
                    @click="verCfdi"
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
  </div>
</template>
<script>
//componente de password
import Password from "@pages/confirmar_password";
import facturacion from "@services/facturacion";
export default {
  components: {
    Password,
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
    };
  },
  methods: {
    verCfdi() {
      alert(this.cfdi.id);
    },

    cerrarVentana() {
      this.$emit("closeVentana");
    },
    async get_cfdi_id() {
      try {
        let res = await facturacion.get_cfdi_id(this.getFolio);
        this.cfdi = res.data[0];
        console.log("get_cfdi_id -> this.cfdi", this.cfdi);
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
        this.cerrarVentana();
      }
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
  },
  created() {},
};
</script>
