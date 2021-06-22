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

          <div class="w-full xl:w-4/12 mb-1 px-2 input-text" v-show="ver_fecha">
            <label class="">
              Rango de Fechas año/mes/dia
              <span>(*)</span>
            </label>
            <flat-pickr
              name="rango_fechas"
              data-vv-as=" "
              v-validate:rango_fechas_validacion_computed.immediate="'required'"
              :config="configdateTimePickerRange"
              v-model="form.rango_fechas"
              placeholder="Rango de fechas del reporte"
              class="w-full"
              @on-close="onCloseDate"
            />
          </div>
          <div class="w-full xl:w-4/12 px-2 input-text" v-show="!ver_fecha">
            <label>
              Fecha del Reporte
              <span>(*)</span>
            </label>
            <flat-pickr
              name="fecha"
              data-vv-as=" "
              v-validate:fecha_validacion_computed.immediate="'required'"
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
                    title="Ver Nota de Pago"
                    @click="
                      openReporte(
                        'reporte de pago',
                        '/pagos/recibo_de_pago/',
                        4,
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
    </div>
  </div>
</template>

<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
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
  },
  computed: {
    fecha_validacion_computed: function () {
      return this.form.fecha;
    },
    rango_fechas_validacion_computed: function () {
      return this.form.rango_fechas;
    },
    ver_fecha: function () {
      let ver = true;
      if (this.form.modulo.value == 1) {
        /**inventarios */
        if (this.form.reporte.value == 1) {
          ver = false;
        }
      }
      return ver;
    },
  },
  watch: {
    "form.modulo": function (newValue, oldValue) {
      /**Cargando los reportes segun el modulo */
      this.reportes = [{ label: "Seleccionar", value: "" }];
      if (newValue.value != 1) {
        this.form.reporte = this.reportes[0];
        return;
      }
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
      }
      this.form.reporte = this.reportes[1];
    },
  },
  data() {
    return {
      configdateTimePicker: configdateTimePicker,
      configdateTimePickerRange: configdateTimePickerRange,
      form: {
        modulo: { label: "Seleccionar", value: "" },
        reporte: { label: "Seleccionar", value: "", detalle: "" },
        rango_fechas: "",
        fecha: "",
      },
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
    async get_data(page, evento = "") {},
    reset(card) {
      card.removeRefreshAnimation(500);
    },

    openReporte(nombre_reporte = "", link = "", parametro = "", tipo = "") {},

    onCloseDate(selectedDates, dateStr, instance) {
      /**se valdiad que se busque la informacion solo en los casos donde la fechas cambien */
      let buscar = false;
      if (selectedDates.length == 0) {
        /**no hay fechas que buscar */
      } else {
        /**hay fechas que buscar */
      }
    },
  },
  created() {
    this.form.modulo = this.modulos[1];
  },
};
</script>
