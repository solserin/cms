<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups normal-forms reportes_lista"
      fullscreen
      title="expediente de venta en cementerio"
      :active.sync="showVentana"
      ref="lista_reportes"
    >
      <div class="flex flex-wrap">
        <div class="w-full" v-if="datosVenta.operacion_id">
          <vs-table class="" :data="documentos" noDataText="0 Resultados">
            <template slot="header">
              <h3>Documentos del contrato</h3>
            </template>
            <template slot="thead">
              <vs-th class="w-1/5">#</vs-th>
              <vs-th class="w-3/5">Documento</vs-th>
              <vs-th class="w-1/5">Seleccionar Documento</vs-th>
            </template>
            <template>
              <vs-tr
                v-show="mostrarDocumento(documento.documento)"
                v-for="(documento, index_documento) in documentos"
                v-bind:key="documento.id"
              >
                <vs-td>
                  <span class="font-semibold"> {{ index_documento + 1 }}</span>
                </vs-td>
                <vs-td>
                  <span class="font-semibold">{{ documento.documento }}</span>
                </vs-td>
                <vs-td>
                  <img
                    width="30"
                    v-if="documento.tipo == 'pdf'"
                    class="cursor-pointer ml-auto mr-auto"
                    src="@assets/images/pdf.svg"
                    title="Consultar Documento"
                    @click="
                      openReporte(documento.documento, documento.url, '', '')
                    "
                  />
                  <img
                    width="30"
                    v-else
                    class="cursor-pointer ml-auto mr-auto"
                    src="@assets/images/excel.svg"
                    title="Consultar Documento"
                    @click="
                      openReporte(documento.documento, documento.url, '', '')
                    "
                  />
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
        </div>
      </div>

      <div class="w-full pt-8" v-if="datosVenta.operacion_id">
        <vs-table
          class="tablas-pagos"
          :data="datosVenta.pagos_programados"
          noDataText="0 Resultados"
          ref="tabla_pagos_programados"
        >
          <template slot="header">
            <h3>Listado de Pagos programados</h3>
          </template>
          <template slot="thead">
            <vs-th>#</vs-th>
            <vs-th>Referencia</vs-th>
            <vs-th>Fecha Programada</vs-th>
            <vs-th>Nueva Fecha de Pago</vs-th>
            <vs-th>Monto Pago</vs-th>
            <vs-th>Intereses Generados</vs-th>
            <vs-th>Restante a Pagar</vs-th>
            <vs-th>Concepto</vs-th>
            <vs-th>Estatus</vs-th>
            <vs-th>Pagar Recibo</vs-th>
          </template>
          <template>
            <vs-tr
              v-show="programados.status == 1"
              v-for="(programados,
              index_programado) in datosVenta.pagos_programados"
              v-bind:key="programados.id"
              ref="row"
            >
              <vs-td
                :class="[programados.status_pago == 0 ? 'text-danger' : '']"
              >
                <span class="font-semibold">{{ programados.num_pago }}</span>
              </vs-td>
              <vs-td
                :class="[programados.status_pago == 0 ? 'text-danger' : '']"
              >
                {{ programados.referencia_pago }}
              </vs-td>
              <vs-td
                :class="[programados.status_pago == 0 ? 'text-danger' : '']"
                >{{ programados.fecha_programada_abr }}</vs-td
              >
              <vs-td
                :class="[programados.status_pago == 0 ? 'text-danger' : '']"
              >
                <span v-if="programados.saldo_neto > 0">
                  {{ programados.fecha_a_pagar_abr }}
                </span>
                <span v-else>{{ programados.fecha_ultimo_pago_abr }} </span>
              </vs-td>
              <vs-td
                :class="[programados.status_pago == 0 ? 'text-danger' : '']"
                >$
                {{
                  programados.monto_programado | numFormat("0,000.00")
                }}</vs-td
              >
              <vs-td
                :class="[programados.status_pago == 0 ? 'text-danger' : '']"
                >$ {{ programados.intereses | numFormat("0,000.00") }}</vs-td
              >
              <vs-td
                :class="[programados.status_pago == 0 ? 'text-danger' : '']"
                >$ {{ programados.saldo_neto | numFormat("0,000.00") }}</vs-td
              >
              <vs-td
                :class="[programados.status_pago == 0 ? 'text-danger' : '']"
                >{{ programados.concepto_texto }}</vs-td
              >
              <vs-td
                :class="[
                  programados.status_pago == 0 ? 'text-danger' : '',
                  programados.status_pago == 2 ? 'text-success' : ''
                ]"
              >
                <span>{{ programados.status_pago_texto }}</span>
              </vs-td>
              <vs-td>
                <div class="flex flex-start py-1">
                  <img
                    v-if="programados.saldo_neto > 0"
                    width="26"
                    class="cursor-pointer ml-auto mr-auto"
                    src="@assets/images/dollar_bill.svg"
                    title="Pagar Ficha"
                    @click="pagar(programados.referencia_pago)"
                  />
                  <img
                    v-else
                    width="26"
                    class="cursor-pointer ml-auto mr-auto"
                    src="@assets/images/pdf.svg"
                    title="Pagar Ficha"
                    @click="pagar(programados.referencia_pago)"
                  />
                </div>
              </vs-td>
              <!-- <template class="expand-user" slot="expand">
                d
              </template>
              -->
            </vs-tr>
          </template>
        </vs-table>
      </div>

      <div class="w-full pt-8" v-if="datosVenta.operacion_id">
        <vs-table class="tablas-pagos" :data="pagos" noDataText="0 Resultados">
          <template slot="header">
            <h3>Listado de Abonos Recibidos</h3>
          </template>
          <template slot="thead">
            <vs-th>#</vs-th>
            <vs-th>Referencia</vs-th>
            <vs-th>Fecha Programada</vs-th>
            <vs-th>Monto Pago</vs-th>
            <vs-th>Intereses Generados</vs-th>
            <vs-th>Restante a Pagar</vs-th>
            <vs-th>Concepto</vs-th>
            <vs-th>Estatus</vs-th>
            <vs-th>Ver Nota de Pago</vs-th>
          </template>
          <template>
            <vs-tr
              v-for="(pago, index_pago) in pagos"
              v-bind:key="pago.id"
              ref="row"
            >
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class="font-semibold">{{ pago.status }}</span>
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class="font-semibold">{{ pago.status }}</span>
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class="font-semibold">{{ pago.status }}</span>
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class="font-semibold">{{ pago.status }}</span>
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class="font-semibold">{{ pago.status }}</span>
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class="font-semibold">{{ pago.status }}</span>
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class="font-semibold">{{ pago.status }}</span>
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class="font-semibold">{{ pago.status }}</span>
              </vs-td>
              <vs-td>
                <div class="flex flex-start py-1">
                  <img
                    width="26"
                    class="cursor-pointer ml-auto mr-auto"
                    src="@assets/images/pdf.svg"
                    title="Ver Nota de Pago"
                    @click="
                      openReporte(
                        'reporte de pago',
                        '/pagos/recibo_de_pago/',
                        pago.id,
                        'pago'
                      )
                    "
                  />
                </div>
              </vs-td>
            </vs-tr>
          </template>
        </vs-table>
      </div>

      <Reporteador
        :header="'consultar documentos de venta de propiedad'"
        :show="openReportesLista"
        :listadereportes="ListaReportes"
        :request="request"
        @closeReportes="openReportesLista = false"
      ></Reporteador>

      <FormularioPagos
        :referencia="referencia"
        :show="verFormularioPagos"
        @closeVentana="closeFormularioPagos"
        @retorno_pagos="retorno_pagos"
      ></FormularioPagos>
    </vs-popup>
  </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import cementerio from "@services/cementerio";
import pagos from "@services/pagos";
import FormularioPagos from "@pages/pagos/FormularioPagos";
export default {
  components: {
    Reporteador,
    FormularioPagos
  },
  props: {
    show: {
      type: Boolean,
      required: true
    },
    id_venta: {
      type: Number,
      required: true
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
        (async () => {
          await this.consultar_venta_id();
          if (this.operacion_id != "") {
            await this.consultar_pagos_operacion_id();
          }
        })();
      } else {
        /**cerrar ventana */
        this.datosVenta = [];
        this.total = 0;
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
    },
    get_venta_id: {
      get() {
        return this.id_venta;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  data() {
    return {
      referencia: "",
      documentos: [
        {
          documento: "Formato de Solicitud",
          url: "/cementerio/documento_solicitud",
          tipo: "pdf"
        },
        {
          documento: "Convenio",
          url: "/cementerio/documento_convenio",
          tipo: "pdf"
        },
        {
          documento: "Título",
          url: "/cementerio/documento_titulo",
          tipo: "pdf"
        },
        /*
        {
          documento: "Estado de cuenta",
          url: "/cementerio/documento_estado_de_cuenta_cementerio",
          tipo: "pdf"
        },
        */
        {
          documento: "Talonario de Pagos",
          url: "/cementerio/referencias_de_pago",
          tipo: "pdf"
        },
        {
          documento: "Reglamento de Pago",
          url: "/cementerio/reglamento_pago",
          tipo: "pdf"
        },
        {
          documento: "Acuse de cancelación",
          url: "/inventarios/cementerio/acuse_cancelacion",
          tipo: "pdf"
        }
      ],
      total: 0 /**rows que se van a remplazar el click en el evento de las tablas para modificar el expand */,
      funcion_reemplazada: [],
      datosVenta: [],
      ListaReportes: [],
      request: {
        id_pago: "",
        venta_id: "",
        email: "",
        destinatario: ""
      },
      openReportesLista: false,
      verFormularioPagos: false,
      tipoFormularioPagos: "",
      operacion_id: "",
      pagos: []
    };
  },
  methods: {
    closeFormularioPagos() {
      this.verFormularioPagos = false;
    },
    retorno_pagos(datos) {
      (async () => {
        await this.consultar_venta_id();
        if (this.operacion_id != "") {
          await this.consultar_pagos_operacion_id();
        }
      })();
    },
    pagar(referencia) {
      this.referencia = referencia;
      this.verFormularioPagos = true;
    },
    mostrarDocumento(documento) {
      if (documento != "Acuse de cancelación") {
        return true;
      } else {
        /**chenado si esta cancelada la venta para mostrar este archivo de acuse de cancelacion */
        if (this.datosVenta.operacion_status == 0) {
          return true;
        } else return false;
      }
    },
    cancelar() {
      this.$emit("closeListaReportes");
      return;
    },

    openReporte(nombre_reporte = "", link = "", parametro = "", tipo = "") {
      this.ListaReportes = [];
      this.ListaReportes.push({
        nombre: nombre_reporte,
        url: link
      });
      //estado de cuenta
      this.request.email = this.datosVenta.email;

      if (tipo == "pago") {
        this.request.id_pago = parametro;
      } else {
        this.request.venta_id = this.datosVenta.ventas_terrenos_id;
      }

      this.request.destinatario = this.datosVenta.nombre;
      this.openReportesLista = true;
      this.$vs.loading.close();
    },
    async consultar_venta_id() {
      this.ListaReportes = [];
      this.$vs.loading();
      try {
        this.operacion_id = "";
        let res = await cementerio.consultar_venta_id(this.get_venta_id);
        this.datosVenta = res.data[0];
        this.operacion_id = this.datosVenta.operacion_id;
        /*if (this.datosVenta.pagos_programados.length > 0) {
          //calculando el total de rows 
          this.funcion_reemplazada = [];
          for (
            let index = 0;
            index < this.datosVenta.pagos_programados.length;
            index++
          ) {
            this.$nextTick(() => {
              this.funcion_reemplazada.push(this.$refs["row"][index].clicktd);
              this.$refs["row"][index].clicktd = e => {};
              this.$refs["row"][index].$el
                .querySelector(".vs-icon")
                .addEventListener("click", this.funcion_reemplazada[index]);
            });
          }
        }
*/

        this.$vs.loading.close();
      } catch (err) {
        this.$vs.loading.close();
        if (err.response) {
          if (err.response.status == 403) {
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 4000
            });
          }
        }
      }
    },
    async consultar_pagos_operacion_id() {
      this.$vs.loading();
      try {
        this.pagos = [];
        let datos_request = { operacion_id: this.operacion_id };
        let res = await pagos.consultar_pagos_operacion_id(datos_request);
        this.pagos = res.data.data;
        this.$vs.loading.close();
      } catch (err) {
        this.$vs.loading.close();
        if (err.response) {
          if (err.response.status == 403) {
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 4000
            });
          }
        }
      }
    }
  },
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", e => {
      if (e.keyCode === 27) {
        if (this.showVentana) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          //this.cancelar();
        }
      }
    });
  }
};
</script>

<style lang="stylus">
.con-expand-users {
  width: 100%;

  .con-btns-user {
    display: flex;
    padding: 10px;
    padding-bottom: 0px;
    align-items: center;
    justify-content: space-between;

    .con-userx {
      display: flex;
      align-items: center;
      justify-content: flex-start;
    }
  }

  .list-icon {
    i {
      font-size: 0.9rem !important;
    }
  }
}
</style>