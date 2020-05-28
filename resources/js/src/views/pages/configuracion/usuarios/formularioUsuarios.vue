<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups usuarios big-forms"
      close="cancel"
      :title="title"
      :active.sync="showVentana"
      ref="usuarios"
    >
      <div class="w-full sm:w-12/12 md:w-10/12 lg:w-10/12 xl:w-10/12 px-2 pb-4">
        <h3 class="text-xl">
          <feather-icon icon="UsersIcon" class="mr-2" svgClasses="w-5 h-5" />
          <span>Información del Usuario</span>
        </h3>
      </div>
      <vs-divider />

      <div class="flex flex-wrap">
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            Nombre
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            ref="nombre"
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
          <label class="text-sm opacity-75 font-bold">
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
          <label class="text-sm opacity-75 font-bold">
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
          <label class="text-sm opacity-75 font-bold">
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
          <label class="text-sm opacity-75 font-bold">
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
          <label class="text-sm opacity-75 font-bold">
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
          <label class="text-sm opacity-75 font-bold">Dirección</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.direccion"
          />
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 mb-3 px-2">
          <label class="text-sm opacity-75 font-bold">Teléfono</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.telefono"
          />
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 mb-2 px-2">
          <label class="text-sm opacity-75 font-bold">Celular</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.celular"
          />
        </div>
      </div>

      <div class="flex flex-wrap">
        <div class="w-full sm:w-12/12 md:w-10/12 lg:w-10/12 xl:w-10/12 px-2 py-4">
          <h3 class="text-xl">
            <feather-icon icon="UsersIcon" class="mr-2" svgClasses="w-5 h-5" />
            <span>Seleccione las áreas de responsabilidad de este usuario</span>
          </h3>
        </div>
        <vs-divider />

        <div
          class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2 py-4"
          v-for="puesto in puestos"
          :key="puesto.id"
        >
          <vs-checkbox
            class="bg-gray-headers px-2 py-4"
            v-model="form.puestos"
            :vs-value="puesto.id"
          >
            <label class="text-sm opacity-75 font-bold dark-text">{{ puesto.puesto }}</label>
          </vs-checkbox>
        </div>
        <div class="w-full px-2 mt-2">
          <span class="text-danger text-sm" v-if="this.errores.puestos">{{errores.puestos[0]}}</span>
        </div>
      </div>

      <div class="flex flex-wrap">
        <div class="vx-row w-full mt-2 px-2">
          <div class="w-full sm:w-12/12 md:w-10/12 lg:w-10/12 xl:w-10/12 px-2 py-4">
            <h3 class="text-xl">
              <feather-icon icon="UsersIcon" class="mr-2" svgClasses="w-5 h-5" />
              <span>Información de un Contacto</span>
            </h3>
          </div>
        </div>
        <vs-divider />
        <div class="w-full mb-2 px-2">
          <label class="text-sm opacity-75 font-bold">Nombre de un contacto</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.nombre_contacto"
          />
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 mb-2 px-2">
          <label class="text-sm opacity-75 font-bold">Teléfono del contacto</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.tel_contacto"
          />
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 mb-2 px-2">
          <label class="text-sm opacity-75 font-bold">Parentesco</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.parentesco_contacto"
          />
        </div>
      </div>

      <div class="w-full sm:w-12/12 md:w-4/12 lg:w-3/12 xl:w-3/12 mt-8 pb-20 px-2 mr-auto ml-auto">
        <vs-button class="w-full" @click="acceptAlert()" color="primary">
          <img width="25px" class="cursor-pointer" size="small" src="@assets/images/save.svg" />
          <span class="texto-btn" v-if="this.getTipoformulario=='agregar'">Guardar Datos</span>
          <span class="texto-btn" v-else>Modificar Datos</span>
        </vs-button>
      </div>
    </vs-popup>
    <Password
      :show="operConfirmar"
      :callback-on-success="callback"
      @closeVerificar="closeChecker"
      :accion="accionNombre"
    ></Password>
    <ConfirmarDanger
      :show="openConfirmarDanger"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarDanger=false"
      :accion="accionConfirmarDanger"
      :confirmarButton="botonConfirmarDanger"
    ></ConfirmarDanger>
    <ConfirmarAceptar
      :show="openConfirmarAceptar"
      :callback-on-success="callback"
      @closeVerificar="openConfirmarAceptar=false"
      :accion="'He revisado la información y quiero registrar a este usuario'"
      :confirmarButton="'Guardar Usuario'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
//componente de password
import Password from "@pages/confirmar_password";
import usuarios from "@services/Usuarios";
import vSelect from "vue-select";
import ConfirmarDanger from "@pages/ConfirmarDanger";
/**VARIABLES GLOBALES */
import { generosOptions } from "@/VariablesGlobales";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
export default {
  components: {
    "v-select": vSelect,
    Password,
    ConfirmarDanger,
    ConfirmarAceptar
  },
  props: {
    show: {
      type: Boolean,
      required: true
    },
    tipo: {
      type: String,
      required: true
    },
    id_usuario: {
      type: Number,
      required: false,
      default: 0
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.$refs["usuarios"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancel();
        };
        this.$nextTick(() =>
          this.$refs["nombre"].$el.querySelector("input").focus()
        );
        this.get_roles();
        /**get puestos de trabajo */
        this.get_puestos();
        if (this.getTipoformulario == "agregar") {
          this.title = "Registrar Nuevo Usuario";
        } else {
          this.title = "Modificar Usuario";

          /**se cargan los datos del usuario */
          this.get_usuarioById(this.get_usuario_id);
        }
      }
    }
  },
  data() {
    return {
      openConfirmarAceptar: false,
      title: "",
      botonConfirmarDanger: "",
      openConfirmarDanger: false,
      callBackConfirmar: Function,
      accionConfirmarDanger: "",
      activeTab: 0,
      generosOptions: generosOptions,
      operConfirmar: false,
      callback: Function,
      accionNombre: "agregar nuevo usuario",
      roles: { label: "Seleccione 1", value: "" },
      rolesOptions: [],
      genero: generosOptions[0],
      puestos: [],
      form: {
        user_id: "",
        puestos: [],
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
    },
    getTipoformulario: {
      get() {
        return this.tipo;
      },
      set(newValue) {
        return newValue;
      }
    },
    get_usuario_id: {
      get() {
        return this.id_usuario;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  methods: {
    get_usuarioById(id_user) {
      this.$vs.loading();
      usuarios
        .get_usuarioById(id_user)
        .then(res => {
          this.$vs.loading.close();
          this.roles = {
            label: res.data[0].rol,
            value: res.data[0].roles_id
          };
          this.genero = {
            label: res.data[0].genero_des,
            value: res.data[0].genero
          };
          this.form.user_id = res.data[0].id_user;
          this.form.nombre = res.data[0].nombre;
          this.form.usuario = res.data[0].email;
          this.form.password = "nochanges";
          this.form.repetir = "nochanges";
          this.form.direccion = res.data[0].domicilio;
          this.form.telefono = res.data[0].telefono;
          this.form.celular = res.data[0].celular;

          this.form.nombre_contacto = res.data[0].nombre_contacto;
          this.form.tel_contacto = res.data[0].tel_contacto;
          this.form.parentesco_contacto = res.data[0].parentesco;
          /**llenando los puestos */
          res.data[0].puestos.forEach(element => {
            this.form.puestos.push(element.id);
          });
        })
        .catch(err => {
          this.$vs.loading.close();
        });
    },

    acceptAlert() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$vs.notify({
              title: "Error",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000"
            });
            return;
          } else {
            //se confirma la cntraseña

            if (this.getTipoformulario == "agregar") {
              /**se manda llamar la funcion de agregar usuario */
              this.openConfirmarAceptar = true;
              this.callback = this.saveUsuario;
            } else {
              this.callback = this.updateUsuario;
              /**se manda agregar  la funcion de modificar */
              this.operConfirmar = true;
            }
          }
        })
        .catch(() => {});
    },
    cancel() {
      this.botonConfirmarDanger = "Salir y limpiar";
      this.accionConfirmarDanger =
        "Esta acción limpiará los datos que capturó en el formulario.";
      this.openConfirmarDanger = true;
      this.callBackConfirmar = this.cerrarVentana;
    },
    cerrarVentana() {
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
      this.form.puestos = [];
      this.$emit("closeVentana");
    },
    get_roles() {
      this.$vs.loading();
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
          this.$vs.loading.close();
        })
        .catch(err => {
          this.$vs.loading.close();
        });
    },
    get_puestos() {
      usuarios
        .get_puestos()
        .then(res => {
          //le agrego todos los roles
          this.puestos = res.data;
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
          this.$vs.notify({
            title: "Agregar Usuarios",
            text: "Se ha creado el nuevo usuario exitosamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 4000
          });
          this.$emit("get_data");
          this.cerrarVentana();
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
              this.$vs.notify({
                title: "Error",
                text: "Verifique que todos los datos han sido capturados",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                position: "bottom-right",
                time: "4000"
              });
              /**error de validacion */
              this.errores = err.response.data.error;
            }
          }
        });
    },

    updateUsuario() {
      this.$vs.loading();
      //limpiando errores
      this.errores = [];
      this.form.rol_id = this.roles.value;
      this.form.genero = this.genero.value;
      usuarios
        .update_usuario(this.form)
        .then(res => {
          this.$vs.loading();
          this.$vs.notify({
            title: "Modificar Usuarios",
            text: "Se ha modificado el usuario exitosamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 4000
          });
          this.$emit("get_data");
          this.cerrarVentana();
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
              this.$vs.notify({
                title: "Error",
                text: "Verifique que todos los datos han sido capturados",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                position: "bottom-right",
                time: "4000"
              });
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