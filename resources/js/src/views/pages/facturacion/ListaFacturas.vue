<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full mb-1">
        <vs-button
          class="float-right"
          size="small"
          color="success"
          @click="TipoFormulario('facturar')"
        >
          <img class="cursor-pointer img-btn" src="@assets/images/cfdi.svg" />
          <span class="texto-btn">Crear CFDI</span>
        </vs-button>
        <vs-button
          class="float-right mr-12 hidden"
          size="small"
          color="primary"
          @click="openPlanesVenta = true"
        >
          <img class="cursor-pointer img-btn" src="@assets/images/shovel.svg" />
          <span class="texto-btn">Servicio de Exhumación</span>
        </vs-button>
      </div>
    </div>
    <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
      <vx-card
        no-radius
        title="Filtros de selección"
        refresh-content-action
        @refresh="reset"
        collapse-action
      >
        <div class="flex flex-wrap">
          <div
            class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2"
          >
            <label class="text-sm opacity-75">Mostrar</label>
            <v-select
              :options="mostrarOptions"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="mostrar"
              class="mb-4 sm:mb-0"
            />
          </div>
          <div
            class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2"
          >
            <label class="text-sm opacity-75">Estado</label>
            <v-select
              :options="estadosOptions"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="estado"
              class="mb-4 md:mb-0"
            />
          </div>
          <div
            class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2"
          >
            <label class="text-sm opacity-75">Filtrar Específico</label>
            <v-select
              :options="filtrosEspecificos"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="filtroEspecifico"
              class="mb-4 md:mb-0"
            />
          </div>
          <div
            class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-4 px-2"
          >
            <label class="text-sm opacity-75">Número de Control</label>
            <vs-input
              class="w-full"
              icon="search"
              maxlength="14"
              placeholder="Filtrar por Número de Control"
              v-model="serverOptions.numero_control"
              v-on:keyup.enter="get_data(1)"
              v-on:blur="get_data(1, 'blur')"
            />
          </div>
        </div>
      </vx-card>
    </div>

    <br />
    <vs-table
      :sst="true"
      :max-items="serverOptions.per_page.value"
      :data="[]"
      noDataText="0 Resultados"
    >
      <template slot="header">
        <h3>Listado de Facturas Emitidad</h3>
      </template>
      <template slot="thead">
        <vs-th>Núm. Servicio</vs-th>
        <vs-th>Fallecido</vs-th>
        <vs-th>Tipo Servicio</vs-th>
        <vs-th>Fecha</vs-th>

        <vs-th>Estatus</vs-th>
        <vs-th>Acciones</vs-th>
      </template>
      <template slot-scope="{ data }">
        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
          <vs-td :data="data[indextr].servicio_id">
            <span class="font-semibold">{{ data[indextr].servicio_id }}</span>
          </vs-td>
          <vs-td :data="data[indextr].nombre_afectado">
            {{ data[indextr].nombre_afectado }}
          </vs-td>
          <vs-td :data="data[indextr].tipo_solicitud_texto">
            {{ data[indextr].tipo_solicitud_texto }}
          </vs-td>
          <vs-td :data="data[indextr].fecha_solicitud_texto">
            <span class="font-medium">
              {{ data[indextr].fecha_solicitud_texto }}
            </span>
          </vs-td>

          <vs-td>
            <p
              v-if="data[indextr].status_texto == 'Cancelada'"
              class="font-medium text-danger"
            >
              {{ data[indextr].status_texto }}
            </p>
            <p
              v-else-if="data[indextr].status_texto == 'Por pagar'"
              class="font-medium"
            >
              {{ data[indextr].status_texto }}
            </p>
            <p
              v-else-if="data[indextr].status_texto == 'Pagada'"
              class="text-success font-medium"
            >
              {{ data[indextr].status_texto }}
            </p>
            <p v-else class="font-medium">
              {{ data[indextr].status_texto }}
            </p>
          </vs-td>
          <vs-td :data="data[indextr].id">
            <div class="flex flex-start py-1">
              <img
                class="cursor-pointer img-btn ml-auto mr-1"
                src="@assets/images/folder.svg"
                title="Expediente"
              />
            </div>
          </vs-td>
          <template class="expand-user" slot="expand"></template>
        </vs-tr>
      </template>
    </vs-table>
    <div>
      <vs-pagination
        v-if="verPaginado"
        :total="this.total"
        v-model="actual"
        class="mt-8"
      ></vs-pagination>
    </div>

    <FormularioCFDI
      :id_cfdi="id_cfdi"
      :tipo="TipodeFormulario"
      :show="verFormularioCFDI"
      @closeVentana="verFormularioCFDI = false"
    ></FormularioCFDI>
  </div>
</template>

<script>
//services para la facturacion
import facturacion from "@services/facturacion";

import FormularioCFDI from "../facturacion/FormularioCFDI";

/**VARIABLES GLOBALES */
import { mostrarOptions } from "@/VariablesGlobales";
import vSelect from "vue-select";
export default {
  components: {
    "v-select": vSelect,
    FormularioCFDI,
  },
  watch: {
    actual: function (newValue, oldValue) {
      (async () => {
        //await this.get_data(this.actual);
      })();
    },
    mostrar: function (newValue, oldValue) {
      (async () => {
        //await this.get_data(1);
      })();
    },
    estado: function (newVal, previousVal) {
      (async () => {
        //await this.get_data(1);
      })();
    },
  },
  data() {
    return {
      //variable
      mostrarOptions: mostrarOptions,
      mostrar: { label: "15", value: "15" },
      estado: { label: "Todas", value: "" },
      estadosOptions: [
        {
          label: "Todas",
          value: "",
        },
        {
          label: "Activas",
          value: "1",
        },
        {
          label: "Canceladas",
          value: "0",
        },
      ],
      filtroEspecifico: { label: "Núm. Factura", value: "1" },
      filtrosEspecificos: [
        {
          label: "Núm. Factura",
          value: "1",
        },
      ],
      serverOptions: {
        page: "",
        per_page: "",
        status: "",
        filtro_especifico_opcion: "",
        numero_control: "",
      },

      verPaginado: true,
      total: 0,
      actual: 1,
      TipodeFormulario: "",
      verFormularioCFDI: false,
      id_cfdi: 0,
    };
  },
  methods: {
    TipoFormulario(tipo) {
      this.TipodeFormulario = tipo;
      this.verFormularioCFDI = true;
    },
    reset(card) {
      card.removeRefreshAnimation(500);
      this.filtroEspecifico = { label: "Núm. Factura", value: "1" };
      this.serverOptions.numero_control = "";
      this.mostrar = { label: "15", value: "15" };
      this.estado = { label: "Todas", value: "" };
      //this.get_data(this.actual);
    },
    async get_data(page, evento = "") {
      if (evento == "blur") {
        if (
          this.serverOptions.numero_control == "" ||
          this.serverOptions.numero_control != ""
        ) {
          //la funcion no avanza

          return false;
        }
      }
      let self = this;
      if (funeraria.cancel) {
        funeraria.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = this.mostrar.value;
      this.serverOptions.status = this.estado.value;
      this.serverOptions.filtro_especifico_opcion = this.filtroEspecifico.value;

      try {
        let res = await funeraria.get_solicitudes_servicios(this.serverOptions);
        if (res.data.data) {
          //this.ventas = res.data.data;
          this.total = res.data.last_page;
          this.actual = res.data.current_page;
        }
        this.verPaginado = true;
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
  },
  created() {
    (async () => {
      //await this.get_data(this.actual);
    })();
  },
};
</script>
