<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full mb-1">
        <vs-button
          class="float-right"
          size="small"
          color="success"
          @click="TipoFormularioSolicitud('agregar')"
        >
          <img class="cursor-pointer img-btn" src="@assets/images/plus.svg" />
          <span class="texto-btn">Nuevo Servicio Funerario</span>
        </vs-button>
        <vs-button
          class="float-right mr-12"
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

        <div class="flex flex-wrap">
          <div class="w-full px-2">
            <h3 class="text-base font-semibold my-3">
              <feather-icon
                icon="UserIcon"
                class="mr-2"
                svgClasses="w-5 h-5"
              />Filtrar por Nombre del Fallecido
            </h3>
          </div>
          <div
            class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4 px-2"
          >
            <label class="text-sm opacity-75">Nombre del Fallecido</label>
            <vs-input
              class="w-full"
              icon="search"
              placeholder="Filtrar por Nombre del Fallecido"
              v-model="serverOptions.fallecido"
              v-on:keyup.enter="get_data(1)"
              v-on:blur="get_data(1, 'blur')"
              maxlength="75"
            />
          </div>
        </div>
      </vx-card>
    </div>

    <br />
    <vs-table
      :sst="true"
      :max-items="serverOptions.per_page.value"
      :data="ventas"
      noDataText="0 Resultados"
    >
      <template slot="header">
        <h3>Listado de Servicios Funerarios Atendidos</h3>
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
          <vs-td :data="data[indextr].ventas_planes_id">
            <span class="font-semibold">{{
              data[indextr].ventas_planes_id
            }}</span>
          </vs-td>
          <vs-td :data="data[indextr].nombre">{{ data[indextr].nombre }}</vs-td>
          <vs-td :data="data[indextr].venta_plan.tipo_financiamiento_texto">{{
            data[indextr].venta_plan.tipo_financiamiento_texto
          }}</vs-td>
          <vs-td :data="data[indextr].numero_solicitud">
            <span class="font-medium">{{
              data[indextr].numero_solicitud_texto
            }}</span>
          </vs-td>

          <vs-td :data="data[indextr].operacion_status">
            <p v-if="data[indextr].operacion_status == 1" class="font-medium">
              {{ data[indextr].status_texto }}
            </p>
            <p
              v-else-if="data[indextr].operacion_status == 2"
              class="font-medium text-success"
            >
              {{ data[indextr].status_texto }}
            </p>
            <p v-else class="text-danger font-medium">
              {{ data[indextr].status_texto }}
            </p>
          </vs-td>

          <vs-td :data="data[indextr].id">
            <div class="flex flex-start py-1">
              <img
                class="cursor-pointer img-btn ml-auto"
                src="@assets/images/folder.svg"
                title="Expediente"
                @click="ConsultarVenta(data[indextr].ventas_planes_id)"
              />
              <img
                class="cursor-pointer img-btn ml-6 mr-6"
                src="@assets/images/edit.svg"
                title="Modificar Contrato"
                @click="openModificar(data[indextr].ventas_planes_id)"
              />
              <img
                width="24"
                v-if="data[indextr].operacion_status >= 1"
                class="cursor-pointer mr-auto"
                src="@assets/images/trash.svg"
                title="Cancelar Contrato"
                @click="cancelarVenta(data[indextr].ventas_planes_id)"
              />
              <img
                width="24"
                v-else
                class="cursor-pointer mr-auto"
                src="@assets/images/trash-open.svg"
                title="Esta venta ya fue cancelada, puede hacer click aquí para consultar"
                @click="ConsultarVentaAcuse(data[indextr].ventas_planes_id)"
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

    <FormularioVentas
      :id_venta="id_venta_modificar"
      :tipo="tipoFormulario"
      :show="verFormularioVentas"
      @closeVentana="verFormularioVentas = false"
      @ver_pdfs_nueva_venta="ConsultarVenta"
    ></FormularioVentas>

    <FormularioSolicitud
      :id_venta="id_solicitud_modificar"
      :tipo="tipoFormularioSolicitud"
      :show="verFormularioSolicitud"
      @closeVentana="verFormularioSolicitud = false"
      @ver_pdfs_nueva_venta="ConsultarVenta"
    ></FormularioSolicitud>

    <Password
      :show="openStatus"
      :callback-on-success="callback"
      @closeVerificar="closeStatus"
      :accion="accionNombre"
    ></Password>

    <ReportesVentas
      :verAcuse="verAcuse"
      :show="openReportes"
      @closeListaReportes="closeListaReportes"
      :id_venta="id_venta"
    ></ReportesVentas>

    <CancelarVenta
      :show="openCancelar"
      @closeCancelarVenta="openCancelar = false"
      @ConsultarVenta="ConsultarVenta"
      :id_venta="id_venta"
    ></CancelarVenta>

    <PlanesVenta
      :show="openPlanesVenta"
      @closePlanesFuneraria="openPlanesVenta = false"
    ></PlanesVenta>
  </div>
</template>

<script>
//planes de venta
import planes from "@services/planes";

import FormularioVentas from "../servicios_funerarios/FormularioVentas";
import FormularioSolicitud from "../servicios_funerarios/FormularioSolicitud";

import ReportesVentas from "../servicios_funerarios/ReportesVentas";
import CancelarVenta from "../servicios_funerarios/CancelarVenta";

//componente de password
import Password from "@pages/confirmar_password";

import usuarios from "@services/Usuarios";
/**VARIABLES GLOBALES */
import { mostrarOptions } from "@/VariablesGlobales";
import vSelect from "vue-select";
import PlanesVenta from "@pages/funeraria/ventas/PlanesVentas";
export default {
  components: {
    "v-select": vSelect,
    Password,
    FormularioVentas,
    ReportesVentas,
    CancelarVenta,
    PlanesVenta,
    FormularioSolicitud,
  },
  watch: {
    actual: function (newValue, oldValue) {
      (async () => {
        await this.get_data(this.actual);
      })();
    },
    mostrar: function (newValue, oldValue) {
      (async () => {
        await this.get_data(1);
      })();
    },
    estado: function (newVal, previousVal) {
      (async () => {
        await this.get_data(1);
      })();
    },
  },
  data() {
    return {
      verAcuse: false,
      openPlanesVenta: false,
      openCancelar: false,
      openReportes: false,
      verFormularioVentas: false,
      tipoFormulario: "",
      //variable
      tipo_propiedades: [],
      propiedad: { label: "Todos", value: "" },
      openReportesLista: false,
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
      filtroEspecifico: { label: "Núm. Servicio", value: "1" },
      filtrosEspecificos: [
        {
          label: "Núm. Servicio",
          value: "1",
        },
      ],
      serverOptions: {
        page: "",
        per_page: "",
        status: "",
        filtro_especifico_opcion: "",
        numero_control: "",
        fallecido: "",
      },
      activeTab: 0,
      verPaginado: true,
      total: 0,
      actual: 1,
      ventas: [],
      //fin variables
      openStatus: false,
      callback: Function,
      accionNombre: "",
      verAgregar: false,
      verModificar: false,
      id_venta_modificar: 0,
      /**opciones para filtrar la peticion del server */
      id_venta: 0 /**para consultar los reportesw */,

      /**datos del form */
      id_solicitud_modificar: 0,
      tipoFormularioSolicitud: "",
      verFormularioSolicitud: false,
    };
  },
  methods: {
    reset(card) {
      card.removeRefreshAnimation(500);
      this.filtroEspecifico = { label: "Núm. Servicio", value: "1" };
      this.serverOptions.numero_control = "";
      this.mostrar = { label: "15", value: "15" };
      this.estado = { label: "Todas", value: "" };
      this.serverOptions.fallecido = "";
      //this.get_data(this.actual);
    },
    async get_data(page, evento = "") {
      if (evento == "blur") {
        if (
          this.serverOptions.fallecido != "" ||
          this.serverOptions.fallecido == ""
        ) {
          //la funcion no avanza

          return false;
        }
        if (
          this.serverOptions.numero_control == "" ||
          this.serverOptions.numero_control != ""
        ) {
          //la funcion no avanza

          return false;
        }
      }
      let self = this;
      if (planes.cancel) {
        planes.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = this.mostrar.value;
      this.serverOptions.status = this.estado.value;
      this.serverOptions.filtro_especifico_opcion = this.filtroEspecifico.value;

      try {
        let res = await planes.get_ventas(this.serverOptions);
        if (res.data.data) {
          this.ventas = res.data.data;
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

    //eliminar usuario logicamente

    closeModificar() {
      this.verModificar = false;
    },

    closeStatus() {
      this.openStatus = false;
    },

    ConsultarVenta(id_venta) {
      this.id_venta = id_venta;
      this.openReportes = true;
    },
    ConsultarVentaAcuse(id_venta) {
      this.verAcuse = true;
      this.id_venta = id_venta;
      this.openReportes = true;
    },

    openModificar(id_venta) {
      this.tipoFormulario = "modificar";
      this.id_venta_modificar = id_venta;
      this.verFormularioVentas = true;
    },

    cancelarVenta(id_venta) {
      this.id_venta = id_venta;
      this.openCancelar = true;
    },
    formulario(tipo) {
      this.tipoFormulario = tipo;
      this.verFormularioVentas = true;
    },

    TipoFormularioSolicitud(tipo) {
      this.tipoFormularioSolicitud = tipo;
      this.verFormularioSolicitud = true;
    },

    closeListaReportes() {
      this.openReportes = false;
      this.verAcuse = false;
      this.id_venta = 0;
      (async () => {
        await this.get_data(this.actual);
      })();
    },
    closeCancelarVentaRefrescar() {
      this.openCancelar = false;
      (async () => {
        await this.get_data(this.actual);
      })();
    },
  },
  created() {
    (async () => {
      await this.get_data(this.actual);
    })();
  },
};
</script>
