<template>
  <div>
    <form>
        <vs-alert color="danger" title="Funeraria no encontrada" v-if="!funeraria">
            <span>Porfavor, registre la informacion de la funeraria antes de registrar informacion del crematorio</span>
        </vs-alert>
        <div class="mt-4 pb-3" v-if="funeraria">
            <div class="vx-row w-full">
                <div class="vx-col w-full">
                    <div class="flex items-end">
                        <feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />
                        <span class="leading-none font-medium">Informacion del crematorio</span>
                    </div>
                </div>
            </div>
            <vs-divider/>
            <div class="vx-row">
                <div class="vx-col w-full mt-3 md:w-3/5">
                    <label for="" class="vs-input--label">Nombre del crematorio <span class="text-danger text-sm">(*)</span>:</label>
                    <vs-input class="w-full uppercase" :disabled="disableFields" icon-pack="feather" icon="icon-user" data-vv-as="Nombre comercial" v-model="crematorio.crematorio" v-validate="'required'" name="crematorio"/>
                    <span class="text-danger text-sm" v-show="errors.has('crematorio')">{{ errors.first('crematorio') }}</span>
                </div>
                <div class="vx-col w-full mt-3 md:w-1/5">
                    <label for="" class="vs-input--label">Telefonos <span class="text-danger text-sm">(*)</span>:</label>
                    <vs-input class="w-full uppercase" :disabled="disableFields" data-vv-as="Telefonos" icon-pack="feather" icon="icon-phone" v-model="crematorio.telefonos" v-validate="'required'" name="telefonos"/>
                    <span class="text-danger text-sm" v-show="errors.has('telefonos')">{{ errors.first('telefonos') }}</span>
                </div>
                <div class="vx-col w-full mt-3 md:w-1/5">
                    <vs-input class="w-full uppercase" :disabled="disableFields" data-vv-as="Fax" icon-pack="feather" icon="icon-file" v-model="crematorio.fax" label="Fax:" name="fax"/>
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
            <vs-divider/>
            <div class="vx-row">
                <div class="vx-col w-full md:w-1/3 mt-3">
                    <label for="" class="vs-input--label">Estado <span class="text-danger text-sm">(*)</span>:</label>
                    <v-select :disabled="disableFields" v-model="selectedEstado" :clearable="false" name="estado" data-vv-as="Estado" v-validate="'required'" placeholder="Seleccione un estado" @input="estadoChange" :options="estados">
                        <div  slot="no-options">No hay opciones disponibles.</div>
                    </v-select>
                    <span class="text-danger text-sm" v-show="errors.has('estado')">{{ errors.first('estado') }}</span>
                </div>
                <div class="vx-col w-full md:w-1/3 mt-3">
                    <label for="" class="vs-input--label">Municipio <span class="text-danger text-sm">(*)</span>:</label>
                    <v-select :disabled="disableFields" v-model="selectedMunicipio" :clearable="false" name="municipio" data-vv-as="Municipio" v-validate="'required'" placeholder="Seleccione un municipio"  @input="municipioChange" :options="municipios">
                        <div  slot="no-options">No hay opciones disponibles.</div>
                    </v-select>
                    <span class="text-danger text-sm" v-show="errors.has('municipio')">{{ errors.first('municipio') }}</span>
                </div>
                <div class="vx-col w-full md:w-1/3 mt-3">
                    <label for="" class="vs-input--label">Localidad(Ciudad) <span class="text-danger text-sm">(*)</span>:</label>
                    <v-select :disabled="disableFields" v-model="selectedLocalidad" :clearable="false" name="localidad" data-vv-as="Localidad" v-validate="'required'" placeholder="Seleccione una localidad" :options="localidades">
                        <div  slot="no-options">No hay opciones disponibles.</div>
                    </v-select>
                    <span class="text-danger text-sm" v-show="errors.has('localidad')">{{ errors.first('localidad') }}</span>
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col w-full md:w-3/12 mt-3">
                    <label for="" class="vs-input--label">Calle <span class="text-danger text-sm">(*)</span>:</label>
                    <vs-input :disabled="disableFields" name="calle" icon-pack="feather" icon="icon-map" data-vv-as="Calle" v-validate="'required'" v-model="crematorio.calle" class="w-full uppercase"/>
                    <span class="text-danger text-sm" v-show="errors.has('calle')">{{ errors.first('calle') }}</span>
                </div>
                <div class="vx-col w-full md:w-2/12 mt-3">
                    <label for="" class="vs-input--label">Numero Ext. <span class="text-danger text-sm">(*)</span>:</label>
                    <vs-input :disabled="disableFields" name="num_ext" icon-pack="feather" icon="icon-map" data-vv-as="Numero exterior" v-validate="'required'" v-model="crematorio.num_ext" class="w-full uppercase"/>
                    <span class="text-danger text-sm" v-show="errors.has('num_ext')">{{ errors.first('num_ext') }}</span>
                </div>
                <div class="vx-col w-full md:w-2/12 mt-3">
                    <label for="" class="vs-input--label">Numero Int. <span class="text-danger text-sm">(*)</span>:</label>
                    <vs-input :disabled="disableFields" name="num_int" icon-pack="feather" icon="icon-map" data-vv-as="Numero interior" v-model="crematorio.num_int" class="w-full uppercase"/>
                    <span class="text-danger text-sm" v-show="errors.has('num_int')">{{ errors.first('num_int') }}</span>
                </div>
                <div class="vx-col w-full md:w-3/12 mt-3">
                    <label for="" class="vs-input--label">Colonia <span class="text-danger text-sm">(*)</span>:</label>
                    <vs-input :disabled="disableFields" name="colonia" icon-pack="feather" icon="icon-map" data-vv-as="Colonia" v-validate="'required'" v-model="crematorio.colonia" class="w-full uppercase"/>
                    <span class="text-danger text-sm" v-show="errors.has('colonia')">{{ errors.first('colonia') }}</span>
                </div>
                <div class="vx-col w-full md:w-2/12 mt-3">
                    <label for="" class="vs-input--label">C.P. <span class="text-danger text-sm">(*)</span>:</label>
                    <vs-input :disabled="disableFields" name="cp" icon-pack="feather" icon="icon-map" data-vv-as="C.P." v-validate="'required|numeric'" v-model="crematorio.cp" class="w-full uppercase"/>
                    <span class="text-danger text-sm" v-show="errors.has('cp')">{{ errors.first('cp') }}</span>
                </div>
            </div>
            <div class="vx-row">
            </div>
            <vs-divider/>
            <div class="vx-row">
                <div class="vx-col w-full">
                    <div class="flex flex-wrap items-center justify-end">
                        <vs-button size="small"  v-if="!disableFields" class="ml-auto mt-2" icon-pack="feather" icon="icon-save" @click.prevent="save">Guardar Crematorio</vs-button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <password-checker accion="Actualizar el crematorio" :show="showChecker" :callback-on-success="saveData" :callback-on-error="closeChecker" @closeVerificar="closeChecker" />
  </div>
</template>
<script>
//use-utc	
import _ from 'lodash'
import { Validator } from 'vee-validate';
import vSelect from 'vue-select'
import inegiService from '@services/inegi'
import empresaService from '@services/empresa'
import PasswordChecker from '@/views/pages/confirmar_password'

export default {
    components: {
        vSelect,
        PasswordChecker
    },
    data() {
        return {
            routePassword: true,
            showChecker: false,
            estados: [],
            funeraria: true,
            disableFields: false,
            municipios: [],
            localidades: [],
            selectedRegimen: null,
            selectedEstado:  null,
            selectedMunicipio:  null,
            selectedLocalidad:  null,
            crematorio: {
                crematorio: '',
                localidades_id: null,
                calle: '',
                num_ext: '',
                num_int: '',
                colonia: '',
                cp: '',
                telefonos: '',
                fax: '',
            }
        }
    },
    methods: {
        save() {
            let self = this
            self.$validator.validateAll().then(result => {                
                if (result) {
                    if (self.routePassword) {
                        self.showChecker = true 
                    } else {
                        self.saveData()
                    }
                } else {
                    self.$vs.loading.close()
                    self.$vs.notify({
                        color:'warning',
                        position:'top-center',
                        title: 'Advertencia',
                        text: 'Se han detectado errores de validacion, porfavor revisa los errores marcados en los campos de cada una de las secciones'
                    })
                }
            })
        },
        saveData() {
            let self = this
            self.crematorio.localidades_id = self.selectedLocalidad.value

            //uppercase attributes

            let attributesUppercase = ['crematorio', 'calle', 'num_ext', 'num_int', 'colonia'];

            _.forEach(attributesUppercase, (value) => {
                if (self.crematorio[value])
                    self.crematorio[value] = self.crematorio[value].toUpperCase()
            })
            self.closeChecker()
            self.$vs.loading()
            empresaService.updateCrematorio(self.crematorio).then((response) => {
                self.$vs.loading.close()
                if (response.status === 200) {
                    self.$vs.notify({
                        color:'success',
                        position:'top-center',
                        title: 'Exito',
                        text: 'Hemos guardado tus cambios'
                    }) 
                    self.loadCrematorio()
                }
            }).catch((e) => {
                self.$vs.loading.close()
                self.$vs.notify({
                    color:'danger',
                    position:'top-center',
                    title: 'Error',
                    text: 'Hubo un error mientras guardabamos tus cambios, porfavor vuelve a intentarlo'
                })
            })
        },
        closeChecker() {
            let self = this
            self.showChecker = false
        },
        estadoChange () {
            let self = this
            inegiService.getMunicipios(self.selectedEstado.value).then((response) => {
                if (response.status === 200) {
                    self.municipios = response.data
                    self.selectedMunicipio = null
                }
            })
        },
        municipioChange (fillTo) {
            let self = this
            inegiService.getLocalidades(self.selectedMunicipio.value).then((response) => {
                if (response.status === 200) {
                    self.localidades = response.data
                    self.selectedLocalidad = null
                }
            })
        },
        loadCrematorio () {
            let self = this
            self.$vs.loading()
            empresaService.getCrematorio().then((response) => {
                self.$vs.loading.close()
                let crematorio = response.data

                self.selectedEstado = {
                    value: crematorio.localidad.municipio.estado.id,
                    label: crematorio.localidad.municipio.estado.nombre
                }

                self.selectedMunicipio = {
                    value: crematorio.localidad.municipio.id,
                    label: crematorio.localidad.municipio.nombre
                }

                self.selectedLocalidad = {
                    value: crematorio.localidad.id,
                    label: crematorio.localidad.nombre
                }

                delete crematorio.localidad

                self.crematorio = _.clone(crematorio)
            }).catch((e) => {        
                self.$vs.loading.close()
            })
        }
    },
    created: function () {
        let self = this
        inegiService.getEstados().then((response) => {
            if (response.status === 200) {
                self.estados = response.data
            }
            empresaService.getFuneraria().then((response) => {
                self.funeraria = true
                self.loadCrematorio()
            }).catch(() => {
                self.funeraria = false
            })
        })
    }
}
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