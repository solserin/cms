<template>
  <div>
    <vs-tabs alignment="left" position="top" v-model="activeTab">
      <vs-tab label="CONTROL DE CLIENTES" icon="supervisor_account" class="pb-5"></vs-tab>
    </vs-tabs>
    <div class="tab-content mt-1" v-show="activeTab==0">
      <div class="flex flex-wrap">
        <div class="w-full sm:w-12/12 ml-auto md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2">
          <vs-button
            class="ml-auto"
            icon-pack="feather"
            icon="icon-shopping-cart"
            color="success"
            @click="verAgregar=true"
          >Registrar Cliente</vs-button>
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
            <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2">
              <label class="text-sm opacity-75">Mostrar</label>
              <v-select
                :options="mostrarOptions"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="mostrar"
                class="mb-4 sm:mb-0"
              />
            </div>
            <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2">
              <label class="text-sm opacity-75">Estado</label>
              <v-select
                :options="estadosOptions"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="estado"
                class="mb-4 md:mb-0"
              />
            </div>
            <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2">
              <label class="text-sm opacity-75">Filtrar Específico</label>
              <v-select
                :options="filtrosEspecificos"
                :clearable="false"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                v-model="filtroEspecifico"
                class="mb-4 md:mb-0"
              />
            </div>
            <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-4 px-2">
              <label class="text-sm opacity-75">{{this.filtroEspecifico.label}}</label>
              <vs-input
                class="w-full"
                icon="search"
                maxlength="14"
                placeholder="Filtrar por dato específico"
                v-model="serverOptions.numero_control"
                v-on:keyup.enter="get_data(1)"
                v-on:blur="get_data(1,'blur')"
              />
            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="w-full px-2">
              <h3 class="text-base font-semibold my-3">
                <feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />Filtrar por Nombre del Cliente
              </h3>
            </div>
            <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4 px-2">
              <label class="text-sm opacity-75">Nombre del Cliente</label>
              <vs-input
                class="w-full"
                icon="search"
                placeholder="Filtrar por Nombre del Cliente"
                v-model="serverOptions.cliente"
                v-on:keyup.enter="get_data(1)"
                v-on:blur="get_data(1,'blur')"
                maxlength="75"
              />
            </div>
          </div>
        </vx-card>
      </div>

      <br />
      <vs-table
        :sst="true"
        @search="handleSearch"
        @change-page="handleChangePage"
        @sort="handleSort"
        v-model="selected"
        :max-items="serverOptions.per_page.value"
        :data="clientes"
        stripe
        noDataText="0 Resultados"
      >
        <template slot="header">
          <h3 class="pb-5 text-primary">Listado de Clientes Registrados</h3>
        </template>
        <template slot="thead">
          <vs-th>Núm. Cliente</vs-th>
          <vs-th>Nombre</vs-th>
          <vs-th>Domicilio</vs-th>
          <vs-th>Celular</vs-th>
          <vs-th>Email</vs-th>
          <vs-th>RFC</vs-th>
          <vs-th>Acciones</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].id">
              <span class="font-semibold">{{data[indextr].id}}</span>
            </vs-td>
            <vs-td :data="data[indextr].nombre">{{data[indextr].nombre}}</vs-td>
            <vs-td :data="data[indextr].uso_venta">{{data[indextr].uso_venta}}</vs-td>
            <vs-td :data="data[indextr].numero_solicitud">
              <span class="font-medium">{{data[indextr].numero_solicitud}}</span>
            </vs-td>
            <vs-td :data="data[indextr].numero_convenio">
              <span class="font-medium">{{data[indextr].numero_convenio}}</span>
            </vs-td>

            <vs-td :data="data[indextr].status">
              <p v-if="data[indextr].status==1">
                <span class="flex items-center px-2 py-1 rounded">
                  <div class="h-3 w-3 rounded-full mr-2" :class="'bg-success'"></div>Activa
                </span>
              </p>
              <p v-else>
                <span class="flex items-center px-2 py-1 rounded">
                  <div class="h-3 w-3 rounded-full mr-2" :class="'bg-danger'"></div>Cancelada
                </span>
              </p>
            </vs-td>
            <vs-td :data="data[indextr].id_user">
              <div class="flex flex-start">
                <img class="mr-3" style="width:20px;" src="@assets/images/pdf.svg" alt />

                <vs-button
                  title="Editar"
                  size="large"
                  icon-pack="feather"
                  icon="icon-edit"
                  color="dark"
                  type="flat"
                  @click="openModificar(data[indextr].id)"
                ></vs-button>
                <vs-button
                  v-if="data[indextr].status==1"
                  title="Cancelar"
                  icon-pack="feather"
                  size="large"
                  icon="icon-shield-off"
                  color="danger"
                  type="flat"
                  @click="deleteUsuario(data[indextr].id_user,data[indextr].nombre)"
                ></vs-button>
                <vs-button
                  v-else
                  title="Activar"
                  icon-pack="feather"
                  size="large"
                  icon="icon-shield"
                  color="success"
                  type="flat"
                  @click="habilitarUsuario(data[indextr].id_user,data[indextr].nombre)"
                ></vs-button>
              </div>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
      <div>
        <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-8"></vs-pagination>
      </div>
      <pre ref="pre"></pre>
    </div>

    <Password
      :show="openStatus"
      :callback-on-success="callback"
      @closeVerificar="closeStatus"
      :accion="accionNombre"
    ></Password>
    <Reporteador
      :header="'consultar reporte de venta'"
      :show="openReportesLista"
      :listadereportes="ListaReportes"
      :request="request"
      @closeReportes="openReportesLista=false;"
    ></Reporteador>
    <NuevoCliente
      :show="verAgregar"
      @closeVentana="verAgregar = false"
      @ver_pdfs_nueva_venta="alert(1)"
    ></NuevoCliente>
  </div>
</template>

<script>
//planes de venta
import Reporteador from "@pages/Reporteador";

import cementerio from "@services/cementerio";

import NuevoCliente from "@pages/clientes/NuevoCliente";

//componente de password
import Password from "@pages/confirmar_password";

/**VARIABLES GLOBALES */
import { mostrarOptions } from "@/VariablesGlobales";
import vSelect from "vue-select";

export default {
  components: {
    "v-select": vSelect,
    Password,
    NuevoCliente,
    Reporteador
  },
  watch: {
    actual: function(newValue, oldValue) {
      //this.get_data(this.actual);
    },
    mostrar: function(newValue, oldValue) {
      //this.get_data(1);
    },
    estado: function(newVal, previousVal) {
      //this.get_data(1);
    }
  },
  data() {
    return {
      //variable
      ListaReportes: [],

      openReportesLista: false,
      mostrarOptions: mostrarOptions,
      mostrar: { label: "15", value: "15" },
      estado: { label: "Todas", value: "" },
      estadosOptions: [
        {
          label: "Todos",
          value: ""
        },
        {
          label: "Activos",
          value: "1"
        },
        {
          label: "Cancelados",
          value: "0"
        }
      ],
      filtroEspecifico: { label: "Núm. Cliente", value: "1" },
      filtrosEspecificos: [
        {
          label: "Núm. Cliente",
          value: "1"
        },
        {
          label: "Núm. RFC",
          value: "2"
        },
        {
          label: "Núm. Celular",
          value: "3"
        },
        {
          label: "Email",
          value: "4"
        }
      ],
      serverOptions: {
        page: "",
        per_page: "",
        status: "",
        filtro_especifico_opcion: "",
        numero_control: "",
        cliente: ""
      },
      activeTab: 0,
      verPaginado: true,
      total: 0,
      actual: 1,
      clientes: [],
      //fin variables
      openStatus: false,
      callback: Function,
      accionNombre: "",
      datosModifcar: {},
      verAgregar: false,
      verModificar: false,
      id_cliente_modificar: 0,
      selected: [],
      users: [],
      /**opciones para filtrar la peticion del server */
      /**user id para bajas y altas */
      user_id: "",
      request: {
        venta_id: "",
        email: ""
      }
    };
  },
  methods: {
    reset(card) {
      card.removeRefreshAnimation(500);
      this.filtroEspecifico = { label: "Núm. Solicitud", value: "1" };
      this.serverOptions.numero_control = "";
      this.mostrar = { label: "15", value: "15" };
      this.estado = { label: "Todas", value: "" };
      this.serverOptions.cliente = "";
      //this.get_data(this.actual);
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
    openModificar(id_cliente) {
      this.id_cliente_modificar = id_cliente;
      this.verModificar = true;
    },

    //eliminar usuario logicamente

    closeModificar() {
      this.verModificar = false;
    },

    closeStatus() {
      this.openStatus = false;
    }
  },
  created() {}
};
</script>
