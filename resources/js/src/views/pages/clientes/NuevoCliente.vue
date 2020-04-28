<template >
  <div class="centerx">
    <vs-popup
      fullscreen
      close="cancelar"
      title="catálogo de clientes"
      :active.sync="showVentana"
      @close="cancelar"
    >
      <!--inicio venta-->
      <vx-card>
        <template slot="no-body">
          <div class="pt-6">
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                <div class="flex flex-wrap">
                  <div class="w-full sm:w-12/12 md:w-10/12 lg:w-10/12 xl:w-10/12 px-2">
                    <h3 class="text-xl">
                      <feather-icon icon="UsersIcon" class="mr-2" svgClasses="w-5 h-5" />Formulario de Registro
                    </h3>
                    <p class="pt-2 text-silver font-medium">Por favor ingrese los siguientes datos.</p>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2 my-3">
                    <vs-button
                      icon-pack="feather"
                      icon="icon-database"
                      color="success"
                      class="w-full"
                    >Guardar Datos</vs-button>
                  </div>
                </div>
                <vs-divider />
              </div>
            </div>

            <!--datos del titular y beneficiarios-->
            <div class="flex flex-wrap px-2">
              <div class="w-full pt-3 pb-3 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="UserCheckIcon" class="mr-2" svgClasses="w-5 h-5" />Información del Cliente
                </h3>
              </div>
              <!--datos del titular-->
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Nombre completo
                  <span class="text-danger text-sm">(*)</span>
                </label>

                <vs-input
                  ref="nombre_cliente"
                  name="nombre"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="85"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el nombre del cliente"
                  v-model="form.nombre"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('nombre') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.nombre">{{errores.nombre[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Fecha de Nacimiento
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <datepicker
                  :language="spanishDatepicker"
                  :disabled-dates="disabledDates"
                  name="fecha_nacimiento"
                  data-vv-as=" "
                  format="yyyy-MM-dd"
                  v-validate:fecha_nacimiento_validacion_computed.immediate="'required'"
                  placeholder="Fecha de Nacimiento"
                  v-model="form.fecha_nac"
                  class="w-full pb-1 pt-1"
                ></datepicker>
                <div>
                  <span class="text-danger text-sm">{{ errors.first('fecha_nacimiento') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.fecha_nac"
                  >{{errores.fecha_nac[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Domicilio Completo
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="direccion"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="150"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Domicilio Completo"
                  v-model="form.direccion"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('direccion') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.direccion"
                  >{{errores.direccion[0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  <span>Nacionalidad</span>
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <v-select
                  :options="nacionalidades"
                  :clearable="false"
                  :dir="$vs.rtl ? 'rtl' : 'ltr'"
                  v-model="form.nacionalidad"
                  class="pb-1 pt-1"
                  v-validate:plan_de_venta_computed.immediate="'required'"
                  name="nacionalidades_id"
                  data-vv-as="Nacionalidad"
                >
                  <div slot="no-options">Seleccione una opción</div>
                </v-select>
                <div>
                  <span class="text-danger text-sm">{{ errors.first('nacionalidades_id') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.nacionalidades_id"
                  >{{errores.nacionalidades_id[0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Ciudad
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="ciudad"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="45"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese la ciudad"
                  v-model="form.ciudad"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('ciudad') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.ciudad">{{errores.ciudad[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Estado
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="estado"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'required'"
                  maxlength="45"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el estado"
                  v-model="form.estado"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('estado') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.estado">{{errores.estado[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">Tél. Domicilio</label>
                <vs-input
                  maxlength="25"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el teléfono del domicilio"
                  v-model="form.telefono"
                />
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">Celular</label>
                <vs-input
                  name="celular"
                  data-vv-as=" "
                  maxlength="25"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese un número de celular"
                  v-model="form.celular"
                />
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">Tél. Extra (Trabajo)</label>
                <vs-input
                  maxlength="25"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese un teléfono extra, del trabajo por ejemplo."
                  v-model="form.telefono_extra"
                />
              </div>

              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                <label class="text-sm opacity-75 font-bold">Correo Electrónico</label>
                <vs-input
                  name="email"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate="'email'"
                  maxlength="85"
                  type="email"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese el email"
                  v-model="form.email"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('email') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.email">{{errores.email[0]}}</span>
                </div>
              </div>

              <!--fin de datos del titular-->

              <vs-divider />
              <div class="w-full pt-3 pb-3 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="UserCheckIcon" class="mr-2" svgClasses="w-5 h-5" />Información Fiscal
                </h3>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  RFC
                  <span v-if="datos_fiscales_validacion_computed" class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="rfc"
                  maxlength="13"
                  data-vv-validate-on="blur"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="e.j. MELM8305281H0"
                  v-model="form.rfc"
                  v-validate:datos_fiscales_validacion_computed.immediate="'required'"
                  v-validate="'min:12|max:13'"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('rfc') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.rfc">{{errores.rfc[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Razón Social
                  <span
                    v-if="datos_fiscales_validacion_computed"
                    class="text-danger text-sm"
                  >(*)</span>
                </label>
                <vs-input
                  name="razon_social"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate:datos_fiscales_validacion_computed.immediate="'required'"
                  maxlength="95"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ej. Mi Empresa SA DE CV"
                  v-model="form.razon_social"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('razon_social') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.razon_social"
                  >{{errores.razon_social[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Dirección Fiscal
                  <span
                    v-if="datos_fiscales_validacion_computed"
                    class="text-danger text-sm"
                  >(*)</span>
                </label>
                <vs-input
                  name="direccion_fiscal"
                  data-vv-as=" "
                  data-vv-validate-on="blur"
                  v-validate:datos_fiscales_validacion_computed.immediate="'required'"
                  maxlength="95"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ej. Av. Américas #405, Col. Lomas C.P. 30404 Mazatlán, Sin."
                  v-model="form.direccion_fiscal"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('direccion_fiscal') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.direccion_fiscal"
                  >{{errores.direccion_fiscal[0]}}</span>
                </div>
              </div>

              <vs-divider />

              <div class="w-full pt-3 pb-3 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="UserCheckIcon" class="mr-2" svgClasses="w-5 h-5" />Referencia de Contacto
                </h3>
              </div>
              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">Nombre de un contacto de referencia</label>
                <vs-input
                  name="nombre_contacto"
                  maxlength="150"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ej. Papá, Mamá, Hermano, Conocido, etc."
                  v-model="form.nombre_contacto"
                />
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">Parentesco con el contacto</label>
                <vs-input
                  name="parentesco_contacto"
                  data-vv-as=" "
                  maxlength="45"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ej. Hermano"
                  v-model="form.parentesco_contacto"
                />
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75 font-bold">Teléfono del contacto</label>
                <vs-input
                  name="telefono_contacto"
                  data-vv-as=" "
                  maxlength="35"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ingrese un teléfono"
                  v-model="form.telefono_contacto"
                />
              </div>

              <vs-divider />
            </div>
            <div class="flex flex-wrap pt-4 px-2">
              <div class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2">
                <div class="mt-5">
                  <p class>
                    <span class="text-danger font-medium">Ojo:</span>
                    Por favor revise la información ingresada, si todo es correcto de click en “Guardar Datos” en la parte superior.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </template>
      </vx-card>
      <!--fin venta-->
    </vs-popup>
    <Password
      :show="operConfirmar"
      :callback-on-success="callback"
      @closeVerificar="closeChecker"
      :accion="accionNombre"
    ></Password>
    <Confirmar
      :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarSinPassword=false"
      :accion="accionConfirmarSinPassword"
      :confirmarButton="botonConfirmarSinPassword"
    ></Confirmar>

    <ConfirmarAceptar
      :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar"
      @closeVerificar="openConfirmarAceptar=false"
      :accion="'He revisado la información y quiero guardar la venta'"
      :confirmarButton="'Guardar Venta'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import Confirmar from "@pages/Confirmar";
//componente de password
import Password from "@pages/confirmar_password";
import cementerio from "@services/cementerio";
import usuarios from "@services/Usuarios";
import vSelect from "vue-select";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";

/**VARIABLES GLOBALES */

export default {
  components: {
    "v-select": vSelect,
    Password,
    Datepicker,
    Confirmar,
    ConfirmarAceptar
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
        this.$nextTick(() => this.setFocusOnInput("nombre_cliente"));
      }
    }
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
    fecha_nacimiento_validacion_computed: function() {
      return this.form.fecha_nac;
    },
    datos_fiscales_validacion_computed: function() {
      if (
        this.form.rfc.trim() != "" ||
        this.form.razon_social.trim() != "" ||
        this.form.direccion_fiscal.trim() != ""
      )
        return true;
      else return false;
    }
  },
  data() {
    return {
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      disabledDates: {
        from: new Date()
      },
      spanishDatepicker: es,
      operConfirmar: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Guardar Cliente",
      nacionalidades: [],
      form: {
        /**datos del cliente personal */
        nombre: "",
        direccion: "",
        ciudad: "",
        estado: "",
        nacionalidad: {
          value: "122",
          label: "Mexicana"
        },
        telefono: "",
        celular: "",
        telefono_extra: "",
        email: "",
        fecha_nac: "",

        /**datos del cliente fiscal */
        rfc: "",
        direccion_fiscal: "",
        razon_social: "",

        /**datos del contacto extra de referencia */
        nombre_contacto: "",
        parentesco_contacto: "",
        telefono_contacto: ""

        /**datos del cliente contacto de referencia */
      },
      errores: []
    };
  },
  methods: {
    setFocusOnInput(inputName) {
      /**
       * @see https://vuejs.org/v2/api/#vm-el
       * @see https://vuejs.org/v2/api/#vm-refs
       */
      // you could just call this.$refs[inputName].focusInput() but i'm not shure if it belongs to the public API
      let inputEl = this.$refs[inputName].$el.querySelector("input");
      console.log(inputEl.focus); // <== See if `focus` method avaliable
      inputEl.focus(); //  <== This time the focus will work properly
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
            this.callBackConfirmarAceptar = this.guardarVenta;
            this.openConfirmarAceptar = true;
          }
        })
        .catch(() => {});
    },

    guardarVenta() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      cementerio
        .guardarVenta(this.form)
        .then(res => {
          //console.log(res);
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Ventas de Propiedades",
              text: "Se ha guardado la venta correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000
            });
            this.$emit("ver_pdfs_nueva_venta", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Ventas de Propiedades",
              text: "Error al guardar la venta, por favor reintente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 4000
            });
          }

          this.$vs.loading.close();
        })
        .catch(err => {
          if (err.response) {
            //console.log("guardarVenta -> err.response", err.response);
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
              //checo si existe cada error
              this.errores = err.response.data.error;
              if (this.errores.ubicacion) {
                //la propiedad esa ya ha sido vendida
                this.$vs.notify({
                  title: "Seleccionar Terreno",
                  text: "Este terreno ya ha sido vendido previamente.",
                  iconPack: "feather",
                  icon: "icon-alert-circle",
                  color: "danger",
                  time: 5000
                });
              }

              this.$vs.notify({
                title: "Guardar Venta",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000
              });
              //console.log(err.response);
            }
          }
          this.$vs.loading.close();
        });
    },
    cancel() {
      this.$emit("closeVentana");
    },

    cancelar() {
      this.botonConfirmarSinPassword = "Salir y limpiar";
      this.accionConfirmarSinPassword =
        "Esta acción limpiará los datos que capturó en el formulario.";
      this.openConfirmarSinPassword = true;
      this.callBackConfirmar = this.cerrarVentana;
    },

    cerrarVentana() {
      this.openConfirmarSinPassword = false;
      this.limpiarVentana();
      this.$emit("closeVentana");
    },
    //regresa los datos a su estado inicial
    limpiarVentana() {
      this.form.nombre = "";
      this.form.direccion = "";
      this.form.ciudad = "";
      this.form.estado = "";
      this.form.nacionalidad = {
        value: "122",
        label: "Mexicana"
      };
      this.form.telefono = "";
      this.form.celular = "";
      this.form.telefono_extra = "";
      this.form.email = "";
      this.form.fecha_nac = "";
      /**datos del cliente fiscal */
      this.form.rfc = "";
      this.form.direccion_fiscal = "";
      this.form.razon_social = "";
      /**datos del contacto extra de referencia */
      this.form.nombre_contacto = "";
      this.form.parentesco_contacto = "";
      this.form.telefono_contacto = "";
    },

    closeChecker() {
      this.operConfirmar = false;
    }
  },
  created() {}
};
</script>