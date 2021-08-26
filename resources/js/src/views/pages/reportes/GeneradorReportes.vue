<template>
  <div>
    <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
      <vx-card
        no-radius
        title="Filtros de elaboración de reportes"
        refresh-content-action
        @refresh="reset"
        :collapse-action="false"
      >
        <div class="flex flex-wrap">
          <div class="w-full xl:w-4/12 mb-1 px-2 input-text">
            <label class="">Módulo del Sistema</label>
            <v-select
              :options="modulos"
              :clearable="false"
              v-model="form.modulo"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              class="w-full"
            />
          </div>
          <div class="w-full xl:w-4/12 mb-1 px-2 input-text">
            <label class="">Tipo de Reporte</label>
            <v-select
              :options="reportes"
              v-model="form.reporte"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              class="w-full"
            />
          </div>

          <div
            class="w-full xl:w-4/12 mb-1 px-2 input-text"
            v-show="ver_fecha_rango"
          >
            <label class="">
              Rango de Fechas año/mes/dia
              <span>(*)</span>
            </label>
            <flat-pickr
              name="rango_fechas"
              data-vv-as=" "
              :config="configdateTimePickerRange"
              v-model="form.rango_fechas"
              placeholder="Rango de fechas del reporte"
              class="w-full"
              @on-close="onCloseDate"
            />
          </div>
          <div class="w-full xl:w-4/12 px-2 input-text" v-show="ver_fecha">
            <label>
              Fecha del Reporte
              <span>(*)</span>
            </label>
            <flat-pickr
              name="fecha"
              data-vv-as=" "
              :config="configdateTimePicker"
              v-model="form.fecha"
              placeholder="Fecha del Reporte"
              class="w-full"
            />
          </div>
        </div>
      </vx-card>

      <div class="w-full pt-8" v-if="form.reporte.value">
        <vs-table
          class="tabla-datos tabla-datos-no-data"
          :data="[]"
          noDataText="0 Resultados"
        >
          <template slot="header">
            <h3>Consultar / Descargar el Reporte Seleccionado</h3>
          </template>
          <template slot="thead">
            <vs-th>Reporte</vs-th>
            <vs-th>Detalle del reporte</vs-th>
            <vs-th>Consultar</vs-th>
          </template>
          <template>
            <vs-tr>
              <vs-td>
                {{ form.reporte.label }}
              </vs-td>
              <vs-td>
                {{ form.reporte.detalle }}
              </vs-td>
              <vs-td>
                <div class="flex justify-center">
                  <img
                    class="cursor-pointer img-btn-24 mx-2"
                    src="@assets/images/pdf.svg"
                    title="Ver Reporte"
                    @click="openReporte()"
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
        :request="form"
        @closeReportes="openReportesLista = false"
      ></Reporteador>
    </div>
  </div>
</template>

<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import Reporteador from "@pages/Reporteador";
import cementerio from "@services/cementerio";
const moment = require("moment");
/**VARIABLES GLOBALES */
import vSelect from "vue-select";
import {
  configdateTimePickerRange,
  configdateTimePicker,
} from "@/VariablesGlobales";
export default {
  components: {
    "v-select": vSelect,
    flatPickr,
    Reporteador,
  },
  computed: {
    ver_fecha: function () {
      let ver = true;
      if (this.form.modulo.value == 1) {
        /**inventarios */
        if (this.form.reporte.value != 1) {
          ver = false;
        }
      } else if (this.form.modulo.value == 2) {
        /**cementerio */
        ver = false;
      }
      return ver;
    },
    ver_fecha_rango: function () {
      let ver = true;
      if (this.form.modulo.value == 1) {
        /**inventarios */
        if (this.form.reporte.value == 1) {
          ver = false;
        }
      } else if (this.form.modulo.value == 2) {
        /**cementerio */
        ver = false;
      }
      return ver;
    },
  },
  watch: {
    "form.modulo": function (newValue, oldValue) {
      /**Cargando los reportes segun el modulo */
      this.reportes = [{ label: "Seleccionar", value: "" }];

      if (newValue.value == 1) {
        /**inventario*/
        this.reportes.push({
          label: "Existencias y Costos",
          value: "1",
          detalle:
            "Existencias y costos, costo total de acuerdo al último costo",
          excel_b: 1,
        });
        this.reportes.push({
          label: "Movimientos del Inventario",
          value: "2",
          detalle: "Movimientos del Inventario",
          excel_b: 1,
        });
        this.reportes.push({
          label: "Inventario Actual Global Funeraria",
          value: "3",
          detalle: "Inventario Actual Global Funeraria",
          excel_b: 1,
        });
      } else if (newValue.value == 2) {
        /**cementerio*/
        this.reportes.push({
          label: "Abonos vencidos de propiedades",
          value: "reporte_propiedades",
          detalle: "Abonos vencidos de venta de propiedades",
          excel_b: 0,
        });
        (async () => {
          /**manda traer los cuotas */
          await this.get_cuotas_simple();
          this.cuotas.forEach((element) => {
            this.reportes.push({
              label: "Deudores cuota cementerio " + element.descripcion,
              value: element.id,
              detalle: element.descripcion,
              excel_b: 0,
              tipo_reporte: "cuota_cementerio",
            });
          });
        })();
      }

      this.form.reporte = this.reportes[0];
    },
  },
  data() {
    return {
      ListaReportes: [],
      openReportesLista: false,
      configdateTimePicker: configdateTimePicker,
      configdateTimePickerRange: configdateTimePickerRange,
      form: {
        modulo: { label: "Seleccionar", value: "" },
        reporte: { label: "Seleccionar", value: "", detalle: "" },
        rango_fechas: [],
        fecha_inicio: "",
        fecha_fin: "",
        fecha: "",
        email: "",
        destinatario: "",
        tipo_reporte: "",
        id_cuota: "",
      },
      cuotas: [],
      modulos: [
        {
          label: "Seleccionar",
          value: "",
        },
        {
          label: "Inventario",
          value: "1",
        },
        {
          label: "Cementerio",
          value: "2",
        },
        {
          label: "Funeraria",
          value: "3",
        },
        {
          label: "Cobranza",
          value: "4",
        },
      ],
      reportes: [
        {
          label: "Seleccionar",
          value: "",
        },
      ],
    };
  },
  methods: {
    async get_cuotas_simple() {
      try {
        this.$vs.loading();
        let res = await cementerio.get_cuotas_simple();
        this.cuotas = res.data;
        this.$vs.loading.close();
      } catch (err) {
        this.$vs.loading.close();
        this.ver = true;
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
          }
        }
      }
    },

    reset(card) {
      card.removeRefreshAnimation(500);
    },

    onCloseDate(selectedDates, dateStr, instance) {
      /**se valdiad que se busque la informacion solo en los casos donde la fechas cambien */
      if (selectedDates.length == 0) {
        /**no hay fechas que buscar */
        this.form.fecha_inicio = "";
        this.form.fecha_fin = "";
      } else {
        /**hay fechas que buscar */
        if (
          this.form.fecha_inicio !=
            moment(selectedDates[0]).format("YYYY-MM-DD") ||
          this.form.fecha_fin != moment(selectedDates[1]).format("YYYY-MM-DD")
        ) {
          /**agreggo la fecha 1 */
          this.form.fecha_inicio = moment(selectedDates[0]).format(
            "YYYY-MM-DD"
          );
          this.form.fecha_fin = moment(selectedDates[1]).format("YYYY-MM-DD");
        }
      }
    },

    openReporte() {
      this.form.tipo_reporte = "";
      if (this.form.modulo.value == 1) {
        if (this.form.reporte.value == 1) {
          if (!this.validarFecha()) {
            return;
          }
        } else if (
          this.form.reporte.value == 2 ||
          this.form.reporte.value == 3
        ) {
          if (!this.validarRangoFecha()) {
            return;
          }
        }
      } else if (this.form.modulo.value == 2) {
        /**cementerio */
        this.form.tipo_reporte = this.form.reporte.tipo_reporte;
      }

      this.ListaReportes = [];
      this.ListaReportes.push({
        nombre: this.form.reporte.label,
        url: "reportes/get_reportes",
      });

      //estado de cuenta
      this.form.email = "";
      this.form.destinatario = "";
      this.openReportesLista = true;
      this.$vs.loading.close();
    },

    validarFecha() {
      if (this.form.fecha == "") {
        this.$vs.notify({
          title: "Error",
          text: "Seleccione la fecha del reporte.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "6000",
        });
        return false;
      } else {
        return true;
      }
    },

    validarRangoFecha() {
      if (this.form.fecha_inicio == "" || this.form.fecha_fin == "") {
        this.$vs.notify({
          title: "Error",
          text: "Seleccione el rango de fechas para el reporte.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "6000",
        });
        return false;
      } else {
        return true;
      }
    },
  },
  created() {
    this.form.modulo = this.modulos[1];
  },
};
</script>
