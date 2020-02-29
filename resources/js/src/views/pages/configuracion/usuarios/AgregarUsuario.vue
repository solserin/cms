<template >
  <div class="centerx">
    <vs-popup
      class="usuarios"
      close="cancelar"
      title="Agregar Nuevos Usuarios"
      :active.sync="showVentana"
      button-close-hidden
    >
      <div class="w-full px-2">
        <vs-button @click="acceptAlert()" color="success" size="small" class="float-right">Guardar</vs-button>
        <vs-button @click="cancel()" color="danger" size="small" class="float-right mr-5">Cancelar</vs-button>
      </div>
      <span class="text-sm texto-importante">IMPORTANTE Los campos con (*) son obligatorios.</span>
      <h3 class="texto-informacion">Información del usuario</h3>
      <div class="flex flex-wrap">
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75">
            Nombre
            <span class="text-danger text-sm">(*)</span>
          </label>
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
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
          <label class="text-sm opacity-75">
            Rol
            <span class="text-danger text-sm">(*)</span>
          </label>
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
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
          <label class="text-sm opacity-75">
            Género
            <span class="text-danger text-sm">(*)</span>
          </label>
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
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75">
            Usuario (email)
            <span class="text-danger text-sm">(*)</span>
          </label>
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
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
          <label class="text-sm opacity-75">
            Password
            <span class="text-danger text-sm">(*)</span>
          </label>
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

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
          <label class="text-sm opacity-75">
            Repetir Password
            <span class="text-danger text-sm">(*)</span>
          </label>
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
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75">Dirección</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.direccion"
          />
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 mb-3 px-2">
          <label class="text-sm opacity-75">Teléfono</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.telefono"
          />
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 mb-2 px-2">
          <label class="text-sm opacity-75">Celular</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.celular"
          />
        </div>
      </div>

      <div class="flex flex-wrap">
        <h3 class="texto-informacion">Información del contacto</h3>
        <div class="w-full mb-2 px-2">
          <label class="text-sm opacity-75">Nombre de un contacto</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.nombre_contacto"
          />
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 mb-2 px-2">
          <label class="text-sm opacity-75">Teléfono del contacto</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.tel_contacto"
          />
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 mb-2 px-2">
          <label class="text-sm opacity-75">Parentesco</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.parentesco_contacto"
          />
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
import usuarios from "@services/Usuarios";
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
      activeTab: 0,
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
        genero: "",
        direccion: "",
        telefono: "",
        celular: "",
        nombre_contacto: "",
        tel_contacto: "",
        parentesco_contacto: ""
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
      this.form.direccion = "";
      this.form.telefono = "";
      this.form.celular = "";
      this.form.nombre_contacto = "";
      this.form.tel_contacto = "";
      this.form.parentesco_contacto = "";
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
          this.$emit("get_data");
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