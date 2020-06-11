<template>
  <div>
    <vs-tabs alignment="left" position="top" v-model="activeTab">
      <vs-tab label="FILTROS DE BÚSQUEDA" icon="supervisor_account" class="pb-5"></vs-tab>
      <vs-tab label="INVENTARIO GRÁFICO" icon="location_on" class="pb-5"></vs-tab>
    </vs-tabs>

    <div class="tab-content mt-1" v-show="activeTab==0">
      <div class="flex flex-wrap">
        <div class="w-full sm:w-12/12 ml-auto md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2">
          <vs-button
            class="ml-auto"
            icon-pack="feather"
            icon="icon-shopping-cart"
            color="success"
            @click="formulario('agregar')"
          >Nueva Venta</vs-button>
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
              <label class="text-sm opacity-75">Número de Control</label>
              <vs-input
                class="w-full"
                icon="search"
                maxlength="14"
                placeholder="Filtrar por Número de Control"
                v-model="serverOptions.numero_control"
                v-on:keyup.enter="get_data(1)"
                v-on:blur="get_data(1,'blur')"
              />
            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="w-full px-2">
              <h3 class="text-base font-semibold my-3">
                <feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />Filtrar por Nombre del Titular
              </h3>
            </div>
            <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4 px-2">
              <label class="text-sm opacity-75">Nombre del Titular</label>
              <vs-input
                class="w-full"
                icon="search"
                placeholder="Filtrar por Nombre del Titular"
                v-model="serverOptions.titular"
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
        :data="ventas"
        stripe
        noDataText="0 Resultados"
      >
        <template slot="header">
          <h3 class="pb-5 text-primary">Listado de Ventas Realizadas del Cementerio</h3>
        </template>
        <template slot="thead">
          <vs-th>Núm. Venta</vs-th>
          <vs-th>Titular</vs-th>
          <vs-th>Uso Venta</vs-th>
          <vs-th>Solicitud</vs-th>
          <vs-th>Convenio</vs-th>
          <vs-th>Título</vs-th>
          <vs-th>Ubicacion</vs-th>
          <vs-th>Estatus</vs-th>
          <vs-th>Acciones</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].id">
              <span class="font-semibold">{{data[indextr].id}}</span>
            </vs-td>
            <vs-td :data="data[indextr].cliente_nombre">{{data[indextr].cliente_nombre}}</vs-td>
            <vs-td :data="data[indextr].uso_venta">{{data[indextr].uso_venta}}</vs-td>
            <vs-td :data="data[indextr].numero_solicitud">
              <span class="font-medium">{{data[indextr].numero_solicitud}}</span>
            </vs-td>
            <vs-td :data="data[indextr].numero_convenio">
              <span class="font-medium">{{data[indextr].numero_convenio}}</span>
            </vs-td>
            <vs-td :data="data[indextr].numero_titulo">
              <span class="font-medium">{{data[indextr].numero_titulo}}</span>
            </vs-td>
            <vs-td :data="data[indextr].ubicacion_texto">{{data[indextr].ubicacion_texto}}</vs-td>
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
              <div class="flex justify-center">
                <img
                  class="mr-3"
                  style="width:20px;"
                  @click="ConsultarVenta(data[indextr].id)"
                  src="@assets/images/pdf.svg"
                  alt
                />

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
                  @click="deleteUsuario(data[indextr].id_user,data[indextr].cliente_nombre)"
                ></vs-button>
                <vs-button
                  v-else
                  title="Activar"
                  icon-pack="feather"
                  size="large"
                  icon="icon-shield"
                  color="success"
                  type="flat"
                  @click="habilitarUsuario(data[indextr].id_user,data[indextr].cliente_nombre)"
                ></vs-button>
              </div>
            </vs-td>
            <template class="expand-user" slot="expand"></template>
          </vs-tr>
        </template>
      </vs-table>

      <div>
        <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-8"></vs-pagination>
      </div>

      <pre ref="pre"></pre>
    </div>
    <div class="tab-content mt-1" v-show="activeTab==1">
      <div class="flex flex-wrap">
        <div class="w-full sm:w-12/12 ml-auto md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2">grafico</div>
      </div>
    </div>

    <Password
      :show="openStatus"
      :callback-on-success="callback"
      @closeVerificar="closeStatus"
      :accion="accionNombre"
    ></Password>

    <ReportesVentas
      :show="openReportes"
      @closeListaReportes="closeListaReportes"
      :id_venta="id_venta"
    ></ReportesVentas>
  </div>
</template>

<script>
//planes de venta

import cementerio from "@services/cementerio";

import ReportesVentas from "../ventas/ReportesVentas";

//componente de password
import Password from "@pages/confirmar_password";

import usuarios from "@services/Usuarios";
/**VARIABLES GLOBALES */
import { mostrarOptions } from "@/VariablesGlobales";
import vSelect from "vue-select";

export default {
  components: {
    "v-select": vSelect,
    Password,
    ReportesVentas
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
          value: ""
        },
        {
          label: "Activas",
          value: "1"
        },
        {
          label: "Canceladas",
          value: "0"
        }
      ],
      filtroEspecifico: { label: "Núm. Solicitud", value: "1" },
      filtrosEspecificos: [
        {
          label: "Núm. Solicitud",
          value: "1"
        },
        {
          label: "Núm. Convenio",
          value: "2"
        },
        {
          label: "Núm. Título",
          value: "3"
        },
        {
          label: "Núm. Venta",
          value: "4"
        }
      ],
      serverOptions: {
        page: "",
        per_page: "",
        status: "",
        filtro_especifico_opcion: "",
        numero_control: "",
        titular: ""
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
      datosModifcar: {},
      verAgregar: false,
      verModificar: false,
      id_venta_modificar: 0,
      selected: [],

      /**opciones para filtrar la peticion del server */
      id_venta: 0 /**para consultar los reportesw */
    };
  },
  methods: {
    reset(card) {
      card.removeRefreshAnimation(500);
      this.filtroEspecifico = { label: "Núm. Solicitud", value: "1" };
      this.serverOptions.numero_control = "";
      this.mostrar = { label: "15", value: "15" };
      this.estado = { label: "Todas", value: "" };
      this.serverOptions.titular = "";
      //this.get_data(this.actual);
    },
    get_data(page, evento = "") {
      if (evento == "blur") {
        if (
          this.serverOptions.titular != "" ||
          this.serverOptions.titular == ""
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
      if (cementerio.cancel) {
        cementerio.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = this.mostrar.value;
      this.serverOptions.status = this.estado.value;
      this.serverOptions.filtro_especifico_opcion = this.filtroEspecifico.value;
      cementerio
        .get_ventas(this.serverOptions)
        .then(res => {
          //console.log("get_data -> res", res);
          this.ventas = res.data.data;
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
    habilitarUsuario(id_user, nombre) {
      this.accionNombre = "habilitar usuario " + nombre;
      this.user_id = id_user;
      this.openStatus = true;
      this.callback = this.habilitar_usuario;
    },
    habilitar_usuario() {
      this.$vs.loading();
      let datos = {
        user_id: this.user_id
      };
      usuarios
        .habilitar_usuario(datos)
        .then(res => {
          this.get_data(this.actual);
          this.$vs.loading.close();
          if (res.data == 1) {
            this.$vs.notify({
              title: "Habilitar Usuario",
              text: "Se ha habilitado el usuario exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 4000
            });
          } else {
            this.$vs.notify({
              title: "Habilitar Usuario",
              text: "No se realizaron cambios.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "primary",
              time: 4000
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
                time: 4000
              });
            } else if (err.response.status == 422) {
              /**error de validacion */
              this.errores = err.response.data.error;
            }
          }
        });
    },
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
    openModificar(id_venta) {
      this.tipoFormulario = "modificar";
      this.id_venta_modificar = id_venta;
      this.verFormularioVentas = true;
    },
    formulario(tipo) {
      this.tipoFormulario = tipo;
      this.verFormularioVentas = true;
    },

    closeListaReportes() {
      this.openReportes = false;
      this.id_venta = 0;
      this.get_data(this.actual);
    }
  },
  created() {
    this.get_data(this.actual);
  }
};
</script>
