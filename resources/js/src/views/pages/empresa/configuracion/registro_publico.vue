<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4">
        <vs-card class="cardx card-tarifas" fixedHeight>
          <div slot="header">
            <h3>Información del Registro Público</h3>
          </div>
          <div class="mt-3">
            <div class="flex flex-wrap">
              <div class="w-full pb-5 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="EditIcon" class="mr-2" svgClasses="w-5 h-5" />Escritura Pública
                </h3>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Representante Legal
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="rep_legal"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Representante Legal"
                  v-model="form.rep_legal"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('rep_legal') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.rep_legal"
                  >{{errores.rep_legal[0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Núm. del testimonio
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="t_nep"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Núm. del testimonio"
                  v-model="form.t_nep"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('t_nep') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.t_nep">{{errores.t_nep[0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Fecha del Testimonio
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <datepicker
                  :language="spanishDatepicker"
                  :disabled-dates="disabledDates"
                  name="fecha_tnep"
                  data-vv-as=" "
                  v-validate="'required'"
                  format="yyyy-MM-dd"
                  placeholder="Fecha del Testimonio"
                  v-model="form.fecha_tnep"
                  class="w-full pb-1 pt-1"
                ></datepicker>
                <div>
                  <span class="text-danger text-sm">{{ errors.first('fecha_tnep') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.fecha_tnep"
                  >{{errores.fecha_tnep[0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Otorgada ente la fé del Lic.
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="fe_lic"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Otorgada ente la fé del Lic."
                  v-model="form.fe_lic"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('fe_lic') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.fe_lic">{{errores.fe_lic[0]}}</span>
                </div>
              </div>
            </div>
          </div>
          <vs-divider />

          <div class="mt-3">
            <div class="flex flex-wrap">
              <div class="w-full pb-5 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="EditIcon" class="mr-2" svgClasses="w-5 h-5" />Notario Público
                </h3>
              </div>
              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">
                  Notario Público Núm.
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="num_np"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Notario Público Núm."
                  v-model="form.num_np"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('num_np') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.num_np">{{errores.num_np[0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">
                  Estado
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="estado_np"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Estado"
                  v-model="form.estado_np"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('estado_np') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.estado_np"
                  >{{errores.estado_np[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">
                  Ciudad
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="ciudad_np"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ciudad"
                  v-model="form.ciudad_np"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('ciudad_np') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.ciudad_np"
                  >{{errores.ciudad_np[0]}}</span>
                </div>
              </div>
            </div>
          </div>
          <vs-divider />

          <div class="mt-3">
            <div class="flex flex-wrap">
              <div class="w-full pb-5 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="EditIcon" class="mr-2" svgClasses="w-5 h-5" />Registro Público del Comercio
                </h3>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Núm. Registro Público
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="num_rpc"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Núm. Registro Público"
                  v-model="form.num_rpc"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('num_rpc') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.num_rpc"
                  >{{errores.num_rpc[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Fecha del Registro Público
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <datepicker
                  :language="spanishDatepicker"
                  :disabled-dates="disabledDates"
                  name="fecha_rpc"
                  data-vv-as=" "
                  v-validate="'required'"
                  format="yyyy-MM-dd"
                  placeholder="Fecha del Registro Público"
                  v-model="form.fecha_rpc"
                  class="w-full pb-1 pt-1"
                ></datepicker>
                <div>
                  <span class="text-danger text-sm">{{ errors.first('fecha_rpc') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.fecha_rpc"
                  >{{errores.fecha_rpc[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Estado
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="estado_rpc"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Estado"
                  v-model="form.estado_rpc"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('estado_rpc') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.estado_rpc"
                  >{{errores.estado_rpc[0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Ciudad
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="ciudad_rpc"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ciudad"
                  v-model="form.ciudad_rpc"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('ciudad_rpc') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.ciudad_rpc"
                  >{{errores.ciudad_rpc[0]}}</span>
                </div>
              </div>
            </div>
          </div>
          <vs-divider />

          <div>
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2">
                <p class="text-sm">
                  <span class="text-danger font-medium">Ojo:</span>
                  Desde este apartado puedes modificar la informacion que aparece en los formatos y reportes de la empresa. Presta atención a los datos que pueden causr conflictos fiscales ya que son tomados en cuenta a la hora de facturar.
                </p>
              </div>
              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <vs-button
                  size="small"
                  class="ml-auto"
                  color="success"
                  icon="add_circle_outline"
                  @click="mandarModificar()"
                >Actualizar Datos</vs-button>
              </div>
            </div>
          </div>
        </vs-card>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from "vue-select";
import empresa from "@services/empresa";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";
export default {
  components: {
    "v-select": vSelect,
    Datepicker
  },
  props: {
    datos: {
      type: Object,
      required: true,
      default: {}
    },
    erroresForm: {
      type: Object,
      required: true,
      default: {}
    }
  },
  watch: {
    datos: function(newValue, oldValue) {
      this.mostrarDatos();
    }
  },
  computed: {
    getDatos: {
      get() {
        return this.datos;
      },
      set(newValue) {
        return newValue;
      }
    },
    errores: {
      get() {
        return this.erroresForm;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  data() {
    return {
      disabledDates: {
        from: new Date()
      },
      spanishDatepicker: es,

      form: {
        modulo: "registro_publico",
        rep_legal: "",
        t_nep: "",
        fecha_tnep: "",
        fe_lic: "",
        //datos del notario publico
        num_np: "",
        ciudad_np: "",
        estado_np: "",
        //datos del reg publico de comercio
        num_rpc: "",
        fecha_rpc: "",
        ciudad_rpc: "",
        estado_rpc: ""
      }
    };
  },
  methods: {
    mostrarDatos() {
      //lleno los datos mandados dle parent
      this.form.rep_legal = this.getDatos.registro_publico.rep_legal;
      this.form.t_nep = this.getDatos.registro_publico.t_nep;
      this.form.fecha_tnep = this.getDatos.registro_publico.fecha_tnep;
      this.form.fe_lic = this.getDatos.registro_publico.fe_lic;
      this.form.num_np = this.getDatos.registro_publico.num_np;
      this.form.ciudad_np = this.getDatos.registro_publico.ciudad_np;
      this.form.estado_np = this.getDatos.registro_publico.estado_np;
      this.form.num_rpc = this.getDatos.registro_publico.num_rpc;
      this.form.fecha_rpc = this.getDatos.registro_publico.fecha_rpc;
      this.form.ciudad_rpc = this.getDatos.registro_publico.ciudad_rpc;
      this.form.estado_rpc = this.getDatos.registro_publico.estado_rpc;
    },

    mandarModificar() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$vs.notify({
              title: "Actualizar Datos",
              text: "Error, verifique que ingresó todos los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 4000
            });
            return;
          } else {
            //se manda el emit al parent para guardar datps
            this.$emit("actualizar", this.form);
          }
        })
        .catch(() => {});
    }
  },
  created() {}
};
</script>
