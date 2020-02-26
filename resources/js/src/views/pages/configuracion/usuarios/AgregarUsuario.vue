<template >
  <div class="centerx">
    <vs-popup
      close="cancelar"
      title="Agregar Nuevos Usuario"
      :active.sync="showVentana"
      button-close-hidden
    >
      <div class="flex flex-wrap">
        <div class="w-full">
          <label class="text-sm opacity-75">Rol</label>
          <v-select
            :options="rolesOptions"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="roles"
            class="mb-4 sm:mb-0 pb-1 pt-1"
          />
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.rol_id">{{errores.rol_id[0]}}</span>
          </div>
        </div>
        <div class="w-full">
          <label class="text-sm opacity-75">Género</label>
          <v-select
            :options="generosOptions"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="genero"
            class="mb-4 sm:mb-0 pb-1 pt-1"
          />
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.genero">{{errores.genero[0]}}</span>
          </div>
        </div>

        <div class="w-full">
          <label class="text-sm opacity-75">Nombre</label>
          <vs-input
            name="Nombre"
            data-vv-validate-on="blur"
            v-validate="'required'"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.nombre"
          />
          <div>
            <span class="text-danger text-sm">{{ errors.first('Nombre') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.nombre">{{errores.nombre[0]}}</span>
          </div>
        </div>

        <div class="w-full">
          <label class="text-sm opacity-75">Usuario (email)</label>
          <vs-input
            name="Usuario (email)"
            data-vv-validate-on="blur"
            v-validate="'required|email'"
            type="email"
            class="w-full pb-1 pt-1"
            placeholder="Correo electrónico del usuario"
            v-model="form.usuario"
          />
          <div>
            <span class="text-danger text-sm">{{ errors.first('Usuario (email)') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.usuario">{{errores.usuario[0]}}</span>
          </div>
        </div>

        <div class="w-full">
          <label class="text-sm opacity-75">Password</label>
          <vs-input
            data-vv-validate-on="blur"
            v-validate="'required'"
            name="Password"
            type="password"
            class="w-full pb-1 pt-1"
            placeholder="Contraseña del usuario"
            v-model="form.password"
          />
          <div>
            <span class="text-danger text-sm">{{ errors.first('Password') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.password">{{errores.password[0]}}</span>
          </div>
        </div>

        <div class="w-full">
          <label class="text-sm opacity-75">Repetir Password</label>
          <vs-input
            data-vv-validate-on="blur"
            v-validate="'required'"
            name="Repetir Password"
            type="password"
            class="w-full pb-1 pt-1"
            placeholder="Repita la contraseña"
            v-model="form.repetir"
          />
          <div>
            <span class="text-danger text-sm">{{ errors.first('Repetir Password') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.repetir">{{errores.repetir[0]}}</span>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap">
        <div class="w-full pt-5">
          <vs-button @click="cancel()" color="danger" size="small" class="float-right">Cancelar</vs-button>
          <vs-button
            @click="acceptAlert()"
            color="success"
            size="small"
            class="float-right mr-5"
          >Guardar</vs-button>
        </div>
      </div>
    </vs-popup>
    <Password
      :show="operConfirmar"
      :callback-on-success="callback"
      @closeVerificar="closeChecker"
      :accion="accionNombre"
    ></Password>
  </div>
</template>
<script>
//componente de password
import Password from "../../confirmar_password";
import usuarios from "../../../../services/Usuarios";
import vSelect from "vue-select";
/**VARIABLES GLOBALES */
import { generosOptions } from "../../../../VariablesGlobales";
export default {
  components: {
    "v-select": vSelect,
    Password
  },
  props: {
    show: {
      type: Boolean,
      required: true
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.get_roles();
      }
    }
  },
  data() {
    return {
      generosOptions: generosOptions,
      operConfirmar: false,
      callback: Function,
      accionNombre: "agregar nuevo usuario",
      roles: { label: "Seleccione 1", value: "" },
      rolesOptions: [],
      genero: {
        label: "Seleccione 1",
        value: ""
      },
      form: {
        rol_id: "",
        nombre: "",
        usuario: "",
        password: "",
        repetir: "",
        genero: ""
      },
      errores: []
    };
  },
  computed: {
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  methods: {
    acceptAlert() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            return;
          } else {
            //se confirma la cntraseña
            this.callback = this.saveUsuario;
            this.operConfirmar = true;
          }
        })
        .catch(() => {});
    },
    cancel() {
      this.roles = { label: "Seleccione 1", value: "" };
      this.genero = { label: "Seleccione 1", value: "" };
      this.form.rol_id = "";
      this.form.nombre = "";
      this.form.usuario = "";
      this.form.password = "";
      this.form.repetir = "";
      this.$emit("closeVentana");
    },
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
    //funcion que inserta el nuevo rol
    saveUsuario() {
      this.$vs.loading();
      //limpiando errores
      this.errores = [];
      this.form.rol_id = this.roles.value;
      this.form.genero = this.genero.value;
      usuarios
        .add_usuario(this.form)
        .then(res => {
          this.$vs.loading.close();
          this.cancel();
          this.$vs.notify({
            title: "Agregar Usuarios",
            text: "Se ha creado el nuevo usuario exitosamente.",
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
    closeChecker() {
      this.operConfirmar = false;
    }
  }
};
</script>
<style lang="scss">
.vs-popup--header {
  padding: 10px 0 10px 0;
  background-color: #063278;
  border-radius: 0;
}
.vs-popup--title h3 {
  color: #fff;
  text-transform: uppercase;
  font-size: 14px !important;
  font-weight: 600;
}
.con-vs-popup .vs-popup {
  border-radius: 0px;
}

.vs-button {
  border-radius: 0;
}

.con-vs-popup {
  z-index: 52000 !important;
}
</style>