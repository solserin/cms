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
						<span class="leading-none font-medium">Informacion del articulo</span>
					</div>
				</div>
			</div>
			<vs-divider/>
			<div class="vx-row w-full mt-4">
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Codigo de barras
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.codigo_barras" name="codigo_barras" data-vv-as="Codigo de barras" icon-pack="feather" icon="icon-book"   v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el codigo de barras"/>
					<span class="text-danger text-sm">{{ errors.first('codigo_barras', 'add-articulo') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Nombre:
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.nombre" name="nombre" data-vv-as="nombre"  icon-pack="feather" icon="icon-book-open"  v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el nombre del articulo"/>
					<span class="text-danger text-sm">{{ errors.first('razon-social', 'add-articulo') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Tipo de producto
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.tipos_producto_id" name="tipo_producto" data-vv-as="Tipo de producto" icon-pack="feather" icon="icon-trello"  type="text" class="w-full pb-1 pt-1"/>
					<span class="text-danger text-sm">{{ errors.first('tipo_producto', 'add-articulo') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Tipo de producto SAT
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.sat_productos_servicios_id" name="sat_productos_servicios_id" data-vv-as="Tipo de producto SAT"  icon-pack="feather" icon="icon-trello"  type="text" class="w-full pb-1 pt-1"/>
					<span class="text-danger text-sm">{{ errors.first('sat_productos_servicios_id', 'add-articulo') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Almacen
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.almacenes_id" name="almacenes_id" data-vv-as="Almacen"  icon-pack="feather" icon="icon-book-open"  type="text" class="w-full pb-1 pt-1"/>
					<span class="text-danger text-sm">{{ errors.first('almacenes_id', 'add-articulo') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Localizacion
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.localizacion" name="localizacion" data-vv-as="Localizacion"  icon-pack="feather" icon="icon-book-open"  type="text" class="w-full pb-1 pt-1"/>
					<span class="text-danger text-sm">{{ errors.first('localizacion', 'add-articulo') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Cantidad Maxima
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.maximo" name="maximo" data-vv-as="Maximo"  icon-pack="feather" icon="icon-book-open"  v-validate="'required'" type="text" class="w-full pb-1 pt-1"/>
					<span class="text-danger text-sm">{{ errors.first('maximo', 'add-articulo') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Cantidad Minima
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.minimo" name="minimo" data-vv-as="Minimo"  icon-pack="feather" icon="icon-book-open"  v-validate="'required'" type="text" class="w-full pb-1 pt-1"/>
					<span class="text-danger text-sm">{{ errors.first('minimo', 'add-articulo') }}</span>
				</div>
			</div>
			<div class="vx-row w-full mt-4">
				<div class="vx-col w-full">
					<div class="flex items-end">
						<feather-icon icon="MailIcon" class="mr-2" svgClasses="w-5 h-5" />
						<span class="leading-none font-medium">Precios de venta</span>
					</div>
				</div>
			</div>
			<vs-divider/> 
			<div class="vx-row w-full mt-4">
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Precio de compra neto
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.costo_neto" name="costo-neto" data-vv-as="Costo neto" icon-pack="feather" icon="icon-user"  v-validate="'required'" type="text" class="w-full pb-1 pt-1"/>
					<span class="text-danger text-sm">{{ errors.first('costo-neto', 'add-articulo') }}</span>
				</div>
				<div class="vx-col w-full md:w-4/12">
					<label class="text-sm opacity-75">
						Unidad de compra
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.unidades_compra_id" name="unidad-compra" data-vv-as="Unidad de compra" icon-pack="feather" icon="icon-phone"  v-validate="'required'" type="text" class="w-full pb-1 pt-1"/>
					<span class="text-danger text-sm">{{ errors.first('unidad-compra', 'add-articulo') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Unidad de venta
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.email" name="unidad-venta" data-vv-as="Unidad de venta" icon-pack="feather" icon="icon-at-sign"  v-validate="'required'" type="text" class="w-full pb-1 pt-1"/>
					<span class="text-danger text-sm">{{ errors.first('unidad-venta', 'add-articulo') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Impuestos
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo"  name="unidad-venta" data-vv-as="Impuestos" icon-pack="feather" icon="icon-at-sign"  v-validate="'required'" type="text" class="w-full pb-1 pt-1"/>
					<span class="text-danger text-sm">{{ errors.first('impuestos', 'add-articulo') }}</span>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Retenciones
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input data-vv-scope="add-articulo" v-model="articulo.email" name="retenciones" data-vv-as="Retenciones" icon-pack="feather" icon="icon-at-sign"  v-validate="'required'" type="text" class="w-full pb-1 pt-1"/>
					<span class="text-danger text-sm">{{ errors.first('retenciones', 'add-articulo') }}</span>
				</div>
			</div>
			</form>
    	</vs-popup>
  	</div>
</template>
<script>
import articuloService from '@services/articulos'
import _ from 'lodash'

export default {
    props: {
        show: {
            type: Boolean,
            required: true
		},
		articuloData: {
			type: Object
		}
    },
	watch: {
        show: function() {
			this.showPopup = this.show
			if (this.articuloData) {
				this.articulo = _.clone(this.articuloData)
				this.title = 'MODIFICAR ARITCULO'
			}
			
			if (!this.showPopup) {
				this.title = 'NUEVO ARITCULO'
				for (let member in this.articulo) 
					this.articulo[member] = null

				delete this.articulo.id
				this.articulo.status = 1

				let matcher = {
					scope: 'add-articulo',
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
			articulo: {
				codigo_barras: null,
				nombre: null,
				imagen: null,
				imagen2: null,
				imagen3: null,
				costo_neto: null,
				sat_productos_servicios_id: null,
				cuenta_predial: null,
				codigo_articulo: null,
				grupos_profeco_id: null,
				maximo: null,
				minimo: null,
				almacenes_id: null,
				existencia: null,
				unidades_compra_id: null,
				unidades_venta_id: null,
				factor: null,
				localizacion: null,
				facturable: null,
				caduca: null,
				rentable: null,
				familias_id: null,
				tipos_producto_id: null,
				impuestos: [],
				retenciones: [],
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
            self.$validator.validate('add-articulo.*').then(result => {
				if (result) {
					let promise
					if (self.articulo.id) {
						let id = _.clone(self.articulo.id)
						delete self.articulo.id
						promise = articuloService.update(id, self.articulo)
					} else {
						promise = articuloService.create(self.articulo)
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
								text: "Se ha guardado el articulo"
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