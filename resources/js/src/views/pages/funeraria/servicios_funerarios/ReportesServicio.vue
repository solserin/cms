<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups normal-forms reportes_lista"
      fullscreen
      title="expediente de venta en planes"
      :active.sync="showVentana"
      ref="lista_reportes"
    >
      <div class="flex flex-wrap">
        <div class="w-full" v-if="datosSolicitud.servicio_id">
          <vs-table class :data="documentos" noDataText="0 Resultados">
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
                  <span class="font-semibold">{{ index_documento + 1 }}</span>
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

      <div class="w-full pt-8" v-if="datosSolicitud.operacion">
        <vs-table
          class="tablas-pagos"
          :data="datosSolicitud.operacion.pagos_programados"
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
            <vs-th>Monto Pago</vs-th>
            <vs-th>Restante a Pagar</vs-th>
            <vs-th>Concepto</vs-th>
            <vs-th>Estatus</vs-th>
            <vs-th>Pagar Recibo</vs-th>
          </template>
          <template>
            <vs-tr
              v-show="programados.status == 1"
              v-for="(programados, index_programado) in datosSolicitud.operacion
                .pagos_programados"
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
                >{{ programados.referencia_pago }}</vs-td
              >
              <vs-td
                :class="[programados.status_pago == 0 ? 'text-danger' : '']"
                >{{ programados.fecha_programada_abr }}</vs-td
              >
              <vs-td
                :class="[programados.status_pago == 0 ? 'text-danger' : '']"
              >
                $
                {{ programados.monto_programado | numFormat("0,000.00") }}
              </vs-td>
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
                  programados.status_pago == 2 ? 'text-success' : '',
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
                    width="20"
                    class="cursor-pointer ml-auto mr-auto"
                    src="@assets/images/right.svg"
                    title="ficha cubierta"
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

      <div class="w-full pt-8" v-if="datosSolicitud.operacion_id">
        <vs-table class="tablas-pagos" :data="pagos" noDataText="0 Resultados">
          <template slot="header">
            <h3>Listado de Abonos Recibidos</h3>
          </template>
          <template slot="thead">
            <vs-th>Clave</vs-th>
            <vs-th>Fecha Pago</vs-th>
            <vs-th>Total Pago</vs-th>
            <vs-th>Concepto</vs-th>
            <vs-th>Cobrador</vs-th>
            <vs-th>Estatus</vs-th>
            <vs-th>Consultar</vs-th>
          </template>
          <template>
            <vs-tr
              v-for="(pago, index_pago) in pagos"
              v-bind:key="pago.id"
              ref="row"
            >
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class>{{ pago.id }}</span>
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class>{{ pago.fecha_pago_texto }}</span>
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class
                  >$ {{ pago.total_pago | numFormat("0,000.00") }}</span
                >
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class>{{ pago.movimientos_pagos_texto }}</span>
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class>{{ pago.cobrador.nombre }}</span>
              </vs-td>
              <vs-td :class="[pago.status == 0 ? 'text-danger' : '']">
                <span class>{{ pago.status_texto }}</span>
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
import funeraria from "@services/funeraria";
import pagos from "@services/pagos";
import FormularioPagos from "@pages/pagos/FormularioPagos";
export default {
  components: {
    Reporteador,
    FormularioPagos,
  },
  props: {
    verAcuse: {
      type: Boolean,
      required: false,
      default: false,
    },
    show: {
      type: Boolean,
      required: true,
    },
    id_solicitud: {
      type: Number,
      required: true,
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$refs["lista_reportes"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };
        (async () => {
          await this.get_solicitudes_servicios_id();
          if (this.operacion_id != "") {
            //await this.consultar_pagos_operacion_id();
          }
          /**checamos si esta ventana fue abierta con el fin de ver el acuse de cancelacion */
          if (this.getVerAcuse == true) {
            this.openReporte(
              "Acuse de cancelación",
              "/funeraria/acuse_cancelacion",
              "",
              ""
            );
          }
        })();
      } else {
        /**cerrar ventana */
        this.datosSolicitud = [];
        this.total = 0;
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
    get_solicitud_id: {
      get() {
        return this.id_solicitud;
      },
      set(newValue) {
        return newValue;
      },
    },
    getVerAcuse: {
      get() {
        return this.verAcuse;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      referencia: "",
      documentos: [
        {
          documento: "Solicitud de Servicio",
          url: "/funeraria/get_hoja_solicitud",
          tipo: "pdf",
        },
        {
          documento: "Autorización de Servicio Funerario",
          url: "/funeraria/hoja_preautorizacion",
          tipo: "pdf",
        },
        {
          documento: "Certificado de Defunción",
          url: "/funeraria/certificado_defuncion",
          tipo: "pdf",
        },
        {
          documento: "Guía de Servicio Para el Cliente",
          url: "/funeraria/instrucciones_servicio_funerario",
          tipo: "pdf",
        },
        {
          documento: "Constancia de embalsamiento",
          url: "/funeraria/contancia_de_embalsamiento",
          tipo: "pdf",
        },
        {
          documento: "Material de Velación",
          url: "/funeraria/material_velacion_rentado",
          tipo: "pdf",
        },
        {
          documento: "Entrega de acta de defunción",
          url: "/funeraria/entrega_acta_defuncion",
          tipo: "pdf",
        },
        {
          documento: "Entrega de cenizas",
          url: "/funeraria/entrega_cenizas",
          tipo: "pdf",
        },
        {
          documento: "Hoja de Servicio",
          url: "/funeraria/orden_servicio",
          tipo: "pdf",
        },
        {
          documento: "Acuse de cancelación",
          url: "/funeraria/acuse_cancelacion",
          tipo: "pdf",
        },
      ],
      total: 0 /**rows que se van a remplazar el click en el evento de las tablas para modificar el expand */,
      funcion_reemplazada: [],
      datosSolicitud: [],
      ListaReportes: [],
      request: {
        id_pago: "",
        id_servicio: "",
        email: "",
        destinatario: "",
      },
      openReportesLista: false,
      verFormularioPagos: false,
      tipoFormularioPagos: "",
      operacion_id: "",
      pagos: [],
    };
  },
  methods: {
    closeFormularioPagos() {
      this.verFormularioPagos = false;
    },
    retorno_pagos(datos) {
      (async () => {
        await this.get_solicitudes_servicios_id();
        if (this.operacion_id != "") {
          await this.consultar_pagos_operacion_id();
          /**llamando el pago recien hecho */
          this.openReporte(
            "reporte de pago",
            "/pagos/recibo_de_pago/",
            datos.id_pago,
            "pago"
          );
        }
      })();
    },
    pagar(referencia) {
      this.referencia = referencia;
      this.verFormularioPagos = true;
    },
    mostrarDocumento(documento) {
      if (
        documento != "Acuse de cancelación" &&
        documento != "Constancia de embalsamiento" &&
        documento != "Material de Velación" &&
        documento != "Entrega de acta de defunción" &&
        documento != "Entrega de cenizas" &&
        documento != "Hoja de Servicio"
      ) {
        return true;
      } else {
        if (documento == "Acuse de cancelación") {
          /**chenado si esta cancelada la venta para mostrar este archivo de acuse de cancelacion */
          if (this.datosSolicitud.operacion_status == 0) {
            return true;
          } else return false;
        } else if (documento == "Constancia de embalsamiento") {
          /**chenado si tiene saldo pendiente */
          if (this.datosSolicitud.embalsamar_b == 1) {
            return true;
          } else return false;
        } else if (documento == "Material de Velación") {
          /**chenado si tiene saldo pendiente */
          if (this.datosSolicitud.material_velacion_b == 1) {
            return true;
          } else return false;
        } else if (documento == "Entrega de acta de defunción") {
          /**chenado si tiene saldo pendiente */
          if (this.datosSolicitud.acta_b == 1) {
            return true;
          } else return false;
        } else if (documento == "Entrega de cenizas") {
          /**chenado si tiene saldo pendiente */
          if (this.datosSolicitud.cremacion_b == 1) {
            return true;
          } else return false;
        } else if (documento == "Hoja de Servicio") {
          /**chenado si tiene saldo pendiente */
          if (this.datosSolicitud.operacion != null) {
            return true;
          } else return false;
        }
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
        url: link,
      });
      //estado de cuenta
      this.request.email = this.datosSolicitud.email;

      if (tipo == "pago") {
        this.request.id_pago = parametro;
      } else {
        this.request.id_servicio = this.datosSolicitud.servicio_id;
      }

      this.request.destinatario = this.datosSolicitud.nombre;
      this.openReportesLista = true;
      this.$vs.loading.close();
    },
    async get_solicitudes_servicios_id() {
      this.ListaReportes = [];
      this.$vs.loading();
      try {
        this.operacion_id = "";
        let res = await funeraria.get_solicitudes_servicios_id(
          this.get_solicitud_id
        );
        console.log("get_solicitudes_servicios_id -> res", res);
        this.datosSolicitud = res.data[0];
        //this.operacion_id = this.datosSolicitud.operacion_id;
        /*if (this.datosSolicitud.pagos_programados.length > 0) {
          //calculando el total de rows 
          this.funcion_reemplazada = [];
          for (
            let index = 0;
            index < this.datosSolicitud.pagos_programados.length;
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
              time: 4000,
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
              time: 4000,
            });
          }
        }
      }
    },
  },
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", (e) => {
      if (e.keyCode === 27) {
        if (this.showVentana) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          //this.cancelar();
        }
      }
    });
  },
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