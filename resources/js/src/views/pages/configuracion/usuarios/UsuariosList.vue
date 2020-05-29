<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full sm:w-12/12 ml-auto md:w-1/5 lg:w-1/5 xl:w-1/5 mb-1 px-2">
        <vs-button
          color="success"
          size="small"
          class="w-full ml-auto"
          @click="verFormulario('agregar')"
        >
          <img class="cursor-pointer img-btn" src="@assets/images/plus.svg" />
          <span class="texto-btn">Registrar Usuario</span>
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
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4 px-2">
            <div class="w-full px-2">
              <h3 class="text-base font-semibold my-3">
                <feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />Filtrar por Nombre del Titular
              </h3>
            </div>
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
      :data="users"
      stripe
      noDataText="0 Resultados"
    >
      <template slot="header">
        <h3 class="pb-5 text-primary">Listado de Usuarios registrados</h3>
      </template>
      <template slot="thead">
        <vs-th>Clave</vs-th>
        <vs-th>Nombre</vs-th>
        <vs-th>Usuario</vs-th>
        <vs-th>Género</vs-th>
        <vs-th>Estado</vs-th>
        <vs-th>Rol</vs-th>
        <vs-th>Acciones</vs-th>
      </template>
      <template slot-scope="{data}">
        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
          <vs-td :data="data[indextr].id_user">{{data[indextr].id_user}}</vs-td>
          <vs-td :data="data[indextr].nombre">{{data[indextr].nombre}}</vs-td>
          <vs-td :data="data[indextr].email">{{data[indextr].email}}</vs-td>
          <vs-td :data="data[indextr].genero">
            <p v-if="data[indextr].genero==1">Hombre</p>
            <p v-else>Mujer</p>
          </vs-td>
          <vs-td :data="data[indextr].estado">
            <p v-if="data[indextr].estado==1" class="text-success font-medium">Activo</p>
            <p v-else class="text-danger font-medium">Sin Acceso</p>
          </vs-td>
          <vs-td :data="data[indextr].rol">{{data[indextr].rol}}</vs-td>
          <vs-td :data="data[indextr].id_user">
            <div class="flex flex-start">
              <vs-button
                class="ml-auto"
                title="Editar"
                size="large"
                icon-pack="feather"
                icon="icon-edit"
                color="dark"
                type="flat"
                @click="openModificar(data[indextr].id_user)"
              ></vs-button>
              <vs-button
                class="mr-auto"
                v-if="data[indextr].estado==1"
                title="Desactivar"
                icon-pack="feather"
                size="large"
                icon="icon-shield-off"
                color="danger"
                type="flat"
                @click="deleteUsuario(data[indextr].id_user,data[indextr].nombre)"
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
                @click="habilitarUsuario(data[indextr].id_user,data[indextr].nombre)"
              ></vs-button>
            </div>
          </vs-td>
        </vs-tr>
      </template>
    </vs-table>
    <div>
      <vs-pagination v-if="ver" :total="this.total" v-model="actual" class="mt-8"></vs-pagination>
    </div>

    <formularioUsuarios
      :id_usuario="id_usuario_modificar"
      :tipo="tipoFormulario"
      :show="verFormularioUsuarios"
      @closeVentana="closeVentana"
      @get_data="get_data(actual)"
    ></formularioUsuarios>

    <Password
      :show="openStatus"
      :callback-on-success="callback"
      @closeVerificar="closeStatus"
      :accion="accionNombre"
    ></Password>
  </div>
</template>

<script>
import formularioUsuarios from "@pages/configuracion/usuarios/formularioUsuarios";

//componente de password
import Password from "@pages/confirmar_password";

import usuarios from "@services/Usuarios";
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
    formularioUsuarios
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
      tipoFormulario: "",
      id_usuario_modificar: 0,
      verPdf: false,
      pdfLink: "",
      openStatus: false,
      callback: Function,
      accionNombre: "",
      verModificar: false,
      datosModifcar: {},
      verFormularioUsuarios: false,
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
      users: [],
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
    reset(card) {
      card.removeRefreshAnimation(500);
      this.mostrar = { label: "15", value: "15" };
      this.estado = { label: "Todos", value: "" };
      this.roles = { label: "Todos", value: "" };
      this.nombre = "";
      this.get_data(this.actual);
    },

    get_data(page, evento = "") {
      if (evento == "blur") {
        if (this.nombre != "") {
          //la funcion no avanza

          return false;
        }
      }
      let self = this;
      if (usuarios.cancel) {
        usuarios.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.ver = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = this.mostrar.value;
      this.serverOptions.rol_id = this.roles.value;
      this.serverOptions.status = this.estado.value;
      this.serverOptions.nombre = this.nombre;
      usuarios
        .getUsuarios(this.serverOptions)
        .then(res => {
          this.users = res.data.data;
          this.total = res.data.last_page;
          this.actual = res.data.current_page;
          this.ver = true;
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
    get_roles() {
      usuarios
        .getRoles()
        .then(res => {
          this.rolesOptions = [];
          //le agrego todos los roles
          this.rolesOptions.push({ label: "Todos", value: "" });
          res.data.data.forEach(element => {
            /**AGREGO LOS DEMAS ROLES */
            this.rolesOptions.push(element);
          });
          this.roles = { label: "Todos", value: "" };
        })
        .catch(err => {});
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
    closeVentana() {
      this.verFormularioUsuarios = false;
    },
    openModificar(id_user) {
      this.id_usuario_modificar = id_user;
      this.verFormulario("modificar");
    },
    //eliminar usuario logicamente
    deleteUsuario(id_user, nombre) {
      this.accionNombre = "deshabilitar usuario " + nombre;
      this.user_id = id_user;
      this.openStatus = true;
      this.callback = this.delete_usuario;
    },
    delete_usuario() {
      this.$vs.loading();
      let datos = {
        user_id: this.user_id
      };
      usuarios
        .delete_usuario(datos)
        .then(res => {
          this.$vs.loading.close();
          this.get_data(this.actual);
          if (res.data == 1) {
            this.$vs.notify({
              title: "Deshabilitar Usuario",
              text: "Se ha deshabilitado el usuario exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 4000
            });
          } else {
            this.$vs.notify({
              title: "Deshabilitar Usuario",
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

    verFormulario(tipo) {
      this.tipoFormulario = tipo;
      this.verFormularioUsuarios = true;
    }
  },
  created() {
    this.get_roles();
    this.get_data(this.actual);
  }
};
</script>
