<!--
IDS DE LOS PERMISOS QUE APLICAN EN ESTE MODULO DE CLIENTES
registrar clientes 11
modificar informacion 12
eliminar clientes 13
consultar información 14
consultar estados de cuenta15

para saber si se debe desplegar algun boton o accion se hace llamando la funcion global
this.$modulo.permiso(this.$route.path,11)
con la ruta especifica del modulo que se desea consultar y el id del permiso

-->
<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full sm:w-12/12 ml-auto md:w-1/5 lg:w-1/5 xl:w-1/5 mb-1 px-2">
        <vs-button
          color="success"
          size="small"
          class="w-full ml-auto"
          @click="formulario('agregar')"
        >
          <img class="cursor-pointer img-btn" src="@assets/images/plus.svg" />
          <span class="texto-btn">Registrar Cliente</span>
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
              maxlength="75"
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
        <vs-th>Status</vs-th>
        <vs-th>Acciones</vs-th>
      </template>
      <template slot-scope="{data}">
        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
          <vs-td :data="data[indextr].id">
            <span class="font-semibold">{{data[indextr].id}}</span>
          </vs-td>
          <vs-td :data="data[indextr].nombre">{{data[indextr].nombre}}</vs-td>
          <vs-td :data="data[indextr].direccion">{{data[indextr].direccion}}</vs-td>
          <vs-td :data="data[indextr].celular">
            <span class="font-medium">{{data[indextr].celular}}</span>
          </vs-td>

          <vs-td :data="data[indextr].status">
            <p v-if="data[indextr].status==1" class="text-success font-medium">Activo</p>
            <p v-else class="text-danger font-medium">Sin Acceso</p>
          </vs-td>
          <vs-td :data="data[indextr].id_user">
            <div class="flex flex-start">
              <img class="mr-3 hidden" style="width:20px;" src="@assets/images/pdf.svg" alt />

              <vs-button
                class="ml-auto"
                title="Editar"
                size="large"
                icon-pack="feather"
                icon="icon-edit"
                color="dark"
                type="flat"
                @click="openModificar(data[indextr].id)"
              ></vs-button>
              <vs-button
                class="mr-auto"
                v-if="data[indextr].status==1"
                title="Cancelar"
                icon-pack="feather"
                size="large"
                icon="icon-shield-off"
                color="danger"
                type="flat"
                @click="deleteCliente(data[indextr].id,data[indextr].nombre)"
              ></vs-button>
              <vs-button
                class="mr-auto"
                v-else
                title="Activar"
                icon-pack="feather"
                size="large"
                icon="icon-shield"
                color="success"
                type="flat"
                @click="altaCliente(data[indextr].id,data[indextr].nombre)"
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
    <FormularioClientes
      :id_cliente="id_cliente_modificar"
      :tipo="tipoFormulario"
      :show="verFormularioClientes"
      @closeVentana="verFormularioClientes = false"
      @retornar_id="retorno_id"
    ></FormularioClientes>
  </div>
</template>

<script>
//planes de venta
import Reporteador from "@pages/Reporteador";

import clientes from "@services/clientes";

import FormularioClientes from "@pages/clientes/FormularioClientes";

//componente de password
import Password from "@pages/confirmar_password";

/**VARIABLES GLOBALES */
import { mostrarOptions, PermisosModulo } from "@/VariablesGlobales";

import vSelect from "vue-select";

export default {
  components: {
    "v-select": vSelect,
    Password,
    FormularioClientes,
    Reporteador
  },
  watch: {
    actual: function(newValue, oldValue) {
      this.get_data(this.actual);
    },
    mostrar: function(newValue, oldValue) {
      this.get_data(1);
    },
    estado: function(newVal, previousVal) {
      this.get_data(1);
    }
  },
  data() {
    return {
      //variable
      ListaReportes: [],
      PermisosModulo: PermisosModulo,
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

      verPaginado: true,
      total: 0,
      actual: 1,
      clientes: [],
      //fin variables
      openStatus: false,
      callback: Function,
      accionNombre: "",
      datosModifcar: {},
      tipoFormulario: "",
      verFormularioClientes: false,
      verModificar: false,
      id_cliente_modificar: 0,
      selected: [],
      users: [],
      /**opciones para filtrar la peticion del server */
      /**user id para bajas y altas */
      cliente_id: "",
      request: {
        venta_id: "",
        email: ""
      }
    };
  },
  methods: {
    reset(card) {
      card.removeRefreshAnimation(500);
      this.filtroEspecifico = {
        label: "Núm. Cliente",
        value: "1"
      };
      this.serverOptions.numero_control = "";
      this.mostrar = { label: "15", value: "15" };
      this.estado = { label: "Todos", value: "" };
      this.serverOptions.cliente = "";
      this.get_data(this.actual);
    },

    get_data(page, evento = "") {
      if (evento == "blur") {
        if (
          this.serverOptions.cliente != "" ||
          this.serverOptions.cliente == ""
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
      if (clientes.cancel) {
        clientes.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = this.mostrar.value;
      this.serverOptions.status = this.estado.value;
      this.serverOptions.filtro_especifico_opcion = this.filtroEspecifico.value;
      clientes
        .get_clientes(this.serverOptions)
        .then(res => {
          //console.log("get_data -> res", res);
          this.clientes = res.data.data;
          this.total = res.data.last_page;
          this.actual = res.data.current_page;
          this.verPaginado = true;
          this.$vs.loading.close();
        })
        .catch(err => {
          this.$vs.loading.close();
          this.ver = true;
          if (err.response) {
            if (err.response.status == 403) {
              /**FORBIDDEN ERROR */
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
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},

    //eliminar usuario logicamente

    closeModificar() {
      this.verModificar = false;
    },

    closeStatus() {
      this.openStatus = false;
    },

    formulario(tipo) {
      this.tipoFormulario = tipo;
      this.verFormularioClientes = true;
    },
    openModificar(id_cliente) {
      this.tipoFormulario = "modificar";
      this.id_cliente_modificar = id_cliente;
      this.verFormularioClientes = true;
    },
    retorno_id(dato) {
      this.get_data(this.actual);
    },
    deleteCliente(id_cliente, nombre) {
      this.accionNombre = "deshabilitar cliente " + nombre;
      this.cliente_id = id_cliente;
      this.openStatus = true;
      this.callback = this.delete_cliente;
    },

    altaCliente(id_cliente, nombre) {
      this.accionNombre = "Habilitar cliente " + nombre;
      this.cliente_id = id_cliente;
      this.openStatus = true;
      this.callback = this.habilitar_cliente;
    },
    delete_cliente() {
      this.$vs.loading();
      let datos = {
        cliente_id: this.cliente_id
      };
      clientes
        .delete_cliente(datos)
        .then(res => {
          this.$vs.loading.close();
          this.get_data(this.actual);
          if (res.data == 1) {
            this.$vs.notify({
              title: "Deshabilitar Cliente",
              text: "Se ha deshabilitado al cliente exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000
            });
          } else {
            this.$vs.notify({
              title: "Deshabilitar Cliente",
              text: "No se realizaron cambios.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 5000
            });
          }
        })
        .catch(err => {
          this.$vs.loading.close();
          if (err.response) {
            if (err.response.status == 403) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Permiso denegado",
                text:
                  "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 8000
              });
            } else if (err.response.status == 422) {
              /**error de validacion */
              this.errores = err.response.data.error;
            }
          }
        });
    },
    habilitar_cliente() {
      this.$vs.loading();
      let datos = {
        cliente_id: this.cliente_id
      };
      clientes
        .alta_cliente(datos)
        .then(res => {
          this.$vs.loading.close();
          this.get_data(this.actual);
          if (res.data == 1) {
            this.$vs.notify({
              title: "Habilitar Cliente",
              text: "Se ha habilitado al cliente exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000
            });
          } else {
            this.$vs.notify({
              title: "Habilitar Cliente",
              text: "No se realizaron cambios.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 5000
            });
          }
        })
        .catch(err => {
          this.$vs.loading.close();
          if (err.response) {
            console.log("habilitar_cliente -> err.response", err.response);
            if (err.response.status == 403) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Permiso denegado",
                text:
                  "Verifique sus permisos con el administrador del sistema.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time: 8000
              });
            } else if (err.response.status == 422) {
              /**error de validacion */
              this.errores = err.response.data.error;
            }
          }
        });
    }
  },
  created() {
    this.get_data(this.actual);
  }
};
</script>
