<template >
  <div class="centerx">
    <vs-popup
      fullscreen
      close="cancelar"
      title="catálogo de clientes"
      :active.sync="showVentana"
      @close="cancelar"
    >
      <!--inicio cliente-->
      <vx-card>
        <template slot="no-body">
          <div class="pt-6">
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                <div class="flex flex-wrap">
                  <div class="w-full sm:w-12/12 md:w-10/12 lg:w-10/12 xl:w-10/12 px-2">
                    <h3 class="text-xl">
                      <feather-icon icon="UsersIcon" class="mr-2" svgClasses="w-5 h-5" />
                      <span v-if="getTipoformulario=='agregar'">Formulario de Registro</span>
                      <span v-else>Formulario de Modificación de Clientes</span>
                    </h3>
                    <p class="pt-2 text-silver font-medium">Por favor ingrese los siguientes datos.</p>
                  </div>
                  <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2 my-3">
                    <vs-button
                      icon-pack="feather"
                      icon="icon-database"
                      color="success"
                      class="w-full"
                      @click="acceptAlert()"
                    >
                      <span v-if="getTipoformulario=='agregar'">Guardar Cliente</span>
                      <span v-else>Modificar Cliente</span>
                    </vs-button>
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

              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
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

              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  <span>Género</span>
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <v-select
                  :options="generos"
                  :clearable="false"
                  :dir="$vs.rtl ? 'rtl' : 'ltr'"
                  v-model="form.genero"
                  class="pb-1 pt-1"
                  v-validate:genero_computed.immediate="'required'"
                  name="genero"
                  data-vv-as=" "
                >
                  <div slot="no-options">Seleccione una opción</div>
                </v-select>
                <div>
                  <span class="text-danger text-sm">{{ errors.first('genero') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores['genero.value']"
                  >{{errores['genero.value'][0]}}</span>
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
                  v-validate:nacionalidad_computed.immediate="'required'"
                  name="nacionalidades_id"
                  data-vv-as=" "
                >
                  <div slot="no-options">Seleccione una opción</div>
                </v-select>
                <div>
                  <span class="text-danger text-sm">{{ errors.first('nacionalidades_id') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores['nacionalidad.value']"
                  >{{errores['nacionalidad.value'][0]}}</span>
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
                  data-vv-as=" "
                  name="rfc"
                  maxlength="13"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="e.j. MELM8305281H0"
                  v-model="form.rfc"
                  v-validate:rfc_validacion_computed="'required'"
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
                  v-validate:razon_social_validacion_computed="'required'"
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
                  v-validate:direccion_fiscal_validacion_computed="'required'"
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
      <!--fin cliente-->
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
      :accion="'He revisado la información y quiero registrar a este cliente'"
      :confirmarButton="'Guardar Cliente'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import Confirmar from "@pages/Confirmar";
//componente de password
import Password from "@pages/confirmar_password";
import cementerio from "@services/cementerio";
import clientes from "@services/clientes";
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
    },
    tipo: {
      type: String,
      required: true
    },
    id_cliente: {
      type: Number,
      required: false,
      default: 0
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.$nextTick(() =>
          this.$refs["nombre_cliente"].$el.querySelector("input").focus()
        );
        if (this.getTipoformulario == "modificar") {
          /**se cargan los datos al formulario */
          this.get_cliente_by_id(this.get_cliente_id);
        }
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
    getTipoformulario: {
      get() {
        return this.tipo;
      },
      set(newValue) {
        return newValue;
      }
    },
    get_cliente_id: {
      get() {
        return this.id_cliente;
      },
      set(newValue) {
        return newValue;
      }
    },

    nacionalidad_computed: function() {
      return this.form.nacionalidad.value;
    },
    genero_computed: function() {
      return this.form.genero.value;
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
    },
    rfc_validacion_computed: function() {
      if (
        this.form.rfc.trim() != "" ||
        this.form.razon_social.trim() != "" ||
        this.form.direccion_fiscal.trim() != ""
      )
        return this.form.rfc;
      else return true;
    },
    razon_social_validacion_computed: function() {
      if (
        this.form.rfc.trim() != "" ||
        this.form.razon_social.trim() != "" ||
        this.form.direccion_fiscal.trim() != ""
      )
        return this.form.razon_social;
      else return true;
    },
    direccion_fiscal_validacion_computed: function() {
      if (
        this.form.rfc.trim() != "" ||
        this.form.razon_social.trim() != "" ||
        this.form.direccion_fiscal.trim() != ""
      )
        return this.form.direccion_fiscal;
      else return true;
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
      accionNombre: "Modificar Cliente",
      nacionalidades: [],
      generos: [
        {
          value: "1",
          label: "Hombre"
        },
        {
          value: "2",
          label: "Mujer"
        }
      ],
      form: {
        /**en caso de modificar */
        id_cliente_modificar: 0,
        /**datos del cliente personal */
        genero: {
          value: "1",
          label: "Hombre"
        },
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
    get_cliente_by_id() {
      /**trae la informacion de el cliente por id */
      this.$vs.loading();
      clientes
        .get_cliente_id(this.get_cliente_id)
        .then(res => {
          //actualizo los datos en el formulario
          this.form.nombre = res.data.nombre;
          this.form.direccion = res.data.direccion;
          this.form.ciudad = res.data.ciudad;
          this.form.estado = res.data.estado;
          this.form.nacionalidad = {
            value: res.data.nacionalidad["id"],
            label: res.data.nacionalidad["nacionalidad"]
          };
          this.form.genero = {
            value: res.data.genero["id"],
            label: res.data.genero["genero"]
          };
          this.form.telefono = res.data.telefono;
          this.form.celular = res.data.celular;
          this.form.telefono_extra = res.data.telefono_extra;
          this.form.email = res.data.email;

          var partes = res.data.fecha_nac.split("-");
          //yyyy-mm-dd
          this.form.fecha_nac = new Date(partes[0], partes[1] - 1, partes[2]);

          /**datos del cliente fiscal */
          this.form.rfc = res.data.rfc != null ? res.data.rfc : "";
          this.form.direccion_fiscal =
            res.data.direccion_fiscal != null ? res.data.direccion_fiscal : "";
          this.form.razon_social =
            res.data.razon_social != null ? res.data.razon_social : "";
          /**datos del contacto extra de referencia */
          this.form.nombre_contacto = res.data.nombre_contacto;
          this.form.parentesco_contacto = res.data.parentesco_contacto;
          this.form.telefono_contacto = res.data.telefono_contacto;
          this.$vs.loading.close();
        })
        .catch(err => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: "Modificar Cliente",
            text: "Ocurrió un error al traer la informacion, reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-right",
            time: "4000"
          });
          this.cerrarVentana();
        });
    },
    get_nacionalidades() {
      clientes
        .get_nacionalidades()
        .then(res => {
          //le agrego las nacionalidades
          this.nacionalidades = [];
          this.nacionalidades.push({ label: "Seleccione 1", value: "" });
          res.data.forEach(element => {
            this.nacionalidades.push({
              label: element.nacionalidad,
              value: element.id
            });
          });
          this.form.nacionalidad = this.nacionalidades[122];
        })
        .catch(err => {});
    },
    acceptAlert() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$vs.notify({
              title: "Guardar Cliente",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000"
            });
          } else {
            this.errores = [];
            if (this.getTipoformulario == "agregar") {
              this.callBackConfirmarAceptar = this.guardar_cliente;
              this.openConfirmarAceptar = true;
            } else {
              /**modificar, se valida con password */
              this.form.id_cliente_modificar = this.get_cliente_id;
              this.callback = this.modificar_cliente;
              this.operConfirmar = true;
            }
          }
        })
        .catch(() => {});
    },

    guardar_cliente() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      clientes
        .guardar_cliente(this.form)
        .then(res => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Registro de Clientes",
              text: "Se ha guardado el cliente correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000
            });
            this.$emit("retornar_id", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Registro de Clientes",
              text: "Error al guardar el cliente, por favor reintente.",
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
              this.$vs.notify({
                title: "Registro de Clientes",
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

    modificar_cliente() {
      //aqui mando modoificar los datos
      this.errores = [];
      this.$vs.loading();
      clientes
        .modificar_cliente(this.form)
        .then(res => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Modificación de Clientes",
              text: "Se ha modificó el cliente correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000
            });
            this.$emit("retornar_id", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Modificación de Clientes",
              text: "Error al guardar el cliente, por favor reintente.",
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
              this.$vs.notify({
                title: "Modificación de Clientes",
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
      this.form.genero = {
        value: "1",
        label: "Hombre"
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
  created() {
    //cargo nacionalidades
    this.get_nacionalidades();
  }
};
</script>