<template >
  <div class="centerx">
    <vs-popup
      class
      fullscreen
      title="Documentos Disponibles de La Venta"
      :active.sync="showVentana"
      ref="lista_reportes"
    >
      <h3 class="text-base text-center pt-1 pb-6 hidden">
        <span class="uppercase bold">Seleccione el documento que necesita</span>
      </h3>

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
        <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center py-3">
          <img
            class="cursor-pointer"
            src="@assets/images/reportes/pdf_download.svg"
            @click="openReporte('Referencias de Pago','/inventarios/cementerio/referencias_de_pago','')"
          />
          <h4 class="py-3 capitalize">Fichas de Pago</h4>
        </div>
      </div>

      <div
        v-for="(programacion, index) in datosVenta.programacion_pagos"
        v-bind:key="programacion.num_version"
      >
        <div class="pt-5 px-2">
          <h3
            v-if="index==0"
            class="pb-2 text-base text-center uppercase font-semibold"
          >programación de pagos - lista actual</h3>
          <h3
            v-else
            class="pb-2 text-base text-center uppercase font-semibold"
          >programación de pagos (no vigente, solo para consulta de historial)</h3>
        </div>
        <div>
          <vs-table
            class="tablas-pagos"
            :max-items="300"
            :data="programacion.pagos_programados"
            stripe
            noDataText="0 Resultados"
            :ref="'tabla'+index"
          >
            <template slot="thead">
              <vs-th>#</vs-th>
              <vs-th>Referencia</vs-th>
              <vs-th>Fecha Programada</vs-th>
              <vs-th>Monto Pago</vs-th>
              <vs-th>Intereses Generados</vs-th>
              <vs-th>Restante a Pagar</vs-th>
              <vs-th>Concepto</vs-th>
              <vs-th>Recibo de Pago</vs-th>
            </template>
            <template>
              <vs-tr
                v-for="(programados, index_programado) in programacion.pagos_programados"
                v-bind:key="programados.id"
                ref="row"
              >
                <vs-td>
                  <span class="font-semibold">{{programados.num_pago}}</span>
                </vs-td>
                <vs-td>{{programados.referencia_pago}}</vs-td>
                <vs-td>{{programados.fecha_programada_texto}}</vs-td>
                <vs-td>$ {{programados.total | numFormat('0,000.00')}}</vs-td>
                <vs-td>$ {{programados.intereses_a_pagar | numFormat('0,000.00')}}</vs-td>
                <vs-td>$ {{programados.total_a_pagar | numFormat('0,000.00')}}</vs-td>
                <vs-td>{{programados.concepto}}</vs-td>
                <vs-td>
                  <div class="flex justify-center">
                    <img
                      class="cursor-pointer"
                      style="width:20px;"
                      src="@assets/images/pdf.svg"
                      alt
                      @click="openReporte('Referencias de Pago # '+programados.num_pago,'/inventarios/cementerio/referencias_de_pago/'+programados.id,'')"
                    />
                  </div>
                </vs-td>
                <template class="expand-user" slot="expand">
                  <div class="con-expand-users py-12 bg-white">
                    <h3
                      class="pb-5 text-base text-center uppercase font-semibold"
                    >pagos a cuenta de abono programado {{programados.concepto}}</h3>
                    <vs-table
                      v-if="programados.pagos_realizados.length>0"
                      class="tablas-pagos"
                      :max-items="300"
                      :data="programados.pagos_realizados"
                      stripe
                      noDataText="0 Resultados"
                    >
                      <template slot="thead">
                        <vs-th>#</vs-th>
                        <vs-th>Id. Pago (Cementerio)</vs-th>
                        <vs-th>Fecha Realizado</vs-th>
                        <vs-th>Monto Pago</vs-th>
                        <vs-th>Tipo Pago</vs-th>
                        <vs-th>Nombre de quién registro</vs-th>
                        <vs-th>Recibo de Pago</vs-th>
                      </template>
                      <template>
                        <vs-tr
                          v-for="(pagado, index_pagado) in programados.pagos_realizados"
                          v-bind:key="pagado.id"
                        >
                          <vs-td>
                            <span class="font-semibold">{{(index_pagado+1)}}</span>
                          </vs-td>
                          <vs-td>{{pagado.id}}</vs-td>
                          <vs-td>{{pagado.fecha_registro}}</vs-td>
                          <vs-td>$ {{pagado.total | numFormat('0,000.00')}}</vs-td>
                          <vs-td>$ {{pagado.tipo_pagos_id}}</vs-td>
                          <vs-td>$ {{pagado.registro_id }}</vs-td>

                          <vs-td>
                            <div class="flex justify-center">
                              <img
                                class="cursor-pointer hidden"
                                style="width:20px;"
                                src="@assets/images/pdf.svg"
                                alt
                                @click="openReporte('Referencias de Pago # '+pagado.id,'/inventarios/cementerio/referencias_de_pago/'+pagado.id,'')"
                              />
                            </div>
                          </vs-td>
                        </vs-tr>
                      </template>
                    </vs-table>
                  </div>
                </template>
              </vs-tr>
            </template>
          </vs-table>
        </div>
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
      } else {
        /**cerrar ventana */
        this.datosVenta = [];
        this.total = 0;
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

            if (this.datosVenta.programacion_pagos.length > 0) {
              /**calculando el total de rows */
              this.datosVenta.programacion_pagos.forEach(programacion => {
                programacion.pagos_programados.forEach(programado => {
                  this.total++;
                  programado.pagos_realizados.forEach(realizado => {
                    //this.total++;
                  });
                });
              });
              this.funcion_reemplazada = [];
              for (let index = 0; index < this.total; index++) {
                this.$nextTick(() =>
                  this.funcion_reemplazada.push(
                    this.$refs["row"][index].clicktd
                  )
                );
                this.$nextTick(
                  () => (this.$refs["row"][index].clicktd = e => {})
                );
                this.$nextTick(() =>
                  this.$refs["row"][index].$el
                    .querySelector(".vs-icon")
                    .addEventListener("click", this.funcion_reemplazada[index])
                );
              }
            }

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
      total: 0 /**rows que se van a remplazar el click en el evento de las tablas para modificar el expand */,
      funcion_reemplazada: [],
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