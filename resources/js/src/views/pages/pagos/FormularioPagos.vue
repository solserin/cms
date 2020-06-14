<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups-pagos pagos_forms normal-forms"
      close="cancelar"
      :title="title"
      :active.sync="showVentana"
      ref="formulario"
    >
      <div class="flex flex-wrap px-2">
        <!--datos del titular-->
        <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
          <label class="text-base opacity-75 font-medium">
            Referencia de Pago
            <span class="text-danger text-sm" hidden>(*)</span>
          </label>
          <img
            width="40"
            class="img-center float-left mt-6 mr-1"
            src="@assets/images/reference.svg"
          />
          <vs-input
            ref="descripcion"
            name="descripcion"
            data-vv-as=" "
            data-vv-validate-on="blur"
            v-validate="'required'"
            maxlength="85"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ej. Pago de Contado"
            v-model="form.descripcion"
          />
          <div>
            <span class="text-danger text-sm">{{
              errors.first("descripcion")
            }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.descripcion">{{
              errores.descripcion[0]
            }}</span>
          </div>
        </div>
        <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
          <label class="text-base opacity-75 font-medium">
            Fecha y Hora del Pago
            <span class="text-danger text-sm" hidden>(*)</span>
          </label>
          <img
            width="32"
            class="img-center float-left mt-8 mr-1"
            src="@assets/images/calendar.svg"
          />
          <vs-input
            ref="descripcion"
            name="descripcion"
            data-vv-as=" "
            data-vv-validate-on="blur"
            v-validate="'required'"
            maxlength="85"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ej. Pago de Contado"
            v-model="form.descripcion"
          />
          <div>
            <span class="text-danger text-sm">{{
              errors.first("descripcion")
            }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.descripcion">{{
              errores.descripcion[0]
            }}</span>
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
          <label class="text-base opacity-75 font-medium">
            Calcular Adeudo
          </label>
          <div>
            <img
              width="25px"
              class="cursor-pointer"
              src="@assets/images/calculator.svg"
            />
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-1/12 lg:w-1/12 xl:w-1/12 px-2">
          <label class="text-base opacity-75 font-medium">
            Multipago
          </label>
          <div>
            <img
              width="25px"
              class="cursor-pointer"
              src="@assets/images/save.svg"
            />
          </div>
        </div>

        <vs-divider />
      </div>

      <div class="flex flex-wrap px-2">
        <div class="w-full px-2">
          <div class="mt-2">
            <p class="text-center">
              <span class="text-danger font-medium">Ojo:</span>
              Por favor revise la información ingresada, si todo es correcto de
              click en "Botón de Abajo”.
            </p>
          </div>
        </div>
      </div>
      <div
        class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 pt-6 pb-10 px-2 mr-auto ml-auto"
      >
        <vs-button
          class="w-full"
          @click="acceptAlert()"
          color="success"
          size="small"
        >
          <img
            width="25px"
            class="cursor-pointer"
            src="@assets/images/save.svg"
          />
          <span class="texto-btn" v-if="this.getTipoformulario == 'agregar'"
            >Guardar Pago</span
          >
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
      :accion="'He revisado la información y quiero registrar este precio'"
      :confirmarButton="'Registrar Precio'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import cementerio from "@services/cementerio";
import vSelect from "vue-select";

import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
/**VARIABLES GLOBALES */

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
    id_pago: {
      type: Number,
      required: false,
      default: 0
    },
    referencias_pagos: {
      type: String,
      required: false,
      default: ""
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == true) {
        //cargo nacionalidades
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() =>
          this.$refs["descripcion"].$el.querySelector("input").focus()
        );

        this.form.contado_b = {
          value: 1,
          label: "Pago de Contado/Uso Inmediato"
        };

        this.form.descuento_pronto_pago_b = {
          value: "0",
          label: "No"
        };

        (async () => {
          /**de manera asincrona para evitar errores en popular los selects */
          /**cargando los tipos de propeidades */

          if (this.getTipoformulario == "cancelar") {
            this.title = "Cancelación de pagos";
            /**se cargan los datos al formulario */
          } else {
            this.title = "registro de cobranza";
          }
        })();
      }
    },
    "form.contado_b": function(newValue, oldValue) {
      if (newValue.value == 1) {
        this.form.financiamiento = 1;
      } else {
        if (this.getTipoformulario == "modificar") {
          /**se restablece el precio que estaba capturado */
          this.form.financiamiento = this.datosPrecio.financiamiento;
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
    get_precio_id: {
      get() {
        return this.id_precio;
      },
      set(newValue) {
        return newValue;
      }
    },
    tipo_propiedad_computed: function() {
      return this.form.tipo_propiedades_id.value;
    },
    es_contado: function() {
      if (this.form.contado_b.value == 1) {
        return true;
      } else {
        return false;
      }
    },
    aplica_descuento: function() {
      if (this.form.descuento_pronto_pago_b.value == 1) {
        return false;
      } else {
        return true;
      }
    }
  },
  data() {
    return {
      datosPrecio: [],
      title: "",
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      operConfirmar: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Modificar Precio",

      referencia: "",
      form: {
        pagos_cubiertos: [],
        id_pago: 0,
        /**datos */
        nota: "",
        total: 0,
        banco: "",
        referencia: "",
        pago_con_cantidad: 0,
        cambio_pago: 0,
        fecha_pago: "",
        cobrador: ""
      },
      errores: []
    };
  },
  methods: {
    async get_tipo_propiedades() {
      this.$vs.loading();
      try {
        let res = await cementerio.get_tipo_propiedades();
        /**llenando los tipos de propiedad para el select */
        this.tipos_propiedad = [];
        this.tipos_propiedad.push({
          value: "",
          label: "Seleccione 1"
        });
        res.data.forEach(element => {
          this.tipos_propiedad.push({
            label: "Tipo " + element.tipo,
            value: element.id
          });
          //la primero opcion
          this.form.tipo_propiedades_id = this.tipos_propiedad[0];
        });
        this.$vs.loading.close();
      } catch (error) {
        this.$vs.loading.close();
      }
    },
    /**trae la info del precio */
    async get_precio_by_id() {
      this.$vs.loading();
      try {
        let res = await cementerio.get_precio_by_id(this.get_precio_id);
        this.datosPrecio = res.data;
        this.form.descripcion = this.datosPrecio.descripcion;
        this.form.descripcion_ingles = this.datosPrecio.descripcion_ingles;
        //actualizo los datos en el formulario
        this.financiamientos.forEach(element => {
          if (element.value == this.datosPrecio.contado_b) {
            this.form.contado_b = element;
            this.form.financiamiento = this.datosPrecio.financiamiento;
            return;
          }
        });
        this.tipos_propiedad.forEach(element => {
          if (element.value == this.datosPrecio.tipo_propiedades_id) {
            this.form.tipo_propiedades_id = element;
            return;
          }
        });
        this.form.costo_neto = this.datosPrecio.costo_neto;
        this.form.pago_inicial = this.datosPrecio.pago_inicial;
        this.form.costo_neto_financiamiento_normal = this.datosPrecio.costo_neto_financiamiento_normal;

        this.descuento.forEach(element => {
          if (element.value == this.datosPrecio.descuento_pronto_pago_b) {
            this.form.descuento_pronto_pago_b = element;
            return;
          }
        });
        this.form.costo_neto_pronto_pago =
          this.datosPrecio.descuento_pronto_pago_b == 1
            ? this.datosPrecio.costo_neto_pronto_pago
            : "";

        this.$vs.loading.close();
      } catch (error) {
        this.$vs.loading.close();
        this.$vs.notify({
          title: "Modificar Precios",
          text: "Ocurrió un error al traer la informacion, reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "4000"
        });
        this.cerrarVentana();
      }
    },
    acceptAlert() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$vs.notify({
              title: "Registro de Precios",
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
              this.callBackConfirmarAceptar = this.registrar_precio_propiedad;
              this.openConfirmarAceptar = true;
            } else {
              /**modificar, se valida con password */
              this.form.id_precio_modificar = this.get_precio_id;
              this.callback = this.update_precio_propiedad;
              this.operConfirmar = true;
            }
          }
        })
        .catch(() => {});
    },

    registrar_precio_propiedad() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      cementerio
        .registrar_precio_propiedad(this.form)
        .then(res => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Registro de Precios",
              text: "Se ha guardado el precio correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000
            });
            this.$emit("retornar_id", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Registro de Precios",
              text: "Error al guardar el precio, por favor reintente.",
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
                title: "Registro de Precios",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000
              });
              //console.log(err.response);
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Registro de Precios",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000
              });
            }
          }
          this.$vs.loading.close();
        });
    },

    update_precio_propiedad() {
      //aqui mando modoificar los datos
      this.errores = [];
      this.$vs.loading();
      cementerio
        .update_precio_propiedad(this.form)
        .then(res => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Modificación de Precios",
              text: "Se modificó el precio correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000
            });
            this.$emit("retornar_id", res.data);
            this.cerrarVentana();
          } else {
            this.$vs.notify({
              title: "Modificación de Precios",
              text: "Error al guardar el precio, por favor reintente.",
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
            console.log(
              "update_precio_propiedad -> err.response",
              err.response
            );
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
                title: "Modificación de Precios",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000
              });
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Modificación de Precios",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000
              });
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
      /**en caso de modificar */
      this.form.id_precio_modificar = 0;
      /**datos */
      this.form.descripcion = "";
      this.form.descripcion_ingles = "";
      this.form.contado_b = {};
      this.form.financiamiento = "";
      this.form.pago_inicial = "";
      this.form.costo_neto = "";
      this.form.costo_neto_financiamiento_normal = "";
      this.form.descuento_pronto_pago_b = {};
      this.form.costo_neto_pronto_pago = "";
      this.form.tipo_propiedades_id = { value: "", label: "Seleccione 1" };
      this.errores = [];
    },

    closeChecker() {
      this.operConfirmar = false;
    }
  },
  created() {}
};
</script>