<template >
  <div class="centerx">
    <vs-popup
      fullscreen
      close="cancelar"
      title="Venta de Propiedades del Cementerio"
      :active.sync="showVentana"
      button-close-hidden
    >
      <div class="w-full px-2">
        <vs-button @click="acceptAlert()" color="success" size="small" class="float-right">Guardar</vs-button>
        <vs-button @click="cancel()" color="danger" size="small" class="float-right mr-5">Cancelar</vs-button>
      </div>
      <span class="text-sm texto-importante">IMPORTANTE Los campos con (*) son obligatorios.</span>
      <div class="vx-row w-full mt-2">
        <div class="vx-col w-full">
          <div class="flex items-end">
            <feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />
            <span class="leading-none font-medium">Información del titular</span>
          </div>
        </div>
      </div>
      <vs-divider />
      <div class="flex flex-wrap">
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75">
            Nombre completo
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            name="Nombre"
            data-vv-validate-on="blur"
            v-validate="'required'"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del titular"
            v-model="form.nombre"
          />
          <div>
            <span class="text-danger text-sm">{{ errors.first('Nombre') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.nombre">{{errores.nombre[0]}}</span>
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75">
            Domicilio Completo
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            name="DomicilioCompleto"
            data-vv-as="Domicilio Completo"
            data-vv-validate-on="blur"
            v-validate="'required'"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el nombre del usuario"
            v-model="form.domicilio"
          />
          <div>
            <span class="text-danger text-sm">{{ errors.first('DomicilioCompleto') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.domicilio">{{errores.domicilio[0]}}</span>
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75">
            Ciudad
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            name="Ciudad"
            data-vv-validate-on="blur"
            v-validate="'required'"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese la ciudad"
            v-model="form.ciudad"
          />
          <div>
            <span class="text-danger text-sm">{{ errors.first('Ciudad') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.ciudad">{{errores.ciudad[0]}}</span>
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
          <label class="text-sm opacity-75">
            Estado
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            name="Estado"
            data-vv-validate-on="blur"
            v-validate="'required'"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el estado"
            v-model="form.estado"
          />
          <div>
            <span class="text-danger text-sm">{{ errors.first('Estado') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.estado">{{errores.estado[0]}}</span>
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-4/12 px-2">
          <label class="text-sm opacity-75">Tél. Domicilio</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el teléfono del domicilio"
            v-model="form.tel_domicilio"
          />
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-4/12 px-2">
          <label class="text-sm opacity-75">
            Celular
            <span class="text-danger text-sm">(*)</span>
          </label>
          <vs-input
            name="Celular"
            data-vv-validate-on="blur"
            v-validate="'required'"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese un número de celular"
            v-model="form.celular"
          />
          <div>
            <span class="text-danger text-sm">{{ errors.first('Celular') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.celular">{{errores.celular[0]}}</span>
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-4/12 px-2">
          <label class="text-sm opacity-75">Tél. Oficina</label>
          <vs-input
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese un teléfono de oficina"
            v-model="form.tel_oficina"
          />
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-4/12 px-2">
          <label class="text-sm opacity-75">RFC</label>
          <vs-input
            name="rfc"
            data-vv-validate-on="blur"
            type="text"
            class="w-full pb-1 pt-1"
            placeholder="e.j. MELM8305281H0"
            v-model="form.rfc"
            v-validate="'min:12|max:13'"
          />
          <div>
            <span class="text-danger text-sm">{{ errors.first('rfc') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.rfc">{{errores.rfc[0]}}</span>
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-4/12 px-2">
          <label class="text-sm opacity-75">Email</label>
          <vs-input
            type="email"
            class="w-full pb-1 pt-1"
            placeholder="Ingrese el email"
            v-model="form.email"
          />
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-4/12 px-2">
          <label class="text-sm opacity-75">
            Fecha de Nacimiento
            <span class="text-danger text-sm">(*)</span>
          </label>
          <datepicker
            :language="spanishDatepicker"
            :disabled-dates="disabledDates"
            name="FechaNac"
            data-vv-as="Fecha de nacimiento"
            v-validate="'required'"
            format="yyyy-MM-dd"
            placeholder="Seleccionar fecha"
            v-model="form.fecha_nac"
          ></datepicker>
          <div>
            <span class="text-danger text-sm">{{ errors.first('FechaNac') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.fecha_nac">{{errores.fecha_nac[0]}}</span>
          </div>
        </div>
      </div>
      <div class="vx-row w-full mt-4">
        <div class="vx-col w-full">
          <div class="flex items-end">
            <feather-icon icon="ShoppingCartIcon" class="mr-2" svgClasses="w-5 h-5" />
            <span class="leading-none font-medium">Detalle de la venta</span>
          </div>
        </div>
      </div>
      <vs-divider />
      <div class="flex flex-wrap">
        <vs-checkbox class="mb-5" v-model="ventaAntesdelSistema">Venta liquidada antes del sistema</vs-checkbox>
      </div>

      <div class="flex flex-wrap pb-10">
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-4/12 px-2">
          <label class="text-sm opacity-75">
            Fecha de Nacimiento
            <span class="text-danger text-sm">(*)</span>
          </label>
          <datepicker
            :language="spanishDatepicker"
            :disabled-dates="disabledDates"
            name="FechaNac"
            data-vv-as="Fecha de nacimiento"
            v-validate="'required'"
            format="yyyy-MM-dd"
            placeholder="Seleccionar fecha"
            v-model="form.fecha_nac"
          ></datepicker>
          <div>
            <span class="text-danger text-sm">{{ errors.first('FechaNac') }}</span>
          </div>
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.fecha_nac">{{errores.fecha_nac[0]}}</span>
          </div>
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
          <label class="text-sm opacity-75">
            Tipo de Venta
            <span class="text-danger text-sm">(*)</span>
          </label>
          <v-select
            :options="ventasReferencias"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="form.venta_referencia_id"
            class="mb-4 sm:mb-0 pb-1 pt-1"
          />
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.rol_id">{{errores.rol_id[0]}}</span>
          </div>
        </div>
        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
          <label class="text-sm opacity-75">
            Núm. Solicitud
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
            :disabled="(!((uso_futuro)))"
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
            Núm. Convenio
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
            :disabled="(!((uso_futuro*ventaAntesdelSistema)))"
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
            Núm. Título
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
            :disabled="(!(((uso_inmediato+uso_futuro)*(ventaAntesdelSistema))))"
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
            Tipo Propiedad
            <span class="text-danger text-sm">(*)</span>
          </label>
          <v-select
            :options="tipo_propiedades"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="form.tipo_propiedad_id"
            class="mb-4 sm:mb-0 pb-1 pt-1"
          />
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.genero">{{errores.genero[0]}}</span>
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
          <label class="text-sm opacity-75">
            <span v-if="this.form.tipo_propiedad_id.value!=''">
              <span
                v-if="this.form.tipo_propiedad_id.value==1 || this.form.tipo_propiedad_id.value==2"
              >Módulo</span>
              <span v-if="this.form.tipo_propiedad_id.value==3">Columna</span>

              <span v-if="this.form.tipo_propiedad_id.value==4">Terraza</span>
            </span>
            <span v-else>-</span>
            <span class="text-danger text-sm">(*)</span>
          </label>
          <v-select
            :options="propiedades"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="form.propiedad_id"
            class="mb-4 sm:mb-0 pb-1 pt-1"
          />
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.genero">{{errores.genero[0]}}</span>
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
          <label class="text-sm opacity-75">
            Fila
            <span class="text-danger text-sm">(*)</span>
          </label>
          <v-select
            :options="filas"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="form.filas"
            class="mb-4 sm:mb-0 pb-1 pt-1"
          />
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.genero">{{errores.genero[0]}}</span>
          </div>
        </div>

        <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
          <label class="text-sm opacity-75">
            Ubicación
            <span class="text-danger text-sm">(*)</span>
          </label>
          <v-select
            :options="lotes"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="form.lotes"
            class="mb-4 sm:mb-0 pb-1 pt-1"
            :disabled="!cuadriplex"
          />
          <div class="mt-2">
            <span class="text-danger text-sm" v-if="this.errores.genero">{{errores.genero[0]}}</span>
          </div>
        </div>
      </div>
      {{ubicacion}}
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
import Password from "../../../confirmar_password";
import cementerio from "@services/cementerio";
import usuarios from "@services/Usuarios";
import vSelect from "vue-select";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";

/**VARIABLES GLOBALES */
import { alfabeto } from "@/VariablesGlobales";

export default {
  components: {
    "v-select": vSelect,
    Password,
    Datepicker
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
        this.get_ventas_referencias_propiedades();
        this.tipoPropiedades();
      }
    },
    //aqui traigo los datos para segun el tipo de propiedad
    "form.tipo_propiedad_id.value": function(newValue, oldValue) {
      if (newValue != "") {
        //el valor no es nulo y debe buscar las propieades de ese tipo
        this.get_propiedades_by_tipo(newValue);
      } else {
        //reinicio los valores
        this.propiedades = [];
        this.propiedades.push({ label: "Seleccione 1", value: "" });
        this.form.propiedad_id = { label: "Seleccione 1", value: "" };
      }
    },
    //aqui obtengo los datos necesarios para poder saber cuantas filas tiene una propiedad
    "form.propiedad_id.value": function(newValue, oldValue) {
      if (newValue != "") {
        if (this.form.tipo_propiedad_id.value != "") {
          //si el valor de la propiedad es diferente de 1
          //saco los valores para los modulos de uniplex y duplex
          if (this.form.tipo_propiedad_id.value < 3) {
            //filas para uniplex y duplex
            this.propiedades_datos_completos.forEach(element => {
              //buscamos el numero de propiedad que esta seleccionada para poder crear las filas correspondientes
              if (element.id == this.form.propiedad_id.value) {
                this.filas = [];
                this.filas.push({ label: "Seleccione 1", value: "" });
                //al encontrar el numero de filas que les corresponden segun la propiedad creo las filas para que la seleccione el usuario
                for (let index = 1; index <= element.filas; index++) {
                  this.filas.push({
                    label:
                      "Módulo " + this.form.propiedad_id.label + " - " + index,
                    value: index
                  });
                }
                this.form.filas = { label: "Seleccione 1", value: "" };
              }
            });
          }

          //saco los valores para los nichos
          if (this.form.tipo_propiedad_id.value == 3) {
            //filas para uniplex y duplex
            this.propiedades_datos_completos.forEach(element => {
              //buscamos el numero de propiedad que esta seleccionada para poder crear las filas correspondientes
              if (element.id == this.form.propiedad_id.value) {
                this.filas = [];
                this.filas.push({ label: "Seleccione 1", value: "" });
                //al encontrar el numero de filas que les corresponden segun la propiedad creo las filas para que la seleccione el usuario
                for (let index = 1; index <= element.filas; index++) {
                  this.filas.push({
                    label:
                      "Columna " + this.form.propiedad_id.label + " - " + index,
                    value: index
                  });
                }
                this.form.filas = { label: "Seleccione 1", value: "" };
              }
            });
          }

          //saco los valores para las terrazas
          if (this.form.tipo_propiedad_id.value == 4) {
            //filas para uniplex y duplex
            this.propiedades_datos_completos.forEach(element => {
              //buscamos el numero de propiedad que esta seleccionada para poder crear las filas correspondientes
              if (element.id == this.form.propiedad_id.value) {
                this.filas = [];
                this.filas.push({ label: "Seleccione 1", value: "" });
                //al encontrar el numero de filas que les corresponden segun la propiedad creo las filas para que la seleccione el usuario
                for (let index = 1; index <= element.filas; index++) {
                  this.filas.push({
                    label: this.alfabeto[index - 1],
                    value: index
                  });
                }
                this.form.filas = { label: "Seleccione 1", value: "" };
                this.lotes = [];
                this.lotes.push({ label: "Seleccione 1", value: "" });
              }
            });
          }
        }
      } else {
        //limpia los datos a el valor incial
        this.filas = [];
        this.filas.push({ label: "Seleccione 1", value: "" });
        this.form.filas = { label: "Seleccione 1", value: "" };
      }
    },

    //aqui obtengo los datos necesarios para poder saber cuantas filas tiene una propiedad
    "form.filas.value": function(newValue, oldValue) {
      if (newValue != "") {
        if (this.form.tipo_propiedad_id.value == 4) {
          this.propiedades_datos_completos.forEach(element => {
            //buscamos el numero de propiedad que esta seleccionada para poder crear las filas correspondientes
            if (element.id == this.form.propiedad_id.value) {
              //solo cuadriplex
              if (this.form.filas.value != "") {
                //aqui obtengo los datos para poder llamar la funcion que me traera los
                //valores necesarios para poder cargar los lotes que existen segun cada fila
                cementerio
                  .get_columna_fila_terraza(
                    this.form.propiedad_id.value,
                    this.form.filas.value
                  )
                  .then(res => {
                    this.lotes = [];
                    //aqui tengo los valores para saber en que columna empieza y acaba cada fila
                    for (let index = 1; index <= element.columnas; index++) {
                      //aqui se debe crear unicamente los lotes que estan en cada fila segun la terraza
                      if (
                        index >= res.data[0].empieza_columna &&
                        index <= res.data[0].fin_columna
                      ) {
                        this.lotes.push({
                          label: this.form.filas.label + " - " + index,
                          value: index
                        });
                      }
                    }
                    this.form.lotes = { label: "Seleccione 1", value: "" };
                  })
                  .catch(err => {});
              }
            }
          });
        }
      } else {
        this.lotes = [];
        this.lotes.push({ label: "Seleccione 1", value: "" });
        this.form.lotes = { label: "Seleccione 1", value: "" };
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
    uso_inmediato: function() {
      //verifico que tipo de venta es para activar las casillas correspondientes
      if (this.form.venta_referencia_id.value == 1) {
        return true;
      } else {
        return false;
      }
      //return this.ventaAntesdelSistema;
    },
    uso_futuro: function() {
      //verifico que tipo de venta es para activar las casillas correspondientes
      if (this.form.venta_referencia_id.value == 2) {
        return true;
      } else {
        return false;
      }
      //return this.ventaAntesdelSistema;
    },
    cuadriplex: function() {
      //verifico si esl tipo de propiedad es cuadriplex para habilitar el selector de lotes
      if (this.form.tipo_propiedad_id.value == 4) {
        return true;
      } else {
        return false;
      }
    },
    ubicacion: function() {
      //se va creando la ubicacion
      return (
        this.form.tipo_propiedad_id.value +
        "-" +
        this.form.propiedad_id.value +
        "-" +
        this.form.filas.value
      );
    }
  },
  data() {
    return {
      alfabeto: alfabeto,
      disabledDates: {
        from: new Date()
      },
      spanishDatepicker: es,
      ventaAntesdelSistema: false,
      operConfirmar: false,
      callback: Function,
      accionNombre: "agregar nuevo usuario",
      tipo_propiedades: [],
      propiedades_datos_completos: [],
      propiedades: [],
      ventasReferencias: [],
      filas: [],
      lotes: [],
      form: {
        //datos del titular
        nombre: "",
        a_paterno: "",
        a_materno: "",
        domicilio: "",
        ciudad: "",
        estado: "",
        tel_domicilio: "",
        celular: "",
        tel_oficina: "",
        rfc: "",
        email: "",
        fecha_nac: "",
        //
        tipo_venta_id: "",
        num_solicitud: "",
        convenio: "",
        tipo_propiedad_id: {
          label: "Seleccione 1",
          value: ""
        },
        propiedad_id: {
          label: "Seleccione 1",
          value: ""
        },
        venta_referencia_id: {
          label: "Seleccione 1",
          value: ""
        },
        filas: {
          label: "Seleccione 1",
          value: ""
        },
        lotes: {
          label: "Seleccione 1",
          value: ""
        }
      },
      errores: []
    };
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
      this.$emit("closeVentana");
    },
    get_ventas_referencias_propiedades() {
      cementerio
        .get_ventas_referencias_propiedades()
        .then(res => {
          //le agrego todos los roles
          this.ventasReferencias = [];
          this.ventasReferencias.push({ label: "Seleccione 1", value: "" });
          res.data.forEach(element => {
            /**AGREGO LOS DEMAS ROLES */
            this.ventasReferencias.push({
              label: element.tipo_venta,
              value: element.id
            });
          });
        })
        .catch(err => {});
    },

    tipoPropiedades() {
      cementerio
        .tipoPropiedades()
        .then(res => {
          //le agrego todos los roles
          this.tipo_propiedades = [];
          this.tipo_propiedades.push({ label: "Seleccione 1", value: "" });
          res.data.forEach(element => {
            /**AGREGO LOS DEMAS ROLES */
            this.tipo_propiedades.push({
              label: element.tipo,
              value: element.id
            });
          });
        })
        .catch(err => {});
    },

    get_propiedades_by_tipo(id_tipo) {
      cementerio
        .get_propiedades_by_tipo(id_tipo)
        .then(res => {
          //le agrego todos los roles
          this.propiedades = [];
          this.propiedades_datos_completos = [];
          this.propiedades.push({ label: "Seleccione 1", value: "" });
          res.data.forEach(element => {
            /**AGREGO LOS DEMAS ROLES */
            this.propiedades_datos_completos.push(element);
            this.propiedades.push({
              label: element.propiedad_indicador,
              value: element.id
            });
          });
          this.form.propiedad_id = { label: "Seleccione 1", value: "" };
        })
        .catch(err => {});
    },
    closeChecker() {
      this.operConfirmar = false;
    }
  }
};
</script>