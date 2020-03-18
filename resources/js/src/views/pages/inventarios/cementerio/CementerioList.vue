<template>
  <div>
    <vs-tabs alignment="left" position="top" v-model="activeTab">
      <vs-tab label="INVENTARIO DEL CEMENTERIO" icon="supervisor_account" class="pb-5"></vs-tab>
      <!--<vs-tab label="ROLES DEL SISTEMA" icon="fingerprint"></vs-tab>-->
    </vs-tabs>
    <div class="tab-content mt-1" v-show="activeTab==0">
      <vx-card ref="filterCard" title="Filtros de selección" class="user-list-filters">
        <div class="flex flex-wrap">
          <div class="w-full">
            <vs-button
              color="success"
              size="small"
              class="float-right"
              @click="verAgregar=true"
            >Agregar</vs-button>
            <vs-button color="primary" size="small" class="float-right mr-3" @click="pdf()">Pdf</vs-button>
          </div>
        </div>
        <div class="flex flex-wrap">
          <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-4/12 mb-4 px-2">
            <label class="text-sm opacity-75">Mostrar</label>
            <v-select
              :options="mostrarOptions"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="mostrar"
              class="mb-4 sm:mb-0"
            />
          </div>
          <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-4/12 mb-4 px-2">
            <label class="text-sm opacity-75">Estado</label>
            <v-select
              :options="estadosOptions"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="estado"
              class="mb-4 md:mb-0"
            />
          </div>
          <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-4/12 mb-4 px-2">
            <label class="text-sm opacity-75">Roles</label>
            <v-select
              :options="rolesOptions"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="roles"
              class="mb-4 md:mb-0"
            />
          </div>
          <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-11/12 mb-4 px-2">
            <label class="text-sm opacity-75">Nombre</label>
            <vs-input
              class="w-full"
              icon="search"
              placeholder="Filtrar por nombre"
              v-model="nombre"
              v-on:keyup.enter="get_data(1)"
              v-on:blur="get_data(1,'blur')"
            />
          </div>
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-1/12 mb-4">
            <vs-button
              type="border"
              size="small"
              @click="reset"
              color="primary"
              line-position="top"
              class="mt-8"
            >Resetear</vs-button>
          </div>
        </div>
      </vx-card>
      <br />
      <vs-table
        :sst="true"
        @search="handleSearch"
        @change-page="handleChangePage"
        @sort="handleSort"
        v-model="selected"
        :max-items="serverOptions.per_page.value"
        :data="propiedades"
        stripe
        noDataText="0 Resultados"
      >
        <template slot="header">
          <h3 class="pb-5 text-primary">Listado de Usuarios</h3>
        </template>
        <template slot="thead">
          <vs-th>Tamaño</vs-th>
          <vs-th>Descripcion</vs-th>
          <vs-th>Usuario</vs-th>
          <vs-th>Género</vs-th>
          <vs-th>Estado</vs-th>
          <vs-th>Rol</vs-th>
          <vs-th>Acciones</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].tipo_propiedad">{{data[indextr].tipo_propiedad.tipo}}</vs-td>
            <vs-td :data="data[indextr].propiedad_indicador">{{data[indextr].propiedad_indicador}}</vs-td>
            <vs-td :data="data[indextr].email">{{data[indextr].email}}</vs-td>
            <vs-td :data="data[indextr].genero">
              <p v-if="data[indextr].genero==1">Hombre</p>
              <p v-else>Mujer</p>
            </vs-td>
            <vs-td :data="data[indextr].estado">
              <p v-if="data[indextr].estado==1">Activo</p>
              <p v-else>Sin acceso</p>
            </vs-td>
            <vs-td :data="data[indextr].rol">{{data[indextr].rol}}</vs-td>
            <vs-td :data="data[indextr].id_user">
              <div class="flex flex-start">
                <vs-button
                  title="Activar"
                  icon-pack="feather"
                  size="large"
                  icon="icon-shield"
                  color="success"
                  type="flat"
                  @click="datosPropiedad(data[indextr].id)"
                ></vs-button>
              </div>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </div>

    <Password
      :show="openStatus"
      :callback-on-success="callback"
      @closeVerificar="closeStatus"
      :accion="accionNombre"
    ></Password>

    <verPropiedades :show="abrirVisor" :propiedad_id="propiedad_id" @closeVisor="abrirVisor=false"></verPropiedades>
  </div>
</template>

<script>
import verPropiedades from "../cementerio/listaModulos";
//get cementerio services
import cementerio from "@services/cementerio";

//componente de password
import Password from "../../confirmar_password";
/**VARIABLES GLOBALES */
import {
  mostrarOptions,
  estadosOptions,
  rolesOptions
} from "@/VariablesGlobales";
import vSelect from "vue-select";

export default {
  components: {
    "v-select": vSelect,
    Password,
    verPropiedades
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
    },
    roles: function(newValue, oldValue) {
      this.get_data(1);
    }
  },
  data() {
    return {
      abrirVisor: false,
      pdfLink: "",
      openStatus: false,
      callback: Function,
      accionNombre: "",
      activeTab: 0,
      ver: true,
      total: 0,
      actual: 1,
      mostrarOptions: mostrarOptions,
      estadosOptions: estadosOptions,
      rolesOptions: [],
      mostrar: { label: "15", value: "15" },
      estado: { label: "Todos", value: "" },
      roles: { label: "Todos", value: "" },
      nombre: "",
      selected: [],
      propiedades: [],
      propiedad_id: 0,
      /**opciones para filtrar la peticion del server */
      serverOptions: {
        page: "",
        per_page: "",
        status: "",
        rol_id: "",
        nombre: ""
      },
      /**user id para bajas y altas */
      user_id: ""
    };
  },
  methods: {
    reset() {
      this.mostrar = { label: "15", value: "15" };
      this.estado = { label: "Todos", value: "" };
      this.roles = { label: "Todos", value: "" };
      this.nombre = "";
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
    closeVentana() {
      this.verAgregar = false;
    },
    closeStatus() {
      this.openStatus = false;
    },
    datosPropiedad(datos) {
      this.propiedad_id = datos;
      this.abrirVisor = true;
    }
  },
  created() {
    cementerio
      .getDistribucion()
      .then(res => {
        this.propiedades = res.data;
        this.$vs.loading.close();
      })
      .catch(err => {
        this.$vs.loading.close();
        if (err.response) {
          if (err.response.status == 403) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
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
  }
};
</script>
