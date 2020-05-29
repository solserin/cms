<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4">
        <vs-card class="cardx card-tarifas" fixedHeight>
          <div slot="header">
            <h3>Información Fiscal</h3>
          </div>
          <div class="mt-3">
            <div class="flex flex-wrap">
              <div class="w-full pb-5 px-2">
                <h3 class="text-xl">
                  <feather-icon icon="EditIcon" class="mr-2" svgClasses="w-5 h-5" />Firma Electrónica
                </h3>
              </div>
              <div class="w-full mt-5 px-2 text-center">
                <span
                  v-if="this.certificado_llave_capturados"
                  class="pr-3 pl-3 text-white bg-success uppercase"
                >el sistema ya tiene registrado una firma electrónica, si desea actualizar los archivos súbalos por favor.</span>
              </div>
              <div class="w-full mt-5 px-2 text-center">
                <span
                  v-if="this.certificado_llave_validos"
                  class="pr-3 pl-3 text-white bg-danger"
                >Seleccione certificado digital (.cer) y llave privada (.key) que desea actualizar</span>
                <span
                  v-else
                  class="pr-3 pl-3 text-white bg-success uppercase"
                >Certificado (.cer) válido hasta {{fecha_validez}}</span>
              </div>
              <div class="w-full my-10">
                <div class="flex flex-wrap firma-digital">
                  <div
                    class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2 text-center ml-auto"
                  >
                    <label class="text-sm opacity-75 font-semibold">
                      Certificado Digital (.cer)
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-upload
                      accept=".cer"
                      single-upload
                      ref="uploadCer"
                      text="Certificado(.cer)"
                      limit="1"
                      :headers="requestHeaders"
                      fileName="certificate"
                      automatic
                      action="/empresa/facturacion/validateCER"
                      @on-success="successCertificate"
                      @on-error="errorCertificate"
                      @on-delete="clearCertificate"
                    />
                    <span
                      class="text-danger text-sm"
                      v-show="certificateError"
                    >No se ha subido archivo de certificado</span>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.nombre_comercial"
                      >{{errores.nombre_comercial[0]}}</span>
                    </div>
                  </div>
                  <div
                    class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2 text-center mr-auto"
                  >
                    <label class="text-sm opacity-75 font-semibold">
                      Llave Privada (.key)
                      <span class="text-danger text-sm">(*)</span>
                    </label>
                    <vs-upload
                      accept=".key"
                      single-upload
                      ref="uploadKey"
                      text="Llave Privada (.key)"
                      limit="1"
                      :headers="requestHeaders"
                      fileName="key"
                      automatic
                      action="/empresa/facturacion/validateKEY"
                      @on-success="successKey"
                      @on-error="errorKey"
                      @on-delete="clearKey"
                    />
                  </div>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
                <label class="text-sm opacity-75">
                  Contraseña
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  name="password"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="35"
                  type="password"
                  class="w-full pb-1 pt-1"
                  placeholder="Contraseña"
                  v-model="form.password"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('password') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.password"
                  >{{errores.password[0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <label class="text-sm opacity-75">
                  Repetir Contraseña
                  <span class="text-danger text-sm">(*)</span>
                </label>
                <vs-input
                  type="password"
                  name="passwordRepetir"
                  data-vv-as=" "
                  v-validate="'required'"
                  maxlength="35"
                  class="w-full pb-1 pt-1"
                  placeholder="Repetir Contraseña"
                  v-model="form.passwordRepetir"
                />
                <div>
                  <span class="text-danger text-sm">{{ errors.first('passwordRepetir') }}</span>
                </div>
                <div class="mt-2">
                  <span
                    class="text-danger text-sm"
                    v-if="this.errores.passwordRepetir"
                  >{{errores.passwordRepetir[0]}}</span>
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
                  Aquí puedes modificar la información necesaria para poder emitir facturas electrónicas.
                </p>
              </div>
              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2">
                <vs-button
                  size="small"
                  class="ml-auto"
                  color="success"
                  icon="add_circle_outline"
                  @click="mandarModificar()"
                >Actualizar Firma Electrónica</vs-button>
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
import { tr } from "date-fns/locale";
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
    certificado_llave_capturados: function() {
      if (this.form.cerPath != null && this.form.keyPath != null) {
        return true;
      } else {
        return false;
      }
    },

    certificado_llave_validos: function() {
      if (this.form.certificateFile != null && this.form.keyFile != null) {
        return false;
      } else {
        return true;
      }
    },
    //decide si es posbile mandar el form  para actualizar
    validar_certificado_actualizar: function() {
      if (!this.certificado_llave_capturados) {
        if (!this.certificado_llave_validos) {
          return true;
        } else return false;
      } else return true;
    },
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
      requestHeaders: {
        Authorization: "Bearer " + localStorage.getItem("accessToken")
      },
      keyError: false,
      certificateError: false,
      fecha_validez: "",
      form: {
        cerPath: "",
        keyPath: "",
        modulo: "facturacion",
        password: "",
        passwordRepetir: "",
        cer: "",
        key: "",
        certificateFile: null,
        keyFile: null
      }
    };
  },
  methods: {
    mostrarDatos() {
      //lleno los datos mandados del parent
      if (this.getDatos.facturacion["cerfile"] !== null) {
        this.form.password = "nochanges";
        this.form.passwordRepetir = "nochanges";
      }

      this.form.cerPath = this.getDatos.facturacion["cerfile"];
      this.form.keyPath = this.getDatos.facturacion["keyfile"];
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
            if (!this.validar_certificado_actualizar) {
              this.$vs.notify({
                title: "Actualizar Datos",
                text: "Error, capture certificado y llave privada.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 4000
              });
              return;
            }
            let formData = new FormData();
            formData.append("modulo", this.form.modulo);
            formData.append("certificateFile", this.form.certificateFile);
            formData.append("keyFile", this.form.keyFile);
            formData.append("password", this.form.password);
            formData.append("passwordRepetir", this.form.passwordRepetir);
            this.$emit("actualizar", formData);
          }
        })
        .catch(() => {});
    },

    successCertificate(event) {
      //obteniendo las fechas de valiz del certificado
      let res = JSON.parse(event.target.response);
      this.form.certificateFile = this.$refs.uploadCer.filesx[
        this.$refs.uploadCer.filesx.length - 1
      ];
      this.fecha_validez = res.fecha_validez;
      this.certificateError = false;
    },
    errorCertificate(event) {
      this.$refs.uploadCer.srcs[
        this.$refs.uploadCer.srcs.length - 1
      ].error = true;
      let data = JSON.parse(event.target.response);
      this.$vs.notify({
        position: "top-center",
        color: "danger",
        title: "Firma Electrónica",
        text: data.error.message
      });
      this.clearCertificate();
    },
    clearCertificate() {
      this.form.certificateFile = null;
    },

    successKey(event) {
      let res = JSON.parse(event.target.response);
      this.form.keyFile = this.$refs.uploadKey.filesx[
        this.$refs.uploadKey.filesx.length - 1
      ];
      this.certificateError = false;
    },
    errorKey(event) {
      this.$refs.uploadKey.srcs[
        this.$refs.uploadKey.srcs.length - 1
      ].error = true;
      let data = JSON.parse(event.target.response);
      this.$vs.notify({
        position: "top-center",
        color: "danger",
        title: "Firma Electrónica",
        text: data.error.message
      });
      this.clearKey();
    },
    clearKey() {
      this.form.keyFile = null;
    }
  },
  created() {}
};
</script>
