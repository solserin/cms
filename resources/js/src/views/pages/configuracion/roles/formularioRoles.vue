<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups roles big-forms"
      close="cancel"
      :title="title"
      :active.sync="showVentana"
      ref="roles"
    >
      <div class="w-full sm:w-12/12 md:w-10/12 lg:w-10/12 xl:w-10/12 px-2 pb-4">
        <h3 class="text-xl">
          <feather-icon icon="SettingsIcon" svgClasses="w-5 h-5" />
          <span>Caracteríticas del Rol</span>
        </h3>
      </div>
      <vs-divider />

      <div class="flex flex-wrap">
        <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
          <label class="text-sm opacity-75 font-bold">
            Nombre del Rol
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            data-vv-as=" "
            ref="nombre"
            name="nombre_rol"
            data-vv-validate-on="blur"
            v-validate="'required'"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del rol"
            v-model="form.rol"
          />
          <div>
            <span class="text-danger text-sm">{{ errors.first('nombre_rol') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.rol">{{errores.rol[0]}}</span>
          </div>
        </div>

        <div class="w-full px-2">
          <div v-for="seccion in modulos" :key="seccion.id">
            <p class="font-medium py-2 capitalize font-bold">sección de {{seccion.seccion}}</p>

            <div class="modulos my-2 py-2" v-for="modulo in seccion.modulos" :key="modulo.id">
              <div
                :class="['flex flex-wrap',es_agrupador(modulo)==true?'text-primary text-center':'']"
              >
                <div class="w-full font-medium" v-if="es_agrupador(modulo)==true">{{modulo.modulo}}</div>
                <div
                  v-else
                  class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2 font-medium"
                >
                  <div class="ml-1">{{modulo.modulo}}</div>
                  <vs-checkbox
                    ref="permisos_modulo"
                    color="success"
                    class="capitalize text-base font-medium mt-2 permiso"
                    v-model="form.modulos"
                    :vs-value="modulo.id"
                  >Seleccionar Todos</vs-checkbox>
                  {{ form.modulos }}
                </div>
                <div class="w-full sm:w-12/12 md:w-8/12 lg:w-8/12 xl:w-8/12 px-2">
                  <div class="flex flex-wrap">
                    <ul
                      class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 p-2"
                      v-for="permiso in modulo.permisos"
                      :key="permiso.id"
                    >
                      <li>
                        <label class="capitalize text-base font-medium">{{ permiso.permiso }}</label>
                        <vs-switch
                          ref="permiso"
                          color="success"
                          v-model="form.permisos"
                          :vs-value="permiso.id"
                        />
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            {{ form.permisos }}
          </div>
        </div>
      </div>
      <div class="w-full sm:w-12/12 md:w-4/12 lg:w-3/12 xl:w-3/12 mt-8 pb-8 px-2 mr-auto ml-auto">
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
import roles from "@services/Roles";
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
        this.$refs["roles"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancel();
        };
        this.$nextTick(() =>
          this.$refs["nombre"].$el.querySelector("input").focus()
        );
        /**obtengo los datos para llenar el form con los permisos segun su modulo */
        this.get_modulos_permisos();

        if (this.getTipoformulario == "agregar") {
          this.title = "Registrar Nuevo Rol de Usuario";
        } else {
          this.title = "Modificar Permisos de un Rol";
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
      generosOptions: generosOptions,
      operConfirmar: false,
      callback: Function,
      accionNombre: "agregar nuevo usuario",
      modulos: [],
      form: {
        modulos: [],
        permisos: [],
        rol: "",
        rol_id: ""
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
    es_agrupador(dato) {
      if (dato.parent_modulo_id == 0 && dato.url == "") {
        return true;
      } else return false;
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
              this.callback = this.saveRol;
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
      this.form.rol_id = "";
      this.form.rol = "";
      this.form.modulos = [];
      this.form.permisos = [];
      this.$emit("closeVentana");
    },

    get_modulos_permisos() {
      this.$vs.loading();
      roles
        .get_modulos_permisos()
        .then(res => {
          this.modulos = res.data;

          this.$nextTick(() => {
            let index_modulo = 0;
            let permiso_index = 0;
            this.modulos.forEach(secciones => {
              secciones.modulos.forEach(modulo => {
                if (modulo.url != "") {
                  this.$refs["permisos_modulo"][index_modulo].$el.querySelector(
                    "input"
                  ).onclick = $event => {
                    /**revisar si se activo el check */
                    this.checarTodosModulo($event, modulo.id);
                  };

                  index_modulo++;
                }

                modulo.permisos.forEach(permiso => {
                  this.$refs["permiso"][permiso_index].$el.querySelector(
                    "input"
                  ).onclick = $event => {
                    this.updateChecarTodosModulo($event, modulo.id, permiso.id);
                  };
                  permiso_index++;
                });
              });
            });
          });
          this.$vs.loading.close();
        })
        .catch(err => {
          this.$vs.loading.close();
        });
    },

    updateChecarTodosModulo(event, modulo_id, permiso_id) {
      this.$nextTick(() => {
        let total_permisos_modulo = 0; //total de permisos del modulo seleccionadp
        let total_permisos_activados = 0; //total de permisos que ha activado
        let index_permiso = 0; //index en el que se encuenta el permiso clickeado
        if (event.target.checked == true) {
          /**checar si se debe de prender el boton de encender todos setgun si estan todos los permisos de ese modulo activado */
          this.modulos.forEach(secciones => {
            secciones.modulos.forEach(modulo => {
              if (modulo.id == modulo_id) {
                //si ya estamos en el modulo que buscamos prender se debe reccorer el total de permisos para sacar el total
                //de activados
                modulo.permisos.forEach(permiso => {
                  if (
                    this.$refs["permiso"][index_permiso].$el.querySelector(
                      "input"
                    ).checked == true
                  ) {
                    total_permisos_activados++;
                  }
                  //se aumenta el index cel permiso
                  index_permiso++;
                  if ((permiso.modulos_id = modulo_id)) {
                    if (total_permisos_activados == modulo.permisos.length) {
                      //aqui al ya coincidir se debe de activar el modulo al que pertenece el permiso activado
                      this.removeA(this.form.modulos, modulo_id);
                      this.form.modulos.push(modulo_id);
                    }
                  }
                });
                return false;
              }
            });
          });
        } else {
          /**aqui se apaga el checar modulo a fuerzas este o no prendido*/
          let index_modulo = 0;
          this.modulos.forEach(secciones => {
            secciones.modulos.forEach(modulo => {
              if (modulo.id == modulo_id) {
                this.removeA(this.form.modulos, modulo_id);
                this.$refs["permisos_modulo"][index_modulo].$el.querySelector(
                  "input"
                ).checked = false;
              }
              index_modulo++;
            });
          });
        }
      });
    },
    checarTodosModulo(event, modulo_id) {
      /**prendiendo todos los del modulo */
      this.$nextTick(() => {
        let index_permiso = 0;
        this.modulos.forEach(secciones => {
          secciones.modulos.forEach(modulo => {
            modulo.permisos.forEach(permiso => {
              if (modulo_id == permiso.modulos_id) {
                this.removeA(this.form.permisos, permiso.id);
                /**verificando la casilla */
                if (event.target.checked == true) {
                  this.form.permisos.push(permiso.id);
                  /**se remueve el permiso del arreglo y se da el click */
                  this.$refs["permiso"][index_permiso].$el.querySelector(
                    "input"
                  ).checked = true;
                } else {
                  this.$refs["permiso"][index_permiso].$el.querySelector(
                    "input"
                  ).checked = false;
                }
              }
              index_permiso++;
            });
          });
        });
      });
    },

    removeA(arr) {
      var what,
        a = arguments,
        L = a.length,
        ax;
      while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax = arr.indexOf(what)) !== -1) {
          arr.splice(ax, 1);
        }
      }
      return arr;
    },

    //funcion que inserta el nuevo rol
    saveRol() {
      this.$vs.loading();
      //limpiando errores
      this.errores = [];
      roles
        .add_roles(this.form)
        .then(res => {
          console.log("saveRol -> res", res);
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
            console.log("saveRol -> err.response", err.response);
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