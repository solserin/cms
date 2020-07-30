<template>
  <div class="centerx">
    <vs-popup
      class="normal-forms venta-propiedades background-header-forms forms-popups-85"
      fullscreen
      close="cancelar"
      :title="
        getTipoformulario == 'modificar'
          ? 'Modificar Solicitud de Servicio Funerario'
          : 'Solicitud de Servicio Funerario'
      "
      :active.sync="showVentana"
      ref="formulario"
    >
      <!--inicio venta-->
      <div class="venta-details">
        <div class="flex flex-wrap">
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
            <div class="float-left pb-5 px-2">
              <img width="36px" src="@assets/images/corpse.svg" />
              <h3
                class="float-right mt-2 ml-3 text-xl px-2 py-1 bg-seccion-forms capitalize"
              >
                Detalle de la solicitud y del Fallecido
              </h3>
            </div>

            <div class="w-full px-2">
              <vs-divider />
            </div>
            <div class="flex flex-wrap mt-1">
              <div
                class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center"
              >
                <label class="text-sm font-bold">Origen de Solicitud</label>
                <div class="mt-3">
                  <vs-radio
                    vs-name="llamada_b"
                    v-model="form.llamada_b"
                    :vs-value="1"
                    class="mr-4"
                    >Por Llamada</vs-radio
                  >
                  <vs-radio
                    vs-name="llamada_b"
                    v-model="form.llamada_b"
                    :vs-value="0"
                    class="mr-4"
                    >En Sucursal</vs-radio
                  >
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Nombre del Fallecido
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  v-validate.disabled="'required'"
                  name="nombre_afectado"
                  data-vv-as=" "
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Nombre del Fallecido"
                  v-model="form.nombre_afectado"
                  maxlength="100"
                  ref="fallecido"
                />
                <div>
                  <span class="text-danger">{{
                    errors.first("nombre_afectado")
                  }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger"
                    v-if="this.errores.nombre_afectado"
                    >{{ errores.nombre_afectado[0] }}</span
                  >
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Fecha y Hora de Solicitud
                  <span class="texto-importante">(*)</span>
                </label>

                <flat-pickr
                  name="fecha_solicitud"
                  data-vv-as=" "
                  v-validate:fecha_solicitud_validacion_computed.immediate="
                    'required'
                  "
                  :config="configdateTimePickerWithTime"
                  v-model="form.fecha_solicitud"
                  placeholder="Fecha y Hora de Solicitud"
                  class="w-full my-1"
                />

                <div>
                  <span class="text-danger">{{
                    errors.first("fecha_solicitud")
                  }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger"
                    v-if="this.errores.fecha_solicitud"
                    >{{ errores.fecha_solicitud[0] }}</span
                  >
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Causa de Muerte
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  name="causa_muerte"
                  data-vv-as=" "
                  v-validate.disabled="'required'"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Causa de Muerte"
                  v-model="form.causa_muerte"
                  maxlength="100"
                />
                <div>
                  <span class="text-danger">{{
                    errors.first("causa_muerte")
                  }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger" v-if="this.errores.causa_muerte">{{
                    errores.causa_muerte[0]
                  }}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  <span>Muerte Natural</span>
                  <span class="texto-importante">(*)</span>
                </label>
                <v-select
                  :options="opciones"
                  :clearable="false"
                  :dir="$vs.rtl ? 'rtl' : 'ltr'"
                  v-model="form.muerte_natural_b"
                  class="mb-4 sm:mb-0 pb-1 pt-1"
                  name="muerte_natural_b"
                  data-vv-as=" "
                >
                  <div slot="no-options">
                    Seleccione 1
                  </div>
                </v-select>
                <div>
                  <span class="text-danger">{{
                    errors.first("muerte_natural_b")
                  }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger"
                    v-if="this.errores['muerte_natural_b.value']"
                    >{{ errores["muerte_natural_b.value"][0] }}</span
                  >
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  <span>Enfermedad Contagiosa</span>
                  <span class="texto-importante">(*)</span>
                </label>
                <v-select
                  :options="opciones"
                  :clearable="false"
                  :dir="$vs.rtl ? 'rtl' : 'ltr'"
                  v-model="form.contagioso_b"
                  class="mb-4 sm:mb-0 pb-1 pt-1"
                  name="contagioso_b"
                  data-vv-as=" "
                >
                  <div slot="no-options">
                    Seleccione 1
                  </div>
                </v-select>
                <div>
                  <span class="text-danger">{{
                    errors.first("contagioso_b")
                  }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger"
                    v-if="this.errores['contagioso_b.value']"
                    >{{ errores["contagioso_b.value"][0] }}</span
                  >
                </div>
              </div>
            </div>

            <div class="flex flex-wrap mt-1">
              <div class="w-full px-2 mt-2">
                <p class="text-xs">
                  <span class="text-danger font-medium">Ojo:</span>
                  Debe ingresar la información sobre el fallecido y las causas
                  de la muerte para que el personal que traslade el cuerpo tome
                  sus precauciones.
                </p>
                <vs-divider />
              </div>
            </div>

            <div class="flex flex-wrap mt-1">
              <div class="float-left pb-3 px-2">
                <img width="36px" src="@assets/images/informer.svg" />
                <h3
                  class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
                >
                  Datos de Contacto del Informante
                </h3>
              </div>
              <div class="w-full px-2">
                <vs-divider />
              </div>
              <!--vendedor-->
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Nombre del Informante
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  v-validate.disabled="'required'"
                  name="nombre_informante"
                  data-vv-as=" "
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Nombre del Informante"
                  v-model="form.nombre_informante"
                  maxlength="100"
                />
                <div>
                  <span class="text-danger">{{
                    errors.first("nombre_informante")
                  }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger"
                    v-if="this.errores.nombre_informante"
                    >{{ errores.nombre_informante[0] }}</span
                  >
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Teléfono del Informante
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  v-validate.disabled="'required'"
                  name="telefono_informante"
                  data-vv-as=" "
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Teléfono del Informante"
                  v-model="form.telefono_informante"
                  :disabled="fueCancelada"
                  maxlength="12"
                />
                <div>
                  <span class="text-danger">{{
                    errors.first("telefono_informante")
                  }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger"
                    v-if="this.errores.telefono_informante"
                    >{{ errores.telefono_informante[0] }}</span
                  >
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Parentesco con el Fallecido
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  v-validate.disabled="'required'"
                  name="parentesco_informante"
                  data-vv-as=" "
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Parentesco con el Fallecido"
                  v-model="form.parentesco_informante"
                  maxlength="100"
                />
                <div>
                  <span class="text-danger">{{
                    errors.first("parentesco_informante")
                  }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger"
                    v-if="this.errores.parentesco_informante"
                    >{{ errores.parentesco_informante[0] }}</span
                  >
                </div>
              </div>
            </div>
            <div class="flex flex-wrap mt-1">
              <div class="w-full px-2 mt-2">
                <p class="text-xs">
                  <span class="text-danger font-medium">Ojo:</span>
                  Ingrese la información mínima del informante para estar en
                  contacto durante el traslado del cuerpo a la funeraria.
                </p>
                <vs-divider />
              </div>
            </div>

            <div class="flex flex-wrap mt-1">
              <div class="float-left pb-3 px-2">
                <img width="36px" src="@assets/images/recoger.svg" />
                <h3
                  class="float-right mt-2 ml-3 text-xl font-medium px-2 py-1 bg-seccion-forms"
                >
                  Ubicación y Traslado del Fallecido
                </h3>
              </div>
              <div class="w-full px-2">
                <vs-divider />
              </div>
              <!--vendedor-->
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  Ubicación del Fallecido
                  <span class="texto-importante">(*)</span>
                </label>
                <vs-input
                  v-validate.disabled="'required'"
                  name="ubicacion_recoger"
                  data-vv-as=" "
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ubicación del Fallecido"
                  v-model="form.ubicacion_recoger"
                  maxlength="100"
                />
                <div>
                  <span class="text-danger">{{
                    errors.first("ubicacion_recoger")
                  }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger"
                    v-if="this.errores.ubicacion_recoger"
                    >{{ errores.ubicacion_recoger[0] }}</span
                  >
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75 font-bold">
                  <span>Encargado de Recoger</span>
                  <span class="texto-importante">(*)</span>
                </label>
                <v-select
                  :options="recogioOpciones"
                  :clearable="false"
                  :dir="$vs.rtl ? 'rtl' : 'ltr'"
                  v-model="form.recogio"
                  class="mb-4 sm:mb-0 pb-1 pt-1"
                  v-validate:recogio_validacion_computed.immediate="'required'"
                  name="recogio"
                  data-vv-as=" "
                >
                  <div slot="no-options">
                    Seleccione 1
                  </div>
                </v-select>
                <div>
                  <span class="text-danger">{{ errors.first("recogio") }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger"
                    v-if="this.errores['recogio.value']"
                    >{{ errores["recogio.value"][0] }}</span
                  >
                </div>
              </div>

              <div
                class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2"
              >
                <label class="text-sm opacity-75 font-bold">
                  Nota/Observación
                </label>
                <vs-input
                  name="nota_solicitud"
                  data-vv-as=" "
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Nota/Observación"
                  v-model="form.nota_solicitud"
                  maxlength="250"
                />
                <div>
                  <span class="text-danger">{{
                    errors.first("nota_solicitud")
                  }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger"
                    v-if="this.errores.nota_solicitud"
                    >{{ errores.nota_solicitud[0] }}</span
                  >
                </div>
              </div>
            </div>
            <div class="flex flex-wrap mt-1">
              <div class="w-full px-2 mt-2">
                <p class="text-xs">
                  <span class="text-danger font-medium">Ojo:</span>
                  Ingrese la dirección donde será recogido el fallecido y
                  seleccione de la lista al empleado responsable del traslado.
                </p>
                <vs-divider />
              </div>
            </div>
          </div>
        </div>

        <!--checkout-->
        <div class="flex flex-wrap my-6">
          <!--precios-->
          <div
            class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 ml-auto mr-auto"
          >
            <div class="flex flex-wrap mt-4">
              <vs-button
                v-if="!fueCancelada"
                class="w-full ml-auto mr-auto"
                @click="acceptAlert()"
                color="success"
                size="small"
              >
                <img
                  width="25px"
                  class="cursor-pointer"
                  src="@assets/images/save.svg"
                />
                <span
                  class="texto-btn"
                  v-if="this.getTipoformulario == 'agregar'"
                  >Guardar Solicitud</span
                >
                <span class="texto-btn" v-else>Modificar Solicitud</span>
              </vs-button>
            </div>
          </div>
        </div>
        <!--fin del checkout-->
      </div>

      <!--fin venta-->
    </vs-popup>
    <Password
      :show="openPassword"
      :callback-on-success="callback"
      @closeVerificar="closePassword"
      :accion="accionNombre"
    ></Password>
    <ConfirmarDanger
      :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarSinPassword = false"
      :accion="accionConfirmarSinPassword"
      :confirmarButton="botonConfirmarSinPassword"
    ></ConfirmarDanger>

    <ConfirmarAceptar
      :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar"
      @closeVerificar="openConfirmarAceptar = false"
      :accion="'He revisado la información y quiero guardar la venta'"
      :confirmarButton="'Guardar Venta'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
/**date picker */
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import funeraria from "@services/funeraria";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import ClientesBuscador from "@pages/clientes/searcher.vue";

/**VARIABLES GLOBALES */
import { alfabeto, configdateTimePickerWithTime } from "@/VariablesGlobales";

export default {
  components: {
    "v-select": vSelect,
    flatPickr,
    Password,
    ConfirmarDanger,
    ConfirmarAceptar,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    //para saber que tipo de formulario es
    tipo: {
      type: String,
      required: true,
    },
    id_solicitud: {
      type: Number,
      required: false,
      default: 0,
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      this.limpiarValidation();
      if (newValue == true) {
        this.$nextTick(() =>
          this.$refs["fallecido"].$el.querySelector("input").focus()
        );
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        (async () => {
          await this.get_personal_recoger();
          if (this.getTipoformulario == "agregar") {
            /**acciones cuando el formulario es de agregar */
          } else {
            /**es modificar */
            /**pasando el valor de la venta id */
            this.form.id_solicitud = this.get_solicitud_id;
            /**se cargan los datos al formulario */
            await this.consultar_venta_id();
          }
        })();
      } else {
        /**acciones al cerrar el formulario */
      }
    },
  },
  computed: {
    /**validar si es modificar el formulario */
    getTipoformulario: {
      get() {
        return this.tipo;
      },
      set(newValue) {
        return newValue;
      },
    },
    get_solicitud_id: {
      get() {
        return this.id_solicitud;
      },
      set(newValue) {
        return newValue;
      },
    },

    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },

    recogio_validacion_computed: function () {
      return this.form.recogio.value;
    },

    fecha_solicitud_validacion_computed: function () {
      return this.form.fecha_solicitud;
    },
    //fin de validaciones calculadas
  },
  data() {
    return {
      /**variables dle modulo */
      configdateTimePickerWithTime: configdateTimePickerWithTime,
      verDisponibilidad: false,
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      alfabeto: alfabeto,
      openPassword: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Modificar Venta",
      opciones: [
        {
          label: "SI",
          value: 1,
        },
        {
          label: "NO",
          value: 0,
        },
      ],
      recogioOpciones: [],
      /**para modificar, se traen los datos aqui */
      datosSolicitud: [],
      //fin var con mapa
      form: {
        /**varaibles del modulo */
        llamada_b: 1,
        nombre_afectado: "",
        fecha_solicitud: "",
        causa_muerte: "",
        muerte_natural_b: {
          label: "SI",
          value: 1,
        },
        contagioso_b: {
          label: "NO",
          value: 0,
        },
        nombre_informante: "",
        telefono_informante: "",
        parentesco_informante: "",
        ubicacion_recoger: "",
        recogio: {
          label: "Seleccione 1",
          value: "",
        },
        nota_solicitud: "",
        /**en caso de modificar*/
        id_solicitud: "",
      },
      errores: [],
    };
  },
  methods: {
    acceptAlert() {
      this.$validator
        .validateAll()
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Error",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "12000",
            });
            return;
          } else {
            /**aqui se hace la validacion en los totales de la venta */
            //se confirma la cntraseña
            /**actualizando los valores de total de venta */
            //fin de actualizar datos de ubicacion
            (async () => {
              if (this.getTipoformulario == "agregar") {
                this.callBackConfirmarAceptar = await this.guardar_solicitud;
                this.openConfirmarAceptar = true;
              } else {
                /**es modificacion */
                this.callback = await this.modificar_venta;
                this.openPassword = true;
              }
            })();
          }
        })
        .catch(() => {});
    },

    async guardar_solicitud() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await funeraria.guardar_solicitud(this.form);
        console.log("guardar_solicitud -> res", res);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Servicios Funerarios",
            text: "Se ha guardado la solicitud correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
          });
          this.$emit("ver_pdfs_nueva_venta", res.data);
          this.cerrarVentana();
        } else {
          this.$vs.notify({
            title: "Servicios Funerarios",
            text: "Error al guardar la solicitud, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 4000,
          });
        }

        this.$vs.loading.close();
      } catch (err) {
        if (err.response) {
          console.log("guardar_solicitud -> err.response", err.response);
          if (err.response.status == 403) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 4000,
            });
          } else if (err.response.status == 422) {
            //checo si existe cada error
            this.errores = err.response.data.error;
            this.$vs.notify({
              title: "Guardar Solicitud",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 5000,
            });
          } else if (err.response.status == 409) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Guardar Solicitud",
              text: err.response.data.error,
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 15000,
            });
          }
        }
        this.$vs.loading.close();
      }
    },

    async modificar_venta() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await planes.modificar_venta(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Servicios Funerarios",
            text: "Se ha modificado la venta correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
          });
          this.$emit("ver_pdfs_nueva_venta", res.data);
          this.cerrarVentana();
        } else {
          this.$vs.notify({
            title: "Servicios Funerarios",
            text: "Error al modificar la venta, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 4000,
          });
        }

        this.$vs.loading.close();
      } catch (err) {
        if (err.response) {
          if (err.response.status == 403) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 4000,
            });
          } else if (err.response.status == 422) {
            //checo si existe cada error
            this.errores = err.response.data.error;

            this.$vs.notify({
              title: "Modificar Venta",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 5000,
            });
          } else if (err.response.status == 409) {
            //este error es por alguna condicion que el contrano no cumple para modificar
            //la propiedad esa ya ha sido vendida
            this.$vs.notify({
              title: "Modificar información de la venta",
              text: err.response.data.error,
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 30000,
            });
          }
        }
        this.$vs.loading.close();
      }
    },
    cancel() {
      this.$emit("closeVentana");
    },

    //get vendedores
    async get_personal_recoger() {
      try {
        let res = await funeraria.get_personal_recoger();
        //le agrego todos los usuarios vendedores
        this.recogioOpciones = [];
        this.recogioOpciones.push({ label: "Seleccione 1", value: "" });
        if (this.getTipoformulario == "agregar") {
          this.form.recogio = this.recogioOpciones[0];
        }
        res.data.forEach((element) => {
          this.recogioOpciones.push({
            label: element.nombre,
            value: element.id,
          });
        });
      } catch (error) {
        /**error al cargar vendedores */
        this.$vs.notify({
          title: "Error",
          text:
            "Ha ocurrido un error al tratar de cargar el catálogo de personal, por favor reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "9000",
        });
        this.cerrarVentana();
      }
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
      this.form.llamada_b = 1;
      this.form.nombre_afectado = "";
      this.form.fecha_solicitud = "";
      this.form.causa_muerte = "";
      this.form.muerte_natural_b = {
        label: "SI",
        value: 1,
      };
      this.form.contagioso_b = {
        label: "NO",
        value: 0,
      };
      this.form.nombre_informante = "";
      this.form.telefono_informante = "";
      this.form.parentesco_informante = "";
      this.form.ubicacion_recoger = "";
      this.form.recogio = {
        label: "Seleccione 1",
        value: "",
      };
      this.form.nota_solicitud = "";
      /**en caso de modificar*/
      this.form.id_solicitud = "";
    },

    closePassword() {
      this.openPassword = false;
    },

    async consultar_venta_id() {
      try {
        this.$vs.loading();
        let res = await planes.consultar_venta_id(this.form.id_solicitud);
        this.datosSolicitud = res.data[0];
        /**se comienza a llenar la informacion de los datos */
        /**verificar si el plan funerario se ha mantenido igual que cuando se vendio */
        /**aqui se se recorrer el array de planes funerarios con la informacion original del plan */
        let plan_original = {
          plan: this.datosSolicitud.venta_plan.nombre_original,
          plan_ingles: this.datosSolicitud.venta_plan.nombre_original_ingles,
          nota: this.datosSolicitud.venta_plan.nota_original,
          nota_ingles: this.datosSolicitud.venta_plan.nota_original_ingles,
          value: this.datosSolicitud.venta_plan.planes_funerarios_id,
          secciones: this.datosSolicitud.venta_plan.secciones_original,
        };
        /**guarda los precios en caso de que no se encuentre el plan original y se deba agregar precios por separado */
        let precios_plan = [];
        let es_igual = true;
        this.planes_funerarios.forEach((element, index_element) => {
          if (index_element > 0) {
            /**capturando los precios del plan  de la venta original*/
            if (element.value == plan_original.value) {
              precios_plan = element.precios;
            }
            if (
              element.value == plan_original.value &&
              element.plan == plan_original.plan &&
              element.plan_ingles == plan_original.plan_ingles &&
              element.nota == plan_original.nota &&
              element.nota_ingles == plan_original.nota_ingles
            ) {
              /**el plan se mantiente tal y como se vendio
               * se procede a ver si los conceptos se mantienen de igual manera
               */
              element.secciones.forEach(function callback(
                seccion,
                index_seccion
              ) {
                if (es_igual == true) {
                  if (
                    !(
                      plan_original.secciones[index_seccion].conceptos.length ==
                      seccion.conceptos.length
                    )
                  ) {
                    /**no es igual */
                    es_igual = false;
                  }
                  if (es_igual == true) {
                    /**verificando si cambio algun concepto */
                    seccion.conceptos.forEach(function callback(
                      concepto,
                      index_concepto
                    ) {
                      if (
                        concepto.concepto !=
                        plan_original.secciones[index_seccion].conceptos[
                          index_concepto
                        ].concepto
                      ) {
                        es_igual = false;
                        return;
                      }
                    });
                  }
                }
              });
              /**si se encontro */
              if (es_igual == true) {
                this.form.plan_funerario = element;
                return;
              }
              return;
            } else {
              /**no esta */
              es_igual = false;
            }
          }
        });

        if (es_igual == false) {
          plan_original = {
            label:
              this.datosSolicitud.venta_plan.nombre_original +
              "(Original de Venta)",
            plan: this.datosSolicitud.venta_plan.nombre_original,
            plan_ingles: this.datosSolicitud.venta_plan.nombre_original_ingles,
            nota: this.datosSolicitud.venta_plan.nota_original,
            nota_ingles: this.datosSolicitud.venta_plan.nota_original_ingles,
            value: this.datosSolicitud.venta_plan.planes_funerarios_id,
            secciones: this.datosSolicitud.venta_plan.secciones_original,
            precios: precios_plan,
          };
          this.planes_funerarios.push(plan_original);
          this.form.plan_funerario = plan_original;
          /**si no esta, se agrega el concepto original*/
        }
        /**cargando la antiguedad de la venta */
        this.ventasAntiguedad.forEach((element) => {
          if (element.value == this.datosSolicitud.antiguedad_operacion_id) {
            this.form.ventaAntiguedad = element;
            return;
          }
        });
        this.form.id_cliente = this.datosSolicitud.cliente_id;
        this.form.cliente = this.datosSolicitud.nombre;
        /**verificando si existe el vendedor o si no para crearlo, podria no existir en caso de que haya sido cancelado */
        this.vendedores.forEach((element) => {
          if (element.value == this.datosSolicitud.venta_plan.vendedor.id) {
            this.form.vendedor = element;
          }
        });
        if (this.form.vendedor.value == "") {
          let vendedor = {
            value: this.datosSolicitud.venta_plan.vendedor.id,
            label:
              "(" +
              this.datosSolicitud.venta_plan.vendedor.nombre +
              ", vendedor de origen)",
          };
          this.vendedores.push(vendedor);
          this.form.vendedor = vendedor;
          /**se agrega el original y se selecciona */
        }
        //fin seleccionar vendedor
        /**fecha de la venta */
        var partes_fecha = this.datosSolicitud.fecha_operacion.split("-");
        //yyyy-mm-dd
        this.form.fecha_solicitud = new Date(
          partes_fecha[0],
          partes_fecha[1] - 1,
          partes_fecha[2]
        );
        /**numeros de control */
        this.form.solicitud = this.datosSolicitud.numero_solicitud;
        this.form.convenio = this.datosSolicitud.numero_convenio;
        //this.form.titulo = this.datosSolicitud.numero_titulo;
        /**datos del titular sustituto */
        this.form.titular_sustituto = this.datosSolicitud.titular_sustituto;
        this.form.parentesco_titular_sustituto = this.datosSolicitud.parentesco_titular_sustituto;
        this.form.telefono_titular_sustituto = this.datosSolicitud.telefono_titular_sustituto;
        /**beneficairios */
        this.form.beneficiarios = this.datosSolicitud.beneficiarios;
        this.form.nota = this.datosSolicitud.nota;
        /**mostrando los datos relacionados al pago */
        this.$vs.loading.close();
      } catch (err) {
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
              time: 4000,
            });
          }
        }
      }
    },

    limpiarValidation() {
      this.$validator.pause();
      this.$nextTick(() => {
        this.$validator.errors.clear();
        this.$validator.fields.items.forEach((field) => field.reset());
        this.$validator.fields.items.forEach((field) =>
          this.errors.remove(field)
        );
        this.$validator.resume();
      });
    },
  },
  created() {},
};
</script>
