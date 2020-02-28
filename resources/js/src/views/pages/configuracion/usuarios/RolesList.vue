<template>
  <div>
    <vx-card ref="filterCard" title="Control de Roles y Permisos" class="user-list-filters">
      <div class="flex flex-wrap">
        <div class="w-full">
          <vs-button
            color="primary"
            size="small"
            class="ml-2 float-right"
            @click="update_rol"
          >Actualizar</vs-button>
          <vs-button @click="delete_rol()" color="danger" size="small" class="float-right">Eliminar</vs-button>
        </div>
      </div>
      <div class="flex flex-wrap">
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-4/12 xl:w-6/12 mb-4 px-2">
          <label class="text-sm opacity-75">Roles</label>
          <v-select
            :options="rolesOptions"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="roles"
            class="mb-4 md:mb-0"
          />
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.id">{{errores.id[0]}}</span>
          </div>
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-2/12 xl:w-6/12 mb-6 px-2">
          <label class="text-sm opacity-75">Nombre del Rol</label>
          <vs-input
            class="w-full"
            icon="search"
            placeholder="Nombre del Rol"
            v-model="form.rol_modificar"
            v-on:keyup.enter="update_rol()"
          />
          <div class="mt-2">
            <span
              class="text-danger text-sm"
              v-if="this.errores.rol_modificar"
            >{{errores.rol_modificar[0]}}</span>
          </div>
        </div>
      </div>
      <vs-divider position="left-center">Crear nuevos roles</vs-divider>
      <div class="flex flex-wrap">
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-11/12 mb-4 px-2">
          <label class="text-sm opacity-75">Nombre del Nuevo Rol</label>
          <vs-input
            data-vv-validate-on="change"
            v-validate="'required'"
            name="Nuevo Rol"
            class="w-full"
            icon="search"
            placeholder="Nombre del Nuevo Rol"
            v-model.trim="form.rol"
            v-on:keyup.enter="add_rol()"
          />
          <div class="mt-2">
            <span class="text-danger text-sm">{{ errors.first('Nuevo Rol') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.rol">{{errores.rol[0]}}</span>
          </div>
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-1/12 mb-4 px-2">
          <vs-button
            color="success"
            size="small"
            class="mt-8"
            @click="add_rol()"
            disable="true"
          >Guardar</vs-button>
        </div>
      </div>
    </vx-card>
    <br />
    <div class="mt-2">
      <span class="text-danger text-sm" v-if="this.errores.roles_set">{{errores.roles_set[0]}}</span>
    </div>
    <vs-table stripe noDataText="0 Resultados" :data="modulos">
      <template slot="header">
        <h3 class="pb-5 text-primary">Listado de Usuarios</h3>
      </template>
      <template slot="thead">
        <vs-th>Sección</vs-th>
        <vs-th>Módulo</vs-th>
        <vs-th>Agregar</vs-th>
        <vs-th>Editar</vs-th>
        <vs-th>Eliminar</vs-th>
        <vs-th>Consultar</vs-th>
      </template>
      <template slot-scope="{data}">
        <vs-tr :key="indextr" v-for="(tr, indextr) in modulos">
          <vs-td :data="modulos[indextr].seccion">{{modulos[indextr].seccion}}</vs-td>
          <vs-td :data="modulos[indextr].modulo">{{modulos[indextr].modulo}}</vs-td>

          <vs-td v-for="items in 4" :key="items">
            <vs-checkbox v-model="form.roles_set" :vs-value="modulos[indextr].mod_id+'_'+items"></vs-checkbox>
          </vs-td>
        </vs-tr>
      </template>
    </vs-table>
    <Password
      :show="verConfirmar"
      :callback-on-success="callback"
      @closeVerificar="closeChecker"
      :accion="accionNombre"
    ></Password>
  </div>
</template>

<script>
import usuarios from "../../../../services/Usuarios";
//componente de password
import Password from "../../confirmar_password";
/**VARIABLES GLOBALES */
import { rolesOptions } from "../../../../VariablesGlobales";
import vSelect from "vue-select";

import {
  UserPlusIcon,
  PrinterIcon,
  DeleteIcon,
  PlusCircleIcon
} from "vue-feather-icons";
export default {
  watch: {
    roles: function(newValue, oldValue) {
      this.form.id = this.roles.value;
      this.getPermisosRol();
    }
  },
  components: {
    "v-select": vSelect,
    UserPlusIcon,
    PrinterIcon,
    DeleteIcon,
    PlusCircleIcon,
    Password
  },
  computed: {
    validateForm() {
      return !this.errors.any() && this.form.rol != "";
    }
  },
  data() {
    return {
      accionNombre: "",
      callback: Function,
      verConfirmar: false,
      rolesOptions: [],
      roles: { label: "Seleccione 1", value: "" },
      form: {
        rol: "",
        rol_modificar: "",
        id: "",
        roles_set: []
      },
      errores: [],
      modulos: []
    };
  },
  methods: {
    get_roles() {
      usuarios
        .getRoles()
        .then(res => {
          //le agrego todos los roles
          this.rolesOptions = [];
          this.rolesOptions.push({ label: "Seleccione 1", value: "" });
          res.data.data.forEach(element => {
            /**AGREGO LOS DEMAS ROLES */
            this.rolesOptions.push(element);
          });
        })
        .catch(err => {});
    },
    get_modulos() {
      usuarios
        .getModulos()
        .then(res => {
          this.modulos = res.data.data;
        })
        .catch(err => {});
    },
    //eliminar rol
    delete_rol() {
      if (this.form.id != "") {
        this.accionNombre = "eliminar el rol";
        this.callback = this.deleteRol;
        this.verConfirmar = true;
      } else {
        this.$vs.notify({
          title: "Validación de datos",
          text: "Debe seleccionar un rol.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "warning",
          position: "bottom-center",
          time: "4000"
        });
        this.errores = [];
      }
    },
    //funcion que valida formularios y password
    add_rol() {
      if (this.form.rol != "") {
        this.accionNombre = "agregar un nuevo rol";
        this.callback = this.saveRol;
        this.verConfirmar = true;
      } else {
        this.$vs.notify({
          title: "Validación de datos",
          text: "Debe ingresar el nombre del nuevo rol.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "warning",
          position: "bottom-center",
          time: "4000"
        });
        this.errores = [];
      }
    },
    update_rol() {
      this.accionNombre = "actualizar el rol";
      this.errores = [];
      this.callback = this.updateRol;
      this.verConfirmar = true;
    },
    //funcion que modifica el rol
    updateRol() {
      this.$vs.loading();
      //limpiando errores
      this.errores = [];
      usuarios
        .update_rol(this.form)
        .then(res => {
          this.$vs.loading.close();
          if (this.form.rol_modificar.trim() != "") {
            this.get_roles();
            this.roles = {
              label: this.form.rol_modificar,
              value: this.form.id
            };
          }
          this.form.rol_modificar = "";
          this.$vs.notify({
            title: "Modificar Roles",
            text: "Se ha modificado el rol exitosamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 4000
          });
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
    //funcion que inserta el nuevo rol
    saveRol() {
      this.$vs.loading();
      //limpiando errores
      this.errores = [];
      usuarios
        .add_rol(this.form)
        .then(res => {
          this.$vs.loading.close();
          this.form.rol = "";
          this.form.roles_set = [];
          this.get_roles();
          this.$emit("refreshRoles");
          this.$vs.notify({
            title: "Agregar Roles",
            text: "Se ha creado el nuevo rol exitosamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 4000
          });
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
    //funcion para elimnar un rol
    deleteRol() {
      this.$vs.loading();
      //limpiando errores
      this.errores = [];
      usuarios
        .delete_rol(this.form)
        .then(res => {
          this.$vs.loading.close();
          this.form.roles_set = [];
          this.get_roles();
          this.$emit("refreshRoles");
          (this.roles = { label: "Seleccione 1", value: "" }),
            this.$vs.notify({
              title: "Eliminar Roles",
              text: "Se ha eliminado el rol exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 4000
            });
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
            } else if (err.response.status == 409) {
              this.$vs.notify({
                title: "Eliminar Roles",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 4000
              });
            }
          }
        });
    },
    getPermisosRol() {
      this.form.roles_set = [];
      if (this.roles.value != "") {
        let self = this;
        if (usuarios.cancel) {
          usuarios.cancel("Operation canceled by the user.");
        }
        usuarios
          .getPermisosRol(this.roles.value)
          .then(res => {
            this.form.roles_set = res.data;
          })
          .catch(err => {});
      }
    },
    closeChecker() {
      this.verConfirmar = false;
    }
  },
  created() {
    this.get_roles();
    this.get_modulos();
  }
};
</script>

<style lang="scss">
.vs-con-table .vs-con-tbody .con-vs-checkbox {
  -webkit-box-pack: left !important;
  justify-content: left !important;
}
</style>