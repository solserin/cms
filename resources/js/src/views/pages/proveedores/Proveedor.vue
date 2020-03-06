<template>
	<div class="centerx">
    	<vs-popup  class="usuarios" close="cancelar" :title="title" :active="showPopup" button-close-hidden >
			<form>
			<vs-button @click="guardar"  icon-pack="feather" icon="icon-save" color="success" size="small" class="float-right">Guardar</vs-button>
			<vs-button @click="cancel"  icon-pack="feather" icon="icon-x"  color="danger" size="small" class="float-right mr-5">Cancelar</vs-button>
			<span class="text-sm texto-importante">IMPORTANTE Los campos con (*) son obligatorios.</span>
			<div class="vx-row w-full mt-4">
				<div class="vx-col w-full">
					<div class="flex items-end">
						<feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />
						<span class="leading-none font-medium">Informacion del proveedor</span>
					</div>
				</div>
			</div>
			<vs-divider/>
			<div class="vx-row w-full mt-4">
				<div class="vx-col w-full md:w-5/12">
					<label class="text-sm opacity-75">
						Nombre comercial
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-proveedor" v-model="proveedor.nombre_comercial" name="nombre-comercial" data-vv-as="Nombre comercial" icon-pack="feather" icon="icon-book"   v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el nombre comercial"/>
					<span class="text-danger text-sm">{{ errors.first('nombre-comercial', 'add-proveedor', 'add-proveedor') }}</span>
				</div>
				<div class="vx-col w-full md:w-4/12">
					<label class="text-sm opacity-75">
						Razon Social
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-proveedor" v-model="proveedor.razon_social" name="razon-social" data-vv-as="Razon social"  icon-pack="feather" icon="icon-book-open"  v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese la razon social"/>
					<span class="text-danger text-sm">{{ errors.first('razon-social', 'add-proveedor') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						RFC
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-proveedor" v-model="proveedor.rfc" name="rfc" data-vv-as="RFC" icon-pack="feather" icon="icon-trello"  v-validate="'required|min:12|max:12'"  type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el RFC"/>
					<span class="text-danger text-sm">{{ errors.first('rfc', 'add-proveedor') }}</span>
				</div>
			</div>
			<div class="vx-row w-full mt-4">
				<div class="vx-col w-full">
					<div class="flex items-end">
						<feather-icon icon="MailIcon" class="mr-2" svgClasses="w-5 h-5" />
						<span class="leading-none font-medium">Informacion de contacto</span>
					</div>
				</div>
			</div>
			<vs-divider/>
			<div class="vx-row w-full mt-4">
				<div class="vx-col w-full md:w-5/12">
					<label class="text-sm opacity-75">
						Nombre del contacto
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-proveedor" v-model="proveedor.nombre_contacto" name="nombre-contacto" data-vv-as="Nombre del contacto" icon-pack="feather" icon="icon-user"  v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el nombre del contacto"/>
					<span class="text-danger text-sm">{{ errors.first('nombre-contacto', 'add-proveedor') }}</span>
				</div>
				<div class="vx-col w-full md:w-4/12">
					<label class="text-sm opacity-75">
						Telefono de contacto
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-proveedor" v-model="proveedor.telefono" name="telefono-contacto" data-vv-as="Telefono de contacto" icon-pack="feather" icon="icon-phone"  v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el telefono de contacto"/>
					<span class="text-danger text-sm">{{ errors.first('telefono-contacto', 'add-proveedor') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Correo electronico
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-proveedor" v-model="proveedor.email" name="correo-electronico" data-vv-as="Correo electronico" icon-pack="feather" icon="icon-at-sign"  v-validate="'required|email'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el correo electronico"/>
					<span class="text-danger text-sm">{{ errors.first('correo-electronico', 'add-proveedor') }}</span>
				</div>
			</div>
			<div class="vx-row w-full md:mt-4">
				<div class="vx-col w-full md:w-5/12">
					<label class="text-sm opacity-75">
						Sitio WEB
					</label>
					<vs-input data-vv-scope="add-proveedor" v-model="proveedor.pagina_web"  name="sitio-web" data-vv-as="Sitio WEB" icon-pack="feather" icon="icon-chrome" type="text" class="w-full pb-1 pt-1" v-validate="'url'" placeholder="Ingrese el sitio web"/>
					<span class="text-danger text-sm">{{ errors.first('sitio-web', 'add-proveedor') }}</span>
				</div>
			</div>
			<div class="vx-row w-full mt-4">
				<div class="vx-col w-full">
					<div class="flex items-end">
						<feather-icon icon="MapPinIcon" class="mr-2" svgClasses="w-5 h-5" />
						<span class="leading-none font-medium">Direccion</span>
					</div>
				</div>
			</div>
			<vs-divider/>
			<div class="vx-row mt-4">
				<div class="vx-col w-full md:w-1/3">
					<label for class="vs-input--label">Pais
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-proveedor" v-model="proveedor.pais" data-vv-as="Pais" icon-pack="feather" icon="icon-map" name="pais"  v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el pais"/>
					<span class="text-danger text-sm" v-show="errors.has('pais', 'add-proveedor')">{{ errors.first('pais', 'add-proveedor') }}</span>
				</div>
				<div class="vx-col w-full md:w-1/3">
					<label for class="vs-input--label">Estado
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-proveedor" v-model="proveedor.estado" icon-pack="feather" icon="icon-map" name="estado"  v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el estado"/>
					<span class="text-danger text-sm" v-show="errors.has('estado', 'add-proveedor')">{{ errors.first('estado', 'add-proveedor') }}</span>
				</div>
				<div class="vx-col w-full md:w-1/3">
					<label for class="vs-input--label">Ciudad
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-proveedor" v-model="proveedor.ciudad" icon-pack="feather" icon="icon-map" name="ciudad"  v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese la ciudad"/>
					<span class="text-danger text-sm" v-show="errors.has('ciudad', 'add-proveedor')">{{ errors.first('ciudad', 'add-proveedor') }}</span>
				</div>
			</div>
			<div class="vx-row md:mt-4">
				<div class="vx-col w-full md:w-4/12">
					<label for class="vs-input--label">Calle
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input
						data-vv-scope="add-proveedor"
						v-model="proveedor.calle"
						icon-pack="feather"
						icon="icon-map"
						name="direccion_calle"
						data-vv-as="Calle"
						v-validate="'required'"
						class="w-full"
					/>
					<span
						class="text-danger text-sm"
						v-show="errors.has('direccion_calle', 'add-proveedor')"
					>{{ errors.first('direccion_calle', 'add-proveedor') }}</span>
				</div>
				<div class="vx-col w-full md:w-2/12">
					<label for class="vs-input--label">Num. Ext.
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input
						data-vv-scope="add-proveedor"
						v-model="proveedor.num_ext"
						icon-pack="feather"
						icon="icon-map"
						name="num-ext"
						data-vv-as="Numero exterior"
						v-validate="'required'"
						placeholder="Numero exterior"
						class="w-full"
					/>
					<span
						class="text-danger text-sm"
						v-show="errors.has('num-ext', 'add-proveedor')"
					>{{ errors.first('num-ext', 'add-proveedor') }}</span>
				</div>
				<div class="vx-col w-full md:w-2/12">
					<label for class="vs-input--label">Num. Int.</label>
					<vs-input
						data-vv-scope="add-proveedor"
						v-model="proveedor.num_int"
						icon-pack="feather"
						icon="icon-map"
						name="num-int"
						placeholder="Numero Interior"
						class="w-full"
					/>
				</div>
				<div class="vx-col w-full md:w-4/12">
					<label for class="vs-input--label">Colonia
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input
						data-vv-scope="add-proveedor"
						v-model="proveedor.col"
						icon-pack="feather"
						icon="icon-map"
						name="colonia"
						data-vv-as="Colonia"
						v-validate="'required'"
						placeholder="Colonia"
						class="w-full"
					/>
					<span
						class="text-danger text-sm"
						v-show="errors.has('colonia', 'add-proveedor')"
					>{{ errors.first('colonia', 'add-proveedor') }}</span>
				</div>
			</div>
			<div class="vx-row md:mt-4">
				<div class="vx-col w-full md:w-2/12">
					<label for class="vs-input--label">C.P.
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input
						data-vv-scope="add-proveedor"
						v-model="proveedor.cp"
						icon-pack="feather"
						icon="icon-map"
						name="cp"
						data-vv-as="C.P."
						v-validate="'required|alpha_num'"
						class="w-full"
					/>
					<span
						class="text-danger text-sm"
						v-show="errors.has('cp', 'add-proveedor')"
					>{{ errors.first('cp', 'add-proveedor') }}</span>
				</div>
			</div>
			</form>
    	</vs-popup>
  	</div>
</template>
<script>
import proveedorService from '@services/proveedores'
import _ from 'lodash'

export default {
    props: {
        show: {
            type: Boolean,
            required: true
		},
		proveedorData: {
			type: Object
		}
    },
	watch: {
        show: function() {
			this.showPopup = this.show
			if (this.proveedorData) {
				this.proveedor = _.clone(this.proveedorData)
				this.title = 'MODIFICAR PROVEEDOR'
			}
			
			if (!this.showPopup) {
				this.title = 'NUEVO PROVEEDOR'
				for (let member in this.proveedor) 
					this.proveedor[member] = null

				delete this.proveedor.id
				this.proveedor.status = 1

				let matcher = {
					scope: 'add-proveedor',
					vmId: this.$validator.id
				}
            	this.$validator.reset(matcher)
			}
        },
	},
	data() {
		return {
			title: 'NUEVO PROVEEDOR',
            showPopup: false,
			proveedor: {
				nombre_comercial: null,
				razon_social: null,
				rfc: null,
				nombre_contacto: null,
				telefono: null,
				pagina_web: null,
				email: null,
				calle: null,
				col: null,
				num_ext: null,
				num_int: null,
				cp: null,
				ciudad: null,
				estado: null,
				pais: null,
				status: 1
			}
		}
    },
    methods: {
        cancel() {
			this.$emit('update:show', false)
			this.$emit('on-cancel')
        },
        guardar() {
            let self = this
            self.$validator.validate('add-proveedor.*').then(result => {
				if (result) {
					let promise
					if (self.proveedor.id) {
						let id = _.clone(self.proveedor.id)
						delete self.proveedor.id
						promise = proveedorService.update(id, self.proveedor)
					} else {
						promise = proveedorService.create(self.proveedor)
					}

					promise.then((response) => {
						self.$vs.loading.close()
						if (response.status >= 200) {
							this.$emit('update:show', false)
							this.$emit('on-close')
							self.$vs.notify({
								color: "success",
								position: "top-center",
								title: "Completado",
								text: "Se ha guardado el proveedor"
							});
						} else {
							self.$vs.notify({
								color: "warning",
								position: "top-center",
								title: "Advertencia",
								text: "Algo ha sucedido, vuelva a intentarlo"
							});
						}

					}).catch((error) => {
						self.$vs.loading.close()
						if (error.response.status === 409) {
							self.$vs.notify({
								color:'danger',
								position:'top-center',
								title: 'Error',
								text: error.response.data.error
							})
						} else {
							self.$vs.notify({
								color:'danger',
								position:'top-center',
								title: 'Error',
								text:'Algo ha sucedido, vuelva a intentarlo'
							})
						}
					})
				}
			})
        }
    },
    created() { 
        this.$emit('update:show', JSON.parse(this.show))
    }
}
</script>