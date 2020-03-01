<template>
	<div>
		<vx-card title="Configuración de facturación">
			<form>
				<div class="vx-row" v-if="!disableFields">
					<div class="vx-col w-full mt-3 flex justify-center">
						<div class="flex items-center">
							<div class="align-center">
								<vx-tooltip text="Certificado proporcionado por el SAT">
								<vs-upload
									:key="currentTimeStampCer"
									accept=".cer"
									single-upload
									ref="upload" 
									text="Subir certificado" 
									limit="1" 
									:headers="requestHeaders" 
									fileName="certificate" 
									automatic 
									action="/api/empresa/facturacion/validateCER" 
									@on-success="successCertificate"
									@on-error="errorCertificate"
									@on-delete="clearCertificate" />
								<span class="text-danger text-sm" v-show="certificateError">No se ha subido archivo de certificado</span>
								</vx-tooltip>
							</div>
							<div class="align-center">
								<vx-tooltip text="Archivo proporcionado por el SAT, es usado para poder realizar sellos en la facturación electronica, este debe corresponder al certificado de otra manera la facturación fallara">
								<vs-upload
									:key="currentTimeStampKey"
									accept=".key"
									single-upload
									ref="uploadKey" 
									text="Subir archivo key" 
									limit="1" 
									:headers="requestHeaders" 
									fileName="key" 
									automatic 
									action="/api/empresa/facturacion/validateKEY" 
									@on-success="successKey"
									@on-error="errorKey"
									@on-delete="clearKey" />
								</vx-tooltip>
								<span class="text-danger text-sm" v-show="keyError">No se ha subido el archivo key</span>
							</div>
						</div>
					</div>
					<div class="vx-col w-full mt-3 flex justify-center">
						<div class="flex items-center">
							<vs-alert active="true" v-show="keyFilePath || cerFilePath">
								El archivo CER y KEY ya se encuentra registrados, si desea reemplazar cualquiera de estos porfavor suba el archivo correspodiente. <b>SI EL ARCHIVO QUE INTENTA SUBIR NO ES VALIDO, ESTE SERA IGNORADO Y SE MANTENDRA EL ARCHIVO QUE SE ENCUENTRA ACTUALMENTE REGISTRADO.</b>
							</vs-alert>
						</div>
					</div>
				</div>
				<div class="vx-row w-full">
					<div class="vx-col md:w-3/12 mt-3">
						<vx-tooltip text="La contraseña del certificado no se muestra, para cambiarla basta con introducirla en el campo de texto">
							<vs-input :disabled="disableFields" v-model="facturacion.password" icon-pack="feather" icon="icon-lock" data-vv-as="Contraseña" type="password" class="w-full" label="Contraseña del certificado:" v-validate="rules" name="password" ref="password"/>
						</vx-tooltip>
						<span class="text-danger text-sm" v-show="errors.has('password')">{{ errors.first('password') }}</span>
					</div>
					<div class="vx-col md:w-3/12 mt-3">
						<vs-input v-model="repeatPassword" icon-pack="feather" icon="icon-lock"  :disabled="disabledPassword || disableFields" data-vv-as="Repetir Contraseña" type="password" class="w-full" label="Repetir Contraseña:" v-validate="rulesRepeat" name="repeat_password" />
						<span class="text-danger text-sm" v-show="errors.has('repeat_password')">{{ errors.first('repeat_password') }}</span>
					</div>
					<div class="vx-col md:w-3/12 mt-3">
						<vx-tooltip text="Correo usado para emitir facturas">
							<vs-input :disabled="disableFields" data-vv-as="Correo" icon-pack="feather" icon="icon-mail" :bails="false" v-model="facturacion.email_emisor" class="w-full" label="Correo*:" type="email" v-validate="'required|email'" name="email" />
						</vx-tooltip>
						<span class="text-danger text-sm" v-show="errors.has('email')">{{ errors.first('email') }}</span>
					</div>                    
					<div class="vx-col w-full md:w-3/12 mt-3">
						<label for="" class="vs-input--label">Tipo de moneda*:</label>
						<v-select v-model="selectedMoneda" :disabled="disableFields" :clearable="false" name="moneda" data-vv-as="Moneda" v-validate="'required'" placeholder="Seleccione un tipo de moneda" :options="monedas">
							<div  slot="no-options">No hay opciones disponibles.</div>
						</v-select>
						<span class="text-danger text-sm" v-show="errors.has('moneda')">{{ errors.first('moneda') }}</span>
					</div>
				</div>
				<vs-divider v-if="!disableFields" />
				<div class="vx-row" v-if="!disableFields">
					<div class="vx-col w-full">
						<div class="flex flex-wrap items-center justify-end">
							<vs-button class="ml-auto mt-2" icon-pack="feather" icon="icon-save" @click.prevent="save">Guardar</vs-button>
						</div>
					</div>
				</div>
			</form>
		</vx-card>
		<password-checker :show="showChecker" :callback-on-success="saveData" :callback-on-error="closeChecker" :cancel-callback="closeChecker" />
	</div>
</template>
<script>
import vSelect from 'vue-select'
import empresaService from '@services/empresa'
import satService from '@services/sat'
import access from '../../../route-access'
import {routePassword} from '@/route-access'
import PasswordChecker from '@/components/PasswordChecker'
import  _ from 'lodash'

export default {
  components: {
    vSelect,
    PasswordChecker
  },
  data() {
    return {
      monedas: [],
      editAccess: true,
      viewAccess: true,
      disableFields: false,
      currentTimeStampKey: null,
      currentTimeStampCer: null,
      selectedMoneda: null,
      certificateError: false,
      keyError: false,
      cerFilePath: '',
      keyFilePath: '',
      repeatPassword: '',
      showChecker: false,
      facturacion: {
        certificateFile: null,
        keyFile: null,
        password: null,
        url_web_service: null,
        email_emisor: null,
        sat_monedas_id: null
      },
      requestHeaders: {
        'Authorization' : 'Bearer ' + localStorage.getItem('accessToken')
      }
    }
  },
  computed: {
    rules() {
      let self = this
      if (self.cerFilePath && self.keyFilePath) {
        return ''
      }
      return 'required'
    },
    rulesRepeat() {
      let self = this
      if (self.facturacion.password) {
        return 'required|confirmed:password'
      }
      return ''
    },
    disabledPassword() {
      let self = this
      return !!!self.facturacion.password;
    }
  },
  methods:{
    save() {
      let self = this
      let filesError = false
      if (!self.facturacion.certificateFile && !self.cerFilePath) {
        self.certificateError = true
        filesError = true
      }

      if (!self.facturacion.keyFile  && !self.keyFilePath) {
        self.keyError = true
        filesError = true
      }

      self.$validator.validateAll().then(result => {
        if (result && !filesError) {
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
      let form = new FormData();
      form.append('certificateFile', self.facturacion.certificateFile)
      form.append('keyFile', self.facturacion.keyFile)
      form.append('password', self.facturacion.password)
      form.append('url_web_service', self.facturacion.url_web_service)
      form.append('email_emisor', self.facturacion.email_emisor)
      form.append('sat_monedas_id', self.selectedMoneda.value)
      self.closeChecker()
      self.$vs.loading()
      empresaService.uploadFacturacion(form).then((response) => {
          self.$vs.loading.close()
          if (response.status === 200) {
              self.$vs.notify({
                  color:'success',
                  position:'top-center',
                  title: 'Exito',
                  text: 'Hemos guardado tus cambios'
              })
              self.repeatPassword = ''
              self.facturacion.password = ''
              self.facturacion.certificateFile = null
              self.facturacion.keyFile = null

              self.currentTimeStampKey = 'KEY' + (new Date()).getMilliseconds()
              self.currentTimeStampCer = 'CER' + (new Date()).getMilliseconds()
              self.loadFacturacion()
          }
      }).catch(() => {
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
    successCertificate(){
      let self = this
      self.facturacion.certificateFile = self.$refs.upload.filesx[self.$refs.upload.filesx.length - 1]
      self.certificateError = false
      self.$vs.notify({ 
        position:'top-center', 
        color:'success',
        title:'Validacion',
        text:'El certificado proporcionado es valido'
      })
    },
    errorCertificate(event) {
      let self = this
      self.$refs.upload.srcs[self.$refs.upload.srcs.length - 1].error = true
      self.facturacion.certificateFile = null
      let data = JSON.parse(event.target.response)
      self.$vs.notify({
        position:'top-center', 
        color:'danger',
        title:'Validacion',
        text: data.error.message
      })
    },
    clearCertificate() {
      let self = this
      self.facturacion.certificateFile = null
    },
    //Key methods
    successKey(){
      let self = this
      self.facturacion.keyFile = self.$refs.uploadKey.filesx[self.$refs.uploadKey.filesx.length - 1]
      self.keyError = false
      self.$vs.notify({ 
        position:'top-center', 
        color:'success',
        title:'Validacion',
        text:'El archivo key proporcionado es valido'
      })
    },
    errorKey(event) {
      let self = this
      self.$refs.uploadKey.srcs[self.$refs.uploadKey.srcs.length - 1].error = true
      self.facturacion.keyFile = null
      let data = JSON.parse(event.target.response)
      self.$vs.notify({
        position:'top-center', 
        color:'danger',
        title:'Validacion',
        text: data.error.message
      })
    },
    clearKey() {
      let self = this
      self.facturacion.keyFile = null
    },
    loadFacturacion() {
      let self = this
      self.$vs.loading()
      empresaService.getFacturacion().then((result) => {
          self.$vs.loading.close()
          if (result.data) {
            let data = _.clone(result.data)
            self.facturacion.email_emisor = data.email_emisor
            self.facturacion.url_web_service = data.url_web_service
            self.selectedMoneda = {
              value: data.moneda.id,
              label: data.moneda.descripcion
            }
            self.cerFilePath = data.cer
            self.keyFilePath = data.key
          }
      }).catch(() => {
        self.$vs.loading.close()
      })
    }
  },
  beforeMount: function () {
    let self = this
    self.currentTimeStampKey = 'KEY' + (new Date()).getMilliseconds()
    self.currentTimeStampCer = 'CER' + (new Date()).getMilliseconds()
  },
  created: function () {
    let self = this
    self.editAccess = access('/empresa/facturacion', 'e')
    self.viewAccess = access('/empresa/facturacion', 'v')
    self.disableFields = self.editAccess ? false : true
    self.routePassword = routePassword('/empresa/facturacion')
    satService.getMonedas().then((result) => {
      if (result.data) {
        self.monedas = result.data.data
        self.loadFacturacion()
      }
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
.align-center{
  text-align: center;
}
</style>