<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4">
        <vs-card class="cardx card-tarifas" fixedHeight>
          <div slot="header">
            <h3>Información del Cementerio</h3>
          </div>
          <div class="mt-3">
            <div class="flex flex-wrap">
              <div class="w-full pb-5 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="EditIcon" class="mr-2" svgClasses="w-5 h-5" />Información de contacto
                </h3>
              </div>
              <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2">
                <label class="text-sm opacity-75">
                  Nombre del Cementerio
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="cementerio"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="75"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Nombre del Cementerio"
                  v-model="form.cementerio"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('cementerio') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.cementerio"
                  >{{errores.cementerio[0]}}</span>
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

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
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

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
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

              <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
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
export default {
  components: {},
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
      form: {
        modulo: "cementerio",
        cementerio: "",
        telefono: "",
        email: "",
        fax: "",
        calle: "",
        colonia: "",
        num_ext: "",
        num_int: "",
        cp: "",
        ciudad: "",
        estado: ""
      }
    };
  },
  methods: {
    mostrarDatos() {
      //lleno los datos mandados dle parent
      this.form.cementerio = this.getDatos.cementerio.cementerio;
      this.form.telefono = this.getDatos.cementerio.telefono;
      this.form.email = this.getDatos.cementerio.email;
      this.form.fax = this.getDatos.cementerio.fax;
      this.form.calle = this.getDatos.cementerio.calle;
      this.form.colonia = this.getDatos.cementerio.colonia;
      this.form.num_ext = this.getDatos.cementerio.num_ext;
      this.form.num_int = this.getDatos.cementerio.num_int;
      this.form.cp = this.getDatos.cementerio.cp;
      this.form.ciudad = this.getDatos.cementerio.ciudad;
      this.form.estado = this.getDatos.cementerio.estado;
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
