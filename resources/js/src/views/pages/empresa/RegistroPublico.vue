<template>
  <div>
    <form>        
        <vs-alert color="danger" title="Funeraria no encontrada" v-if="!funeraria">
            <span>Porfavor, registre la informacion de la funeraria antes de registrar informacion del registro publico</span>
        </vs-alert>
        <div class="mt-4 pb-3" v-if="funeraria">
            <div class="vx-row w-full">
                <div class="vx-col w-full">
                    <div class="flex items-end">
                        <feather-icon icon="BookmarkIcon" class="mr-2" svgClasses="w-5 h-5" />
                        <span class="leading-none font-medium">Informacion de escritura publica</span>
                    </div>
                </div>
            </div>
            <vs-divider/>
            <div class="vx-row">
                <div class="vx-col w-full md:w-4/12 mt-3">
                    <label for="" class="vs-input--label">Representante legal <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Nombre del representante legal">
                        <vs-input :disabled="disableFields" class="w-full uppercase" icon-pack="feather" icon="icon-user" data-vv-as="Representante legal" v-model="registro.rep_legal" v-validate="'required'" name="rep_legal"/>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('rep_legal')">{{ errors.first('rep_legal') }}</span>
                </div>

                <div class="vx-col w-full md:w-2/12 mt-3">
                    <label for="" class="vs-input--label">Num. del testimonio <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Numero del testimonio de la escritura publica">
                        <vs-input :disabled="disableFields" class="w-full uppercase" icon-pack="feather" icon="icon-file"  data-vv-as="Numero del testimonio" v-model="registro.t_nep" v-validate="'required|numeric'" name="t_nep"/>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('t_nep')">{{ errors.first('t_nep') }}</span>
                </div>

                <div class="vx-col w-full md:w-2/12 mt-3">
                    <label for="" class="vs-input--label">Fecha del testimonio <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Fecha en que se hizo el testimonio del numero de la escritura publica">
                        <datepicker :disabled="disableFields" :language="spanishDatepicker" :disabled-dates="disabledDates" name="fecha_tnep" data-vv-as="Fecha del testimonio"  v-validate="'required'" format="yyyy-MM-dd" placeholder="Seleccionar fecha" v-model="fecha_tnep_bk"></datepicker>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('fecha_tnep')">{{ errors.first('fecha_tnep') }}</span>
                </div>
                <div class="vx-col w-full md:w-4/12 mt-3">
                    <label for="" class="vs-input--label">Otorgada ante la fé del Lic. <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Nombre del o de la licenciada que dio fe en la inscripcion de la escritura publica">
                        <vs-input :disabled="disableFields" icon-pack="feather" icon="icon-user" class="w-full uppercase" data-vv-as="Otorgada ante la fé del Lic." v-model="registro.fe_lic" v-validate="'required'" name="fe_lic"/>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('fe_lic')">{{ errors.first('fe_lic') }}</span>
                </div>
            </div>
            <div class="vx-row w-full mt-8">
                <div class="vx-col w-full">
                    <div class="flex items-end">
                        <feather-icon icon="BriefcaseIcon" class="mr-2" svgClasses="w-5 h-5" />
                        <span class="leading-none font-medium">Informacion del notario publico</span>
                    </div>
                </div>
            </div>
            <vs-divider/>
            <div class="vx-row">
                <div class="vx-col w-full md:w-3/12 mt-3">
                    <label for="" class="vs-input--label">Notario público número <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Numero del notario publico">
                        <vs-input :disabled="disableFields" class="w-full uppercase" icon-pack="feather" icon="icon-briefcase" data-vv-as="Notario público número" v-model="registro.num_np" v-validate="'required|numeric'" name="num_np"/>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('num_np')">{{ errors.first('num_np') }}</span>
                </div>
                <div class="vx-col w-full md:w-3/12 mt-3">
                    <label for="" class="vs-input--label">Estado <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Estado del notario publico">
                        <v-select :disabled="disableFields" v-model="selectedEstado.notario" :clearable="false" name="estado_notario" data-vv-as="Estado" v-validate="'required'" placeholder="Seleccione un estado" @input="estadoChange('notario')" :options="estados">
                        <div  slot="no-options">No hay opciones disponibles.</div>
                    </v-select>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('estado_notario')">{{ errors.first('estado_notario') }}</span>
                </div>
                <div class="vx-col w-full md:w-3/12 mt-3">
                    <label for="" class="vs-input--label">Municipio <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Muncipio del notario publico">
                        <v-select :disabled="disableFields" v-model="selectedMunicipio.notario" :clearable="false" name="municipio_notario" data-vv-as="Municipio" v-validate="'required'" placeholder="Seleccione un municipio"  @input="municipioChange('notario')" :options="municipios.notario">
                        <div  slot="no-options">No hay opciones disponibles.</div>
                    </v-select>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('municipio_notario')">{{ errors.first('municipio_notario') }}</span>
                </div>
                <div class="vx-col w-full md:w-3/12 mt-3">
                    <label for="" class="vs-input--label">Localidad(Ciudad) <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Localidad/Ciudad donde se cuentra el notario publico">
                        <v-select :disabled="disableFields" v-model="selectedLocalidad.notario" :clearable="false" name="ciudad_np" data-vv-as="Localidad" v-validate="'required'" placeholder="Seleccione una localidad" :options="localidades.notario">
                        <div  slot="no-options">No hay opciones disponibles.</div>
                    </v-select>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('ciudad_np')">{{ errors.first('ciudad_np') }}</span>
                </div>
            </div>
            <div class="vx-row w-full mt-8">
                <div class="vx-col w-full">
                    <div class="flex items-end">
                        <feather-icon icon="BookIcon" class="mr-2" svgClasses="w-5 h-5" />
                        <span class="leading-none font-medium">Informacion del registro publico del comercio</span>
                    </div>
                </div>
            </div>
            <vs-divider/>
            <div class="vx-row">
                <div class="vx-col w-full md:w-4/12 mt-3">
                    <label for="" class="vs-input--label">Num. del registro publico <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Numero del registro publico del comercio">
                        <vs-input :disabled="disableFields" name="num_rpc" icon-pack="feather" icon="icon-briefcase" data-vv-as="Num. del registro publico" v-validate="'required|numeric'" v-model="registro.num_rpc" class="w-full uppercase"/>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('num_rpc')">{{ errors.first('num_rpc') }}</span>
                </div>
                <div class="vx-col w-full md:w-4/12 mt-3">
                    <label for="" class="vs-input--label">Fecha del registro publico <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Fecha del registro publico del comercio">
                        <datepicker :disabled="disableFields" :language="spanishDatepicker" :disabled-dates="disabledDates" name="fecha_rpc" data-vv-as="Fecha del registro publico"  v-validate="'required'" format="yyyy-MM-dd" placeholder="Seleccionar fecha" v-model="fecha_rpc_bk"></datepicker>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('fecha_rpc')">{{ errors.first('fecha_rpc') }}</span>
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col w-full md:w-1/3 mt-3">
                    <label for="" class="vs-input--label">Estado <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Estado del registro publico">
                        <v-select :disabled="disableFields" v-model="selectedEstado.registro" :clearable="false" name="estado_registro" data-vv-as="Estado" v-validate="'required'" placeholder="Seleccione un estado" @input="estadoChange('registro')" :options="estados">
                            <div  slot="no-options">No hay opciones disponibles.</div>
                        </v-select>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('estado_registro')">{{ errors.first('estado_registro') }}</span>
                </div>
                <div class="vx-col w-full md:w-1/3 mt-3">
                    <label for="" class="vs-input--label">Municipio <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Municipio del registro publico">
                        <v-select :disabled="disableFields" v-model="selectedMunicipio.registro" :clearable="false" name="municipio_registro" data-vv-as="Municipio" v-validate="'required'" placeholder="Seleccione un municipio"  @input="municipioChange('registro')" :options="municipios.registro">
                            <div  slot="no-options">No hay opciones disponibles.</div>
                        </v-select>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('municipio_registro')">{{ errors.first('municipio_registro') }}</span>
                </div>
                <div class="vx-col w-full md:w-1/3 mt-3">
                    <label for="" class="vs-input--label">Localidad(Ciudad) <span class="text-danger text-sm">(*)</span>:</label>
                    <vx-tooltip text="Localidad/Ciudad del registro publico">
                        <v-select :disabled="disableFields" v-model="selectedLocalidad.registro" :clearable="false" name="ciudad_rpc" data-vv-as="Localidad" v-validate="'required'" placeholder="Seleccione una localidad" :options="localidades.registro">
                            <div  slot="no-options">No hay opciones disponibles.</div>
                        </v-select>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('ciudad_rpc')">{{ errors.first('ciudad_rpc') }}</span>
                </div>
            </div>
            <vs-divider/>
            <div class="vx-row">
                <div class="vx-col w-full">
                    <div class="flex flex-wrap items-center justify-end">
                        <vs-button size="small" v-if="!disableFields" class="ml-auto mt-2" icon-pack="feather" icon="icon-save" @click.prevent="save">Guardar Registro Publico</vs-button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <password-checker accion="Actualizar registro publico" :show="showChecker" :callback-on-success="saveData" :callback-on-error="closeChecker" @closeVerificar="closeChecker" />
  </div>
</template>
<script>
//use-utc	
import _ from 'lodash'
import { Validator } from 'vee-validate';
import vSelect from 'vue-select'
import Datepicker from 'vuejs-datepicker'
import {es} from 'vuejs-datepicker/dist/locale'
import inegiService from '@services/inegi'
import empresaService from '@services/empresa'
import satService from '@services/sat'
import PasswordChecker from '@/views/pages/confirmar_password'
import { format, compareAsc, parse } from 'date-fns'

export default {
    components: {
        vSelect,
        Datepicker,
        PasswordChecker
    },
    computed: {
    },
    data() {
        return {
            disabledDates: {
                from: new Date()
            },
            routePassword: true,
            showChecker: false,
            spanishDatepicker: es,
            estados: [],
            disableFields: false,
            funeraria: true,
            municipios: {
                notario: [],
                registro: []
            },
            localidades: {
                notario: [],
                registro: []
            },
            selectedRegimen: null,
            selectedEstado: {
                notario: null,
                registro: null
            },
            selectedMunicipio: {
                notario: null,
                registro: null
            },
            selectedLocalidad: {
                notario: null,
                registro: null
            },
            selectedZona: null,
            fecha_tnep_bk: '',
            fecha_rpc_bk: '',
            registro: {
                rep_legal: '',
                t_nep: '',
                fecha_tnep: '',
                fe_lic: '',
                num_np: '',
                ciudad_np: null,
                ciudad_rpc: null,
                num_rpc: '',
                fecha_rpc: ''
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

            self.registro.ciudad_np = self.selectedLocalidad.notario.value
            self.registro.ciudad_rpc = self.selectedLocalidad.registro.value

            self.registro.fecha_tnep = format(self.fecha_tnep_bk, 'yyy-MM-dd')
            self.registro.fecha_rpc = format(self.fecha_rpc_bk, 'yyy-MM-dd')

            let attributesUppercase = {
                registro: ['rep_legal', 'fe_lic']
            }

            _.forEach(attributesUppercase.registro, (value) => {
                if (self.registro[value])
                    self.registro[value] = self.registro[value].toUpperCase()
            })

            self.closeChecker()
            self.$vs.loading()
            empresaService.updateRegistroPublico(self.registro).then((response) => {
                self.$vs.loading.close()
                if (response.status === 200) {
                    self.$vs.notify({
                        color:'success',
                        position:'top-center',
                        title: 'Exito',
                        text: 'Hemos guardado tus cambios'
                    }) 
                    self.loadRegistroPublico()
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
        estadoChange (fillTo) {
            let self = this
            inegiService.getMunicipios(self.selectedEstado[fillTo].value).then((response) => {
                if (response.status === 200) {
                    self.municipios[fillTo] = response.data
                    self.selectedMunicipio[fillTo] = null
                }
            })
        },
        municipioChange (fillTo) {
            let self = this
            inegiService.getLocalidades(self.selectedMunicipio[fillTo].value).then((response) => {
                if (response.status === 200) {
                    self.localidades[fillTo] = response.data
                    self.selectedLocalidad[fillTo] = null
                }
            })
        },
        loadRegistroPublico () {
            let self = this
            self.$vs.loading()
            empresaService.getRegistroPublico().then((response) => {
                self.$vs.loading.close()
                let registro  = response.data.registro

                self.selectedEstado.notario = {
                    value: registro.localidad_n_p.municipio.estado.id,
                    label: registro.localidad_n_p.municipio.estado.nombre
                }
                
                self.selectedEstado.registro = {
                    value: registro.localidad_r_p_c.municipio.estado.id,
                    label: registro.localidad_r_p_c.municipio.estado.nombre
                }
                
                self.selectedMunicipio.notario = {
                    value: registro.localidad_n_p.municipio.id,
                    label: registro.localidad_n_p.municipio.nombre
                }
                
                self.selectedMunicipio.registro = {
                    value: registro.localidad_r_p_c.municipio.id,
                    label: registro.localidad_r_p_c.municipio.nombre
                }
                
                self.selectedLocalidad.notario = {
                    value: registro.localidad_n_p.id,
                    label: registro.localidad_n_p.nombre
                }
                
                self.selectedLocalidad.registro = {
                    value: registro.localidad_r_p_c.id,
                    label: registro.localidad_r_p_c.nombre
                }

                delete registro.localidad_n_p
                delete registro.localidad_r_p_c

                self.fecha_tnep_bk = parse(registro.fecha_tnep, 'yyyy-MM-dd', new Date())
                self.fecha_rpc_bk = parse(registro.fecha_rpc, 'yyyy-MM-dd', new Date())
                self.registro = _.clone(registro)                
            }).catch((e) => {        
                self.$vs.loading.close()
            })
        }
    },
    created: function () {
        let self = this
        Promise.all([
            inegiService.getEstados().then((response) => {
                if (response.status === 200) {
                    self.estados = response.data
                }
            })
        ]).then(() => {
            empresaService.getFuneraria().then((response) => {
                self.funeraria = true
                self.loadRegistroPublico()
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