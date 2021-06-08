<template>
  <div>
    <div class="w-full text-right">
      <vs-button
        class="w-full sm:w-full sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
        color="primary"
        @click="OpenFormularioCompras('agregar')"
      >
        <span>Registrar Compra</span>
      </vs-button>
    </div>

    <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
      <vx-card
        no-radius
        title="Filtros de selección"
        refresh-content-action
        @refresh="reset"
        :collapse-action="false"
      >
        <div class="flex flex-wrap">
          <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
            <label class="">Mostrar</label>
            <v-select
              :options="mostrarOptions"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="mostrar"
              class="w-full"
            />
          </div>
          <div class="w-full xl:w-3/12 mb-1 px-2 input-text">
            <label class="">Estado</label>
            <v-select
              :options="estadosOptions"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="estado"
              class="w-full"
            />
          </div>
          <div class="w-full sm:w-6/12 xl:w-3/12 input-text px-2">
            <label class="">Filtrar Específico</label>
            <v-select
              :options="filtrosEspecificos"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="filtroEspecifico"
              class="w-full"
            />
          </div>
          <div class="w-full sm:w-6/12 xl:w-3/12 input-text px-2">
            <label class="">Número de Control</label>
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
              />Filtrar por Nombre del Proveedor
            </h3>
          </div>
          <div class="w-full input-text px-2">
            <label class="">Nombre del Proveedor</label>
            <vs-input
              class="w-full"
              icon="search"
              placeholder="Filtrar por Nombre del Proveedor"
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
      :data="compras"
      noDataText="0 Resultados"
      class="tabla-datos"
    >
      <template slot="header">
        <h3>Listado de Compras a Proveedores</h3>
      </template>
      <template slot="thead">
        <vs-th>Núm. Registro</vs-th>
        <vs-th>Proveedor</vs-th>
        <vs-th>Ref. Factura/Nota</vs-th>
        <vs-th>Fecha</vs-th>
        <vs-th>$ Total Compra</vs-th>
        <vs-th>Estatus</vs-th>
        <vs-th>Acciones</vs-th>
      </template>
      <template slot-scope="{ data }">
        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
          <vs-td :data="data[indextr].num_compra">
            <span class="font-semibold">{{ data[indextr].num_compra }}</span>
          </vs-td>
          <vs-td :data="data[indextr].proveedor">
            {{ data[indextr].proveedor.razon_social }}
          </vs-td>
          <vs-td :data="data[indextr].folio_referencia">
            {{ data[indextr].folio_referencia }}
          </vs-td>
          <vs-td :data="data[indextr].fecha_compra_texto">
            <span class="font-medium">
              {{ data[indextr].fecha_compra_texto }}
            </span>
          </vs-td>
          <vs-td :data="data[indextr].total_compra">
            <span class="font-medium">
              $
              {{ data[indextr].total_compra | numFormat("0,000.00") }}
            </span>
          </vs-td>
          <vs-td>
            <p v-if="data[indextr].status_texto == 'Cancelada'">
              {{ data[indextr].status_texto }}
              <span class="dot-danger"></span>
            </p>
            <p v-else-if="data[indextr].status_texto == 'Por pagar'">
              {{ data[indextr].status_texto }}
              <span class="dot-warning"></span>
            </p>

            <p v-else-if="data[indextr].status_texto == 'Activa'">
              Sin Contrato
              <span class="dot-warning"></span>
            </p>

            <p v-else-if="data[indextr].status_texto == 'Pagada'">
              {{ data[indextr].status_texto }}
              <span class="dot-success"></span>
            </p>
          </vs-td>
          <vs-td :data="data[indextr].id">
            <div class="flex justify-center">
              <img
                class="cursor-pointer img-btn-20 mx-3"
                src="@assets/images/folder.svg"
                title="Expediente"
                @click="ConsultarVenta(data[indextr].servicio_id)"
              />

              <img
                v-if="data[indextr].status_b >= 1"
                class="img-btn-22 mx-3"
                src="@assets/images/trash.svg"
                title="Cancelar Contrato"
                @click="cancelarVenta(data[indextr].servicio_id)"
              />
              <img
                v-else
                class="img-btn-22 mx-3"
                src="@assets/images/trash-open.svg"
                title="Este contrato ya fue cancelado, puede hacer click aquí para consultar"
                @click="ConsultarVentaAcuse(data[indextr].servicio_id)"
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

    <Password
      :show="openStatus"
      :callback-on-success="callback"
      @closeVerificar="closeStatus"
      :accion="accionNombre"
    ></Password>

    <ReportesServicio
      :verAcuse="verAcuse"
      :show="openReportes"
      @closeListaReportes="closeListaReportes"
      :id_compra="id_compra"
    ></ReportesServicio>

    <CancelarVenta
      :show="openCancelar"
      @closeCancelarVenta="openCancelar = false"
      @ConsultarVenta="ConsultarVenta"
      :id_compra="id_compra"
    ></CancelarVenta>

    <FormularioCompras
      :tipo="tipoFormulario"
      :show="verFormularioCompras"
      @closeVentana="verFormularioCompras = false"
    ></FormularioCompras>
  </div>
</template>

<script>
//planes de venta
import funeraria from "@services/funeraria";

import FormularioCompras from "@pages/inventarios/compras/FormularioCompras";

import ReportesServicio from "@pages/funeraria/servicios_funerarios/ReportesServicio";
import CancelarVenta from "@pages/funeraria/servicios_funerarios/CancelarVenta";

//componente de password
import Password from "@pages/confirmar_password";

import usuarios from "@services/Usuarios";

import inventario from "@services/inventario";

/**VARIABLES GLOBALES */
import { mostrarOptions } from "@/VariablesGlobales";
import vSelect from "vue-select";
export default {
  components: {
    "v-select": vSelect,
    Password,
    FormularioCompras,
    ReportesServicio,
    CancelarVenta,
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
      openCancelar: false,
      openReportes: false,
      verFormularioCompras: false,
      tipoFormulario: "",
      //variable
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
      filtroEspecifico: { label: "Núm. Registro", value: "1" },
      filtrosEspecificos: [
        {
          label: "Núm. Registro",
          value: "1",
        },
        {
          label: "Núm. Factura / Nota",
          value: "2",
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
      verPaginado: true,
      total: 0,
      actual: 1,
      compras: [],
      //fin variables
      openStatus: false,
      callback: Function,
      accionNombre: "",
      /**opciones para filtrar la peticion del server */
      id_compra: 0 /**para consultar los reportesw */,
      /**datos del form */
    };
  },
  methods: {
    reset(card) {
      card.removeRefreshAnimation(500);
      this.filtroEspecifico = { label: "Núm. Registro", value: "1" };
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
      if (inventario.cancel) {
        inventario.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = this.mostrar.value;
      this.serverOptions.status = this.estado.value;
      this.serverOptions.filtro_especifico_opcion = this.filtroEspecifico.value;

      try {
        let res = await inventario.get_compras(this.serverOptions);
        if (res.data.data) {
          this.compras = res.data.data;
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

    closeStatus() {
      this.openStatus = false;
    },

    ConsultarVenta(id_compra) {
      this.id_compra = id_compra;
      this.openReportes = true;
    },

    OpenFormularioCompras(tipo) {
      this.tipoFormulario = tipo;
      this.verFormularioCompras = true;
    },

    cancelarVenta(id_compra) {
      this.id_compra = id_compra;
      this.openCancelar = true;
    },

    closeListaReportes() {
      this.openReportes = false;
      this.verAcuse = false;
      this.id_compra = 0;
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
