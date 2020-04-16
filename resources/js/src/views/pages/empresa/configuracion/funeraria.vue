<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4">
        <vs-card class="cardx card-tarifas" fixedHeight>
          <div slot="header">
            <h3>Información de la Funeraria</h3>
          </div>
          <div class="mt-3">
            <div class="flex flex-wrap">
              <div class="w-full pb-5 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="EditIcon" class="mr-2" svgClasses="w-5 h-5" />Información fiscal de la empresa
                </h3>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Nombre comercial
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="nombre_comercial"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder=" Nombre comercial de la empresa"
                  v-model="form.nombre_comercial"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('nombre_comercial') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.nombre_comercial"
                  >{{errores.nombre_comercial[0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Razón Social
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="razon_social"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder=" Razón social de la empresa"
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
              <div class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2">
                <label class="text-sm opacity-75 font-medium">
                  <span>Régimen Fiscal</span>
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <v-select
                  :options="regimenes"
                  :clearable="false"
                  :dir="$vs.rtl ? 'rtl' : 'ltr'"
                  v-model="form.regimen"
                  class="pb-1 pt-1"
                  v-validate:validacion_regimen.immediate="'required'"
                  name="regimen"
                  data-vv-as=" "
                  :disabled="true"
                >
                  <div slot="no-options">Seleccione 1 Régimen</div>
                </v-select>
                <div>
                  <span class="text-danger text-sm">{{ errors.first('regimen') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.regimen"
                  >{{errores.regimen[0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75">
                  RFC
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="rfc"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="RFC de la empresa"
                  v-model="form.rfc"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('rfc') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.rfc">{{errores.rfc[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">
                  Teléfono
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="telefono"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Teléfono(s)"
                  v-model="form.telefono"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('telefono') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.telefono"
                  >{{errores.telefono[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">
                  Correo Electrónico
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="email"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Correo Electrónico"
                  v-model="form.email"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('email') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.email">{{errores.email[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                <label class="text-sm opacity-75">Fax</label>
                <vs-input
                  name="fax"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Fax"
                  v-model="form.fax"
                />

                <div class="mt-2"></div>
              </div>
            </div>
          </div>
          <vs-divider />
          <div class="mt-3">
            <div class="flex flex-wrap">
              <div class="w-full pb-5 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="MapPinIcon" class="mr-2" svgClasses="w-5 h-5" />Ubicación
                </h3>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Calle
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="calle"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Calle"
                  v-model="form.calle"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('calle') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.calle">{{errores.calle[0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Colonia
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="colonia"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Colonia"
                  v-model="form.colonia"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('colonia') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.colonia"
                  >{{errores.colonia[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75">
                  Núm. Exterior
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="num_ext"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="6"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Núm. Exterior"
                  v-model="form.num_ext"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('num_ext') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.num_ext"
                  >{{errores.num_ext[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75">Núm. Interior</label>
                <vs-input
                  name="num_int"
                  maxlength="6"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Núm. Interior"
                  v-model="form.num_int"
                />

                <div class="mt-2"></div>
              </div>

              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75">
                  C.P
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="cp"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="6"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="C.P"
                  v-model="form.cp"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('cp') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.cp">{{errores.cp[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75 font-medium">
                  <span>Zona Horaria</span>
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <v-select
                  :options="zonas"
                  :clearable="false"
                  :dir="$vs.rtl ? 'rtl' : 'ltr'"
                  v-model="form.zona"
                  class="pb-1 pt-1"
                  v-validate:validacion_zona.immediate="'required'"
                  name="zona_horaria"
                  data-vv-as=" "
                >
                  <div slot="no-options">Seleccione una Zona</div>
                </v-select>
                <div>
                  <span class="text-danger text-sm">{{ errors.first('zona_horaria') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.zona">{{errores.zona[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Ciudad
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="ciudad"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Ciudad"
                  v-model="form.ciudad"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('ciudad') }}</span>
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
                  name="estado"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Estado"
                  v-model="form.estado"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('estado') }}</span>
                </div>
                <div class="mt-2">
                  <span class="text-danger text-sm" v-if="this.errores.estado">{{errores.estado[0]}}</span>
                </div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">Cuenta Bancaria</label>
                <vs-input
                  name="cuenta"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Cuenta Bancaria"
                  v-model="form.cuenta"
                />

                <div class="mt-2"></div>
              </div>

              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">Clabe</label>
                <vs-input
                  name="clabe"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Clabe Interbancaria"
                  v-model="form.clabe"
                />

                <div class="mt-2"></div>
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
export default {
  components: {
    "v-select": vSelect
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
    },
    validacion_regimen: function() {
      return this.form.regimen.value;
    },
    validacion_zona: function() {
      return this.form.zona.value;
    }
  },
  data() {
    return {
      regimenes: [
        {
          label: "Seleccione 1",
          value: ""
        }
      ],
      zonas: [
        { label: "Seleccione 1", value: "" },
        {
          label: "Mazatlán",
          value: "America/Mazatlan"
        },
        {
          label: "Ciudad de México",
          value: "America/Mexico_City"
        }
      ],
      form: {
        modulo: "funeraria",
        regimen: {
          label: "Seleccione 1",
          value: ""
        },
        nombre_comercial: "",
        razon_social: "",
        rfc: "",
        telefono: "",
        email: "",
        fax: "",
        cuenta: "",
        clabe: "",
        calle: "",
        colonia: "",
        num_ext: "",
        num_int: "",
        cp: "",
        zona: {
          label: "Mazatlán",
          value: "America/Mazatlan"
        },

        ciudad: "",
        estado: ""
      }
    };
  },
  methods: {
    //llena los regimentes
    get_regimenes() {
      empresa
        .get_regimenes()
        .then(res => {
          //le agrego todos los usuarios vendedores
          this.regimenes = [];
          this.regimenes.push({ label: "Seleccione 1", value: "" });
          res.data.forEach(element => {
            this.regimenes.push({
              label: element.regimen,
              value: element.id
            });
          });

          //selecciono efectivo por default
        })
        .catch(err => {});
    },

    mostrarDatos() {
      //lleno los datos mandados dle parent
      this.form.nombre_comercial = this.getDatos.nombre_comercial;
      this.form.razon_social = this.getDatos.razon_social;
      this.form.regimen = {
        label: this.getDatos.regimen.regimen,
        value: this.getDatos.regimen.id
      };
      this.form.rfc = this.getDatos.rfc;
      this.form.telefono = this.getDatos.telefono;
      this.form.email = this.getDatos.email;
      this.form.fax = this.getDatos.fax;
      this.form.cuenta = this.getDatos.cuenta;
      this.form.clabe = this.getDatos.clabe;
      this.form.calle = this.getDatos.calle;
      this.form.colonia = this.getDatos.colonia;
      this.form.num_ext = this.getDatos.num_ext;
      this.form.num_int = this.getDatos.num_int;

      //definiendo la zona horaria
      if (this.getDatos.zona_horaria === "America/Mazatlan") {
        this.form.zona = {
          label: "Mazatlán",
          value: "America/Mazatlan"
        };
      } else {
        //es la de mexico
        this.form.zona = {
          label: "Ciudad de México",
          value: "America/Mexico_City"
        };
      }
      this.form.cp = this.getDatos.cp;
      this.form.ciudad = this.getDatos.ciudad;
      this.form.estado = this.getDatos.estado;
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
  created() {
    this.get_regimenes();
  }
};
</script>
