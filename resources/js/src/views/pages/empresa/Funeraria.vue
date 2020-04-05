<template>
  <div class="mt-4 pb-3">
    <form>
      <div class="vx-row w-full">
        <div class="vx-col w-full">
          <div class="flex items-end">
            <feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />
            <span class="leading-none font-medium">Informacion de la funeraria</span>
          </div>
        </div>
      </div>
      <vs-divider />
      <div class="vx-row">
        <div class="vx-col w-full md:w-3/12 mt-3">
          <label for class="vs-input--label">
            Nombre comercial
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <vs-input
            class="w-full uppercase"
            icon-pack="feather"
            icon="icon-user"
            data-vv-as
            v-model="funeraria.nombre_comercial"
            v-validate="'required'"
            name="nombre_comercial"
          />
          <span
            class="text-danger text-sm"
            v-show="errors.has('nombre_comercial')"
          >{{ errors.first('nombre_comercial') }}</span>
        </div>
        <div class="vx-col w-full md:w-3/12 mt-3">
          <label for class="vs-input--label">
            Razon social
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <vs-input
            class="w-full uppercase"
            data-vv-as="Razon social"
            icon-pack="feather"
            icon="icon-triangle"
            v-model="funeraria.razon_social"
            v-validate="'required'"
            name="razon_social"
          />
          <span
            class="text-danger text-sm"
            v-show="errors.has('razon_social')"
          >{{ errors.first('razon_social') }}</span>
        </div>
        <div class="vx-col w-full md:w-2/12 mt-3">
          <label for class="vs-input--label">
            RFC
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <vs-input
            class="w-full uppercase"
            placeholder="e.j. MELM8305281H0"
            data-vv-as="RFC"
            icon-pack="feather"
            icon="icon-file-text"
            v-model="funeraria.rfc"
            v-validate="'required|min:12|max:12'"
            name="rfc"
          />
          <span class="text-danger text-sm" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</span>
        </div>
        <div class="vx-col w-full md:w-4/12 mt-3">
          <label for class="vs-input--label">
            Regimen fiscal
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <v-select
            v-model="selectedRegimen"
            :clearable="false"
            name="regimen"
            data-vv-as="Regimen fiscal"
            v-validate="'required'"
            placeholder="Seleccione un regimen fiscal"
            :options="regimenes"
          >
            <div slot="no-options">No hay opciones disponibles.</div>
          </v-select>
          <span
            class="text-danger text-sm"
            v-show="errors.has('regimen')"
          >{{ errors.first('regimen') }}</span>
        </div>
      </div>
      <div class="vx-row">
        <div class="vx-col w-full md:w-3/12 mt-3">
          <label for class="vs-input--label">
            Telefono
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <vs-input
            class="w-full uppercase"
            data-vv-as="Telefono"
            icon-pack="feather"
            icon="icon-phone"
            v-model="funeraria.telefono"
            v-validate="'required'"
            name="telefono"
          />
          <span
            class="text-danger text-sm"
            v-show="errors.has('telefono')"
          >{{ errors.first('telefono') }}</span>
        </div>

        <div class="vx-col w-full md:w-2/12 mt-3">
          <vs-input
            class="w-full uppercase"
            data-vv-as="Extension"
            icon-pack="feather"
            icon="icon-plus"
            v-model="funeraria.ext"
            label="Ext:"
            name="ext"
          />
          <!--<span class="text-danger text-sm" v-show="errors.has('ext')">{{ errors.first('ext') }}</span>-->
        </div>
        <div class="vx-col w-full md:w-3/12 mt-3">
          <vs-input
            class="w-full uppercase"
            data-vv-as="Fax"
            icon-pack="feather"
            icon="icon-file"
            v-model="funeraria.fax"
            label="Fax:"
            name="fax"
          />
          <!--<span class="text-danger text-sm" v-show="errors.has('fax')">{{ errors.first('fax') }}</span>-->
        </div>
        <div class="vx-col w-full md:w-4/12 mt-3">
          <label for class="vs-input--label">
            Correo
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <vs-input
            data-vv-as="Correo"
            icon-pack="feather"
            icon="icon-mail"
            :bails="false"
            v-model="funeraria.email"
            class="w-full"
            type="email"
            v-validate="'required|email'"
            name="email"
          />
          <span class="text-danger text-sm" v-show="errors.has('email')">{{ errors.first('email') }}</span>
        </div>
      </div>
      <div class="vx-row w-full mt-8">
        <div class="vx-col w-full">
          <div class="flex items-end">
            <feather-icon icon="MapPinIcon" class="mr-2" svgClasses="w-5 h-5" />
            <span class="leading-none font-medium">Direccion</span>
          </div>
        </div>
      </div>
      <vs-divider />
      <div class="vx-row">
        <div class="vx-col w-full md:w-1/3 mt-3">
          <label for class="vs-input--label">
            Estado
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <v-select
            v-model="selectedEstado.funeraria"
            :clearable="false"
            name="estado"
            data-vv-as="Estado"
            v-validate="'required'"
            placeholder="Seleccione un estado"
            @input="estadoChange('funeraria')"
            :options="estados"
          >
            <div slot="no-options">No hay opciones disponibles.</div>
          </v-select>
          <span
            class="text-danger text-sm"
            v-show="errors.has('estado')"
          >{{ errors.first('estado') }}</span>
        </div>
        <div class="vx-col w-full md:w-1/3 mt-3">
          <label for class="vs-input--label">
            Municipio
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <v-select
            v-model="selectedMunicipio.funeraria"
            :clearable="false"
            name="municipio"
            data-vv-as="Municipio"
            v-validate="'required'"
            placeholder="Seleccione un municipio"
            @input="municipioChange('funeraria')"
            :options="municipios.funeraria"
          >
            <div slot="no-options">No hay opciones disponibles.</div>
          </v-select>
          <span
            class="text-danger text-sm"
            v-show="errors.has('municipio')"
          >{{ errors.first('municipio') }}</span>
        </div>
        <div class="vx-col w-full md:w-1/3 mt-3">
          <label for class="vs-input--label">
            Localidad(Ciudad)
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <v-select
            v-model="selectedLocalidad.funeraria"
            :clearable="false"
            name="localidad"
            data-vv-as="Localidad"
            v-validate="'required'"
            placeholder="Seleccione una localidad"
            :options="localidades.funeraria"
          >
            <div slot="no-options">No hay opciones disponibles.</div>
          </v-select>
          <span
            class="text-danger text-sm"
            v-show="errors.has('localidad')"
          >{{ errors.first('localidad') }}</span>
        </div>
      </div>
      <div class="vx-row">
        <div class="vx-col w-full md:w-6/12 mt-3">
          <label for class="vs-input--label">
            Calle
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <vs-input
            name="calle"
            icon-pack="feather"
            icon="icon-map"
            data-vv-as="Calle"
            v-validate="'required'"
            v-model="funeraria.calle"
            class="w-full uppercase"
          />
          <span class="text-danger text-sm" v-show="errors.has('calle')">{{ errors.first('calle') }}</span>
        </div>
        <div class="vx-col w-full md:w-3/12 mt-3">
          <label for class="vs-input--label">
            Numero Ext.
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <vs-input
            name="num_ext"
            icon-pack="feather"
            icon="icon-map"
            data-vv-as="Numero exterior"
            v-validate="'required'"
            v-model="funeraria.num_ext"
            class="w-full uppercase"
          />
          <span
            class="text-danger text-sm"
            v-show="errors.has('num_ext')"
          >{{ errors.first('num_ext') }}</span>
        </div>
        <div class="vx-col w-full md:w-3/12 mt-3">
          <label for class="vs-input--label">
            Numero Int.
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <vs-input
            name="num_int"
            icon-pack="feather"
            icon="icon-map"
            data-vv-as="Numero interior"
            v-model="funeraria.num_int"
            class="w-full uppercase"
          />
          <span
            class="text-danger text-sm"
            v-show="errors.has('num_int')"
          >{{ errors.first('num_int') }}</span>
        </div>
      </div>
      <div class="vx-row">
        <div class="vx-col w-full md:w-6/12 mt-3">
          <label for class="vs-input--label">
            Colonia
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <vs-input
            name="colonia"
            icon-pack="feather"
            icon="icon-map"
            data-vv-as="Colonia"
            v-validate="'required'"
            v-model="funeraria.colonia"
            class="w-full uppercase"
          />
          <span
            class="text-danger text-sm"
            v-show="errors.has('colonia')"
          >{{ errors.first('colonia') }}</span>
        </div>
        <div class="vx-col w-full md:w-3/12 mt-3">
          <label for class="vs-input--label">
            C.P.
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <vs-input
            name="cp"
            icon-pack="feather"
            icon="icon-map"
            data-vv-as="C.P."
            v-validate="'required|numeric'"
            v-model="funeraria.cp"
            class="w-full uppercase"
          />
          <span class="text-danger text-sm" v-show="errors.has('cp')">{{ errors.first('cp') }}</span>
        </div>
        <div class="vx-col w-full md:w-3/12 mt-3">
          <label for class="vs-input--label">
            Zona horaria
            <span class="text-danger text-sm">(*)</span>:
          </label>
          <v-select
            v-model="selectedZona"
            :clearable="false"
            name="zona_horaria"
            data-vv-as="Zona horaria"
            v-validate="'required'"
            placeholder="Seleccione una zona horaria"
            :options="zonaHorarias"
          >
            <div slot="no-options">No hay opciones disponibles.</div>
          </v-select>
          <span
            class="text-danger text-sm"
            v-show="errors.has('zona_horaria')"
          >{{ errors.first('zona_horaria') }}</span>
        </div>
      </div>
      <div class="vx-row w-full mt-8">
        <div class="vx-col w-full">
          <div class="flex items-end">
            <feather-icon icon="TwitterIcon" class="mr-2" svgClasses="w-5 h-5" />
            <span class="leading-none font-medium">Social</span>
          </div>
        </div>
      </div>
      <vs-divider />
      <div class="vx-row mt-4">
        <div class="vx-col w-full md:w-6/12 mt-3">
          <vs-input
            icon-pack="feather"
            v-validate="'url'"
            data-vv-as="Facebook"
            icon="icon-facebook"
            name="facebook"
            v-model="funeraria.facebook"
            class="w-full"
            label="Facebook:"
          />
          <span
            class="text-danger text-sm"
            v-show="errors.has('facebook')"
          >{{ errors.first('facebook') }}</span>
        </div>
        <div class="vx-col w-full md:w-6/12 mt-3">
          <vs-input
            icon-pack="feather"
            v-validate="'url'"
            data-vv-as="Sitio web"
            icon="icon-globe"
            name="web"
            v-model="funeraria.web"
            class="w-full"
            label="Sitio de web:"
          />
          <span class="text-danger text-sm" v-show="errors.has('web')">{{ errors.first('web') }}</span>
        </div>
      </div>
      <vs-divider />
      <div class="vx-row">
        <div class="vx-col w-full">
          <div class="flex flex-wrap items-center justify-end">
            <vs-button
              color="success"
              size="small"
              class="ml-auto mt-2"
              icon-pack="feather"
              icon="icon-save"
              @click.prevent="save"
            >Guardar Funeraria</vs-button>
          </div>
        </div>
      </div>
    </form>
    <password-checker
      accion="Actualizar funeraria"
      :show="showChecker"
      :callback-on-success="saveData"
      :callback-on-error="closeChecker"
      @closeVerificar="closeChecker"
    />
  </div>
</template>
<script>
//use-utc
import _ from "lodash";
import { Validator } from "vee-validate";
import vSelect from "vue-select";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";
import inegiService from "@services/inegi";
import empresaService from "@services/empresa";
import satService from "@services/sat";
import PasswordChecker from "@/views/pages/confirmar_password";
import { format, compareAsc, parse } from "date-fns";

export default {
  components: {
    vSelect,
    Datepicker,
    PasswordChecker
  },
  computed: {},
  data() {
    return {
      disabledDates: {
        from: new Date()
      },
      routePassword: true,
      showChecker: false,
      spanishDatepicker: es,
      activeTab: 0,
      regimenes: [],
      estados: [],
      municipios: {
        funeraria: []
      },
      localidades: {
        funeraria: []
      },
      zonaHorarias: [
        {
          value: "America/Mazatlan",
          label: "MAZATLAN"
        },
        {
          value: "America/Mexico_City",
          label: "CIUDAD DE MEXICO"
        }
      ],
      selectedRegimen: null,
      selectedEstado: {
        funeraria: null
      },
      selectedMunicipio: {
        funeraria: null
      },
      selectedLocalidad: {
        funeraria: null
      },
      selectedZona: null,
      fecha_tnep_bk: "",
      fecha_rpc_bk: "",
      logo: "",
      funeraria: {
        nombre_comercial: "",
        razon_social: "",
        rfc: "",
        localidades_id: null,
        sat_regimenes_id: null,
        calle: "",
        num_ext: "",
        num_int: "",
        colonia: "",
        cp: "",
        zona_horaria: "",
        telefono: "",
        ext: "",
        fax: "",
        email: "",
        facebook: "",
        web: "",
        logo: ""
      }
    };
  },
  methods: {
    save() {
      let self = this;
      self.$validator.validateAll().then(result => {
        if (result) {
          if (self.routePassword) {
            self.showChecker = true;
          } else {
            self.saveData();
          }
        } else {
          self.$vs.notify({
            color: "warning",
            position: "top-center",
            title: "Advertencia",
            text:
              "Se han detectado errores de validacion, porfavor revisa los errores marcados en los campos de cada una de las secciones"
          });
        }
      });
    },
    saveData() {
      let self = this;
      self.funeraria.localidades_id = self.selectedLocalidad.funeraria.value;
      self.funeraria.zona_horaria = self.selectedZona.value;
      self.funeraria.sat_regimenes_id = self.selectedRegimen.value;

      let attributesUppercase = {
        funeraria: [
          "nombre_comercial",
          "razon_social",
          "rfc",
          "calle",
          "num_ext",
          "num_int",
          "colonia",
          "telefono",
          "ext",
          "fax"
        ]
      };

      _.forEach(attributesUppercase.funeraria, value => {
        if (self.funeraria[value])
          self.funeraria[value] = self.funeraria[value].toUpperCase();
      });

      let data = {
        funeraria: self.funeraria
      };

      self.closeChecker();
      self.$vs.loading();
      empresaService
        .updateFuneraria(self.funeraria)
        .then(response => {
          self.$vs.loading.close();
          if (response.status === 200) {
            self.$vs.notify({
              color: "success",
              position: "top-center",
              title: "Exito",
              text: "Hemos guardado tus cambios"
            });
            self.loadFuneraria();
          }
        })
        .catch(e => {
          self.$vs.loading.close();
          self.$vs.notify({
            color: "danger",
            position: "top-center",
            title: "Error",
            text:
              "Hubo un error mientras guardabamos tus cambios, porfavor vuelve a intentarlo"
          });
        });
    },
    closeChecker() {
      let self = this;
      self.showChecker = false;
    },
    estadoChange(fillTo) {
      let self = this;
      inegiService
        .getMunicipios(self.selectedEstado[fillTo].value)
        .then(response => {
          if (response.status === 200) {
            self.municipios[fillTo] = response.data;
            self.selectedMunicipio[fillTo] = null;
          }
        });
    },
    municipioChange(fillTo) {
      let self = this;
      inegiService
        .getLocalidades(self.selectedMunicipio[fillTo].value)
        .then(response => {
          if (response.status === 200) {
            self.localidades[fillTo] = response.data;
            self.selectedLocalidad[fillTo] = null;
          }
        });
    },
    loadFuneraria() {
      let self = this;
      self.$vs.loading();
      empresaService
        .getFuneraria()
        .then(response => {
          self.$vs.loading.close();
          if (response.data.funeraria !== undefined) {
            let funeraria = response.data.funeraria;
            //Regimen
            self.selectedRegimen = {
              value: funeraria.regimen.id,
              label: funeraria.regimen.regimen
            };
            //Load localidad
            self.selectedEstado.funeraria = {
              value: funeraria.localidad.municipio.estado.id,
              label: funeraria.localidad.municipio.estado.nombre
            };

            self.selectedMunicipio.funeraria = {
              value: funeraria.localidad.municipio.id,
              label: funeraria.localidad.municipio.nombre
            };

            self.selectedLocalidad.funeraria = {
              value: funeraria.localidad.id,
              label: funeraria.localidad.nombre
            };

            delete funeraria.localidad;
            delete funeraria.regimen;

            if (funeraria.zona_horaria == "America/Mazatlan") {
              self.selectedZona = {
                value: "America/Mazatlan",
                label: "MAZATLAN"
              };
            } else {
              self.selectedZona = {
                value: "America/Mexico_City",
                label: "CIUDAD DE MEXICO"
              };
            }

            self.funeraria = _.clone(funeraria);
          }
        })
        .catch(e => {
          self.$vs.loading.close();
        });
    }
  },
  created: function() {
    let self = this;
    satService.getRegimenes().then(response => {
      if (response.status === 200) {
        self.regimenes = response.data.data.map(obj => {
          return { value: obj.id, label: obj.regimen };
        });
      }
    });
    inegiService.getEstados().then(response => {
      if (response.status === 200) {
        self.estados = response.data;
      }

      self.loadFuneraria();
    });
  }
};
</script>
<style lang="scss">
[dir] .vs--single .vs__selected {
  word-break: break-all;
}
[dir] .tab-action-btn-fill-conatiner.con-vs-tabs .vs-tabs--content {
  height: 10px;
  padding: 0px !important;
}

.logo-container {
  text-align: center;
}
.logo {
  width: 350px;
}

.uppercase input {
  text-transform: uppercase !important;
}
</style>