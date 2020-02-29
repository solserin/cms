<template>
  <div>
    <vs-tabs alignment="left" position="top" v-model="activeTab">
      <vs-tab label="CONTROL DE USUARIOS" icon="supervisor_account" class="pb-5"></vs-tab>
      <vs-tab label="ROLES DEL SISTEMA" icon="fingerprint"></vs-tab>
    </vs-tabs>
    <div class="tab-content mt-1" v-show="activeTab==0">
      <vx-card ref="filterCard" title="Filtros de selección" class="user-list-filters">
        <div class="flex flex-wrap">
          <div class="w-full">
            <vs-button color="success" size="small" class="float-right" @click="verAgregar=true">
              <user-plus-icon size="1x" class="custom-class mr-2"></user-plus-icon>Agregar
            </vs-button>
            <vs-button color="primary" size="small" class="float-right mr-3" @click="pdf()">
              <printer-icon size="1x" class="custom-class mr-2"></printer-icon>Pdf
            </vs-button>
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
        :data="users"
        stripe
        noDataText="0 Resultados"
      >
        <template slot="header">
          <h3 class="pb-5 text-primary">Listado de Usuarios</h3>
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
              <p v-if="data[indextr].estado==1">Activo</p>
              <p v-else>Sin acceso</p>
            </vs-td>
            <vs-td :data="data[indextr].rol">{{data[indextr].rol}}</vs-td>
            <vs-td :data="data[indextr].id_user">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24px"
                height="24px"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="feather feather-edit-3 h-5 w-5 mr-4 hover:text-primary cursor-pointer"
                @click="openModificar(data[indextr].id_user)"
              >
                <path d="M12 20h9" />
                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
              </svg>

              <svg
                v-if="data[indextr].estado==1"
                xmlns="http://www.w3.org/2000/svg"
                width="24px"
                height="24px"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="feather feather-user-check h-5 w-5 hover:text-danger cursor-pointer"
                @click="deleteUsuario(data[indextr].id_user,data[indextr].nombre)"
              >
                <polyline points="3 6 5 6 21 6" />
                <path
                  d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                />
                <line x1="10" y1="11" x2="10" y2="17" />
                <line x1="14" y1="11" x2="14" y2="17" />
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                width="24px"
                height="24px"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="feather feather-user-check h-5 w-5 hover:text-success cursor-pointer"
                @click="habilitarUsuario(data[indextr].id_user,data[indextr].nombre)"
              >
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="8.5" cy="7" r="4" />
                <path d="M17 11l2 2l4-4" />
              </svg>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
      <div>
        <vs-pagination v-if="ver" :total="this.total" v-model="actual" class="mt-8"></vs-pagination>
      </div>
      <pre ref="pre"></pre>
    </div>
    <div class="tab-content mt-4 pb-3" v-show="activeTab==1">
      <rolesList @refreshRoles="get_roles"></rolesList>
    </div>
    <AgregarUsuario :show="verAgregar" @closeVentana="closeVentana" @get_data="get_data(actual)"></AgregarUsuario>
    <UpdateUsuario
      :show="verModificar"
      @closeModificar="closeModificar"
      :datos="datosModifcar"
      @get_data="get_data(actual)"
    ></UpdateUsuario>
    <Password
      :show="openStatus"
      :callback-on-success="callback"
      @closeVerificar="closeStatus"
      :accion="accionNombre"
    ></Password>
    <pdf :show="verPdf" :pdf="pdfLink" @closePdf="verPdf=false"></pdf>
  </div>
</template>

<script>
import pdf from "../../pdf_viewer";
/**IMPORTAR EL COMPONENTE DE ROLES */
import rolesList from "../usuarios/RolesList";
import AgregarUsuario from "../usuarios/AgregarUsuario";
import UpdateUsuario from "../usuarios/UpdateUsuario.vue";

//componente de password
import Password from "../../confirmar_password";
import { UserPlusIcon, PrinterIcon } from "vue-feather-icons";

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
    UserPlusIcon,
    PrinterIcon,
    rolesList,
    Password,
    AgregarUsuario,
    UpdateUsuario,
    pdf
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
      verPdf: false,
      pdfLink: "",
      openStatus: false,
      callback: Function,
      accionNombre: "",
      verModificar: false,
      datosModifcar: {},
      verAgregar: false,
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
    reset() {
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
      this.verAgregar = false;
    },
    openModificar(id_user) {
      this.users.forEach(element => {
        if (element.id_user == id_user) {
          this.datosModifcar = element;
          this.verModificar = true;
          return false;
        }
      });
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

    pdf() {
      this.pdfLink =
        "pdfs?status=" +
        this.estado.value +
        "&rol_id=" +
        this.roles.value +
        "&nombre=" +
        this.nombre;
      this.verPdf = true;
    }
  },
  created() {
    this.get_roles();
    this.get_data(this.actual);
  }
};
</script>
