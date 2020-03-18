<template>
	<div class="centerx">
    	<vs-popup class="usuarios" close="cancelar" :title="title" :active="showPopup" button-close-hidden >
			<form>			
			<div class="vx-row">
				<div class="vx-col w-full">
					<vs-button @click="save" icon-pack="feather" icon="icon-save" color="success" size="small" class="float-right">Guardar</vs-button>
					<vs-button @click="cancel" icon-pack="feather" icon="icon-x"  color="danger" size="small" class="float-right mr-5">Cancelar</vs-button>
				</div>
			</div>
			
			<div class="vx-row">
				<div class="vx-col w-full">
					<vs-tabs v-model="activeTab" class="tab-action-btn-fill-conatiner">
						<vs-tab label="Articulo" icon-pack="feather" icon="icon-book-open"></vs-tab>
						<vs-tab label="Articulos del paquete" icon-pack="feather" icon="icon-book-open" :disabled="!isPaquete"></vs-tab>
					</vs-tabs>
				</div>
			</div>
			<div class="vx-row">
				<div class="vx-col w-full">
					<span class="text-sm texto-importante">IMPORTANTE Los campos con (*) son obligatorios.</span>
				</div>
			</div>
			<div v-show="activeTab == 0">
				<div class="vx-row">
					<div class="vx-col w-full">
						<div class="flex items-end">
							<!--<feather-icon icon="UserIcon" class="mr-2" svgClasses="w-5 h-5" />-->
							<span class="leading-none font-medium">Informacion del articulo</span>
						</div>
						<vs-divider/>
					</div>
					<div class="vx-col w-full md:w-3/12">
						<label class="text-sm opacity-75">
							Codigo de barras
							<span class="text-danger text-sm">(*)</span>
						</label>
						<vs-input data-vv-scope="add-articulo" v-model="articulo.codigo_barras" name="codigo_barras" data-vv-as="Codigo de barras" v-validate="'required|numeric'" type="text" class="w-full pb-1 pt-1" />
						<span class="text-danger text-sm">{{ errors.first('codigo_barras', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full md:w-3/12">
						<label class="text-sm opacity-75">
							Nombre:
							<span class="text-danger text-sm">(*)</span>
						</label>
						<vs-input data-vv-scope="add-articulo" v-model="articulo.nombre" name="nombre" data-vv-as="nombre" v-validate="'required'" type="text" class="w-full pb-1 pt-1" />
						<span class="text-danger text-sm">{{ errors.first('nombre', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full md:w-3/12">
						<label class="text-sm opacity-75">
							Tipo de producto
							<span class="text-danger text-sm">(*)</span>
						</label>
						<v-select :disabled="tipoProductoDisabled" data-vv-scope="add-articulo" class="w-full pb-1 pt-1" v-model="selectedTipoProducto" :clearable="false" name="tipo_producto" data-vv-as="Tipo de producto" v-validate="'required'" :options="tiposProductos">
							<div slot="no-options">No hay opciones disponibles.</div>
						</v-select>
						<span class="text-danger text-sm">{{ errors.first('tipo_producto', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full md:w-3/12">
						<label class="text-sm opacity-75">
							Tipo de producto SAT
							<span class="text-danger text-sm">(*)</span>
						</label>
						<v-select data-vv-scope="add-articulo" class="w-full pb-1 pt-1" v-model="selectedTipoProductoSAT" :clearable="false" name="sat_productos_servicios_id" data-vv-as="Tipo de producto/servicio SAT" v-validate="'required'" :options="tiposProductosSAT">
							<div slot="no-options">No hay opciones disponibles.</div>
						</v-select>
						<span class="text-danger text-sm">{{ errors.first('sat_productos_servicios_id', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full md:w-3/12 mt-4">
						<label class="text-sm opacity-75">
							Grupo profeco
							<span class="text-danger text-sm">(*)</span>
						</label>
						<v-select data-vv-scope="add-articulo" class="w-full pb-1 pt-1" v-model="selectedGrupoProfeco" :clearable="false" name="grupo_profeco" data-vv-as="Grupo profeco" v-validate="'required'" :options="gruposProfeco">
							<div slot="no-options">No hay opciones disponibles.</div>
						</v-select>
						<span class="text-danger text-sm">{{ errors.first('grupo_profeco', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full md:w-3/12 mt-4">
						<label class="text-sm opacity-75">
							Categoria
							<span class="text-danger text-sm">(*)</span>
						</label>
						<v-select data-vv-scope="add-articulo" @input="getFamilias" v-model="selectedCategoria" class="w-full pb-1 pt-1" :clearable="false" name="categoria" data-vv-as="Categoria" v-validate="'required'" :options="categorias">
							<div slot="no-options">No hay opciones disponibles.</div>
						</v-select>
						<span class="text-danger text-sm">{{ errors.first('categoria', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full md:w-3/12 mt-4">
						<label class="text-sm opacity-75">
							Familia
							<span class="text-danger text-sm">(*)</span>
						</label>
						<v-select data-vv-scope="add-articulo" class="w-full pb-1 pt-1" v-model="selectedFamilia" :clearable="false" name="familia" data-vv-as="Familia" v-validate="'required'" :options="familias">
							<div slot="no-options">No hay opciones disponibles.</div>
						</v-select>
						<span class="text-danger text-sm">{{ errors.first('familia', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full md:w-3/12 mt-4" v-if="!(isServicio || isPaquete)" :key="uniqueKey + 'divAlmacen'">
						<label class="text-sm opacity-75">
							Almacen
							<span class="text-danger text-sm">(*)</span>
						</label>
						<v-select data-vv-scope="add-articulo" class="w-full pb-1 pt-1" v-model="selectedAlmacen" :clearable="false" name="almacenes_id" data-vv-as="Almacen" v-validate="'required'" :options="almacenes">
							<div slot="no-options">No hay opciones disponibles.</div>
						</v-select>
						<span class="text-danger text-sm">{{ errors.first('almacenes_id', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full md:w-3/12 mt-4" v-if="!(isServicio || isPaquete)" :key="uniqueKey + 'divLocalizacion'">
						<label class="text-sm opacity-75">
							Localizacion
							<span class="text-danger text-sm">(*)</span>
						</label>
						<vs-input data-vv-scope="add-articulo" v-model="articulo.localizacion" name="localizacion" data-vv-as="Localizacion" v-validate="'required'" type="text" class="w-full pb-1 pt-1"/>
						<span class="text-danger text-sm">{{ errors.first('localizacion', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full md:w-3/12 mt-4">
						<label class="text-sm opacity-75">
							Cuenta predial
							<!--<span class="text-danger text-sm">(*)</span>-->
						</label>
						<vs-input data-vv-scope="add-articulo" v-model="articulo.cuenta_predial" name="cuenta_predial" data-vv-as="cuenta predial" v-validate="'numeric'" type="text" class="w-full pb-1 pt-1" />
						<!--<span class="text-danger text-sm">{{ errors.first('nombre', 'add-articulo') }}</span>-->
					</div>
					<div class="vx-col w-full md:w-3/12 mt-4" v-if="!(isServicio || isPaquete)" :key="uniqueKey + 'divCantidad'">
						<div class="vx-row">
							<div class="vx-col w-full md:w-6/12">
								<label class="text-sm opacity-75">
									Cantidad Minima
									<span class="text-danger text-sm">(*)</span>
								</label>
								<vs-input :key="'minimo'" data-vv-scope="add-articulo" v-model="articulo.minimo" name="minimo" data-vv-as="Minimo"  v-validate="rulesIventarioMinimo" type="text" class="w-full pb-1 pt-1"/>
								<span class="text-danger text-sm">{{ errors.first('minimo', 'add-articulo') }}</span>
							</div>
							<div class="vx-col w-full md:w-6/12">
								<label class="text-sm opacity-75">
									Cantidad Maxima
									<span class="text-danger text-sm">(*)</span>
								</label>
								<vs-input :key="'maximo'" data-vv-scope="add-articulo" v-model="articulo.maximo" name="maximo" data-vv-as="Maximo"  v-validate="rulesInventarioMaximo" type="text" class="w-full pb-1 pt-1"/>
								<span class="text-danger text-sm">{{ errors.first('maximo', 'add-articulo') }}</span>
							</div>
						</div>
					</div>
					<div class="vx-col w-full" :class="isServicio || isPaquete ? 'md:w-3/12 mt-4': 'md:w-3/12 mt-12'" :key="uniqueKey + 'divOptions'">
						<ul class="centerx options">
							<li><vs-checkbox key="facturable" v-model="articulo.facturable">Facturable</vs-checkbox></li> 
							<li><vs-checkbox :key="uniqueKey + 'caduca'" v-model="articulo.caduca" v-if="!(isPaquete || isServicio)">Caduca</vs-checkbox></li>
							<li><vs-checkbox key="rentable" v-model="articulo.rentable">Rentable</vs-checkbox></li>
						</ul>
					</div>
				</div>
				<div class="vx-row mt-6">
					<div class="vx-col w-full">
						<div class="flex items-end">
							<!--<feather-icon icon="MailIcon" class="mr-2" svgClasses="w-5 h-5" />-->
							<span class="leading-none font-medium">Precios de venta</span>
						</div>
					</div>
				</div>
				<vs-divider/> 
				<div class="vx-row mt-4">
					<div class="vx-col w-full" :class="classPrices">
						<label class="text-sm opacity-75">
							Precio de compra neto
							<span class="text-danger text-sm">(*)</span>
						</label>
						<vs-input data-vv-scope="add-articulo" v-model="articulo.costo_neto" name="costo-neto" data-vv-as="Costo neto" v-validate="'required|decimal:2'" type="text" class="w-full pb-1 pt-1"/>
						<span class="text-danger text-sm">{{ errors.first('costo-neto', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full" :class="classPrices">
						<label class="text-sm opacity-75">
							Unidad de compra
							<span class="text-danger text-sm">(*)</span>
						</label>
						<v-select data-vv-scope="add-articulo" class="w-full pb-1 pt-1" v-model="selectedUnidadCompra" :clearable="false" name="unidad_compra" data-vv-as="Unidad de compra" v-validate="'required'" :options="unidades">
							<div slot="no-options">No hay opciones disponibles.</div>
						</v-select>
						<span class="text-danger text-sm">{{ errors.first('unidad_compra', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full":class="classPrices">
						<label class="text-sm opacity-75">
							Unidad de venta
							<span class="text-danger text-sm">(*)</span>
						</label>
						<v-select data-vv-scope="add-articulo" class="w-full pb-1 pt-1" v-model="selectedUnidadVenta" :clearable="false" name="unidad_venta" data-vv-as="Unidad de venta" v-validate="'required'" :options="unidades">
							<div slot="no-options">No hay opciones disponibles.</div>
						</v-select>
						<span class="text-danger text-sm">{{ errors.first('unidad_venta', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full" :class="classPrices" v-if="!(isPaquete || isServicio)" :key="uniqueKey + 'factor'">
						<label class="text-sm opacity-75">
							Factor
							<span class="text-danger text-sm">(*)</span>
						</label>
						<vs-input data-vv-scope="add-articulo" v-model="articulo.factor" name="factor" data-vv-as="Factor" v-validate="'required|numeric|min_value:1'" type="text" class="w-full"/>
						<span class="text-danger text-sm">{{ errors.first('factor', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full" :class="classPrices">
						<label class="text-sm opacity-75">
							Precio de venta
							<span class="text-danger text-sm">(*)</span>
						</label>
						<vs-input data-vv-scope="add-articulo" v-model="preciosVenta.precio1" name="precio1" data-vv-as="Precio de venta" v-validate="'required|decimal:2'" type="text" class="w-full"/>
						<span class="text-danger text-sm">{{ errors.first('precio1', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full" :class="classPrices">
						<label class="text-sm opacity-75">
							Precio de venta (descuento)
							<span class="text-danger text-sm">(*)</span>
						</label>
						<vs-input data-vv-scope="add-articulo" v-model="preciosVenta.precio2" name="precio2" data-vv-as="Precio de venta con descuento"  v-validate="'required|decimal:2'" type="text" class="w-full"/>
						<span class="text-danger text-sm">{{ errors.first('precio2', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full" :class="classPrices">
						<label class="text-sm opacity-75">
							Impuestos
							<!--<span class="text-danger text-sm">(*)</span>-->
						</label>
						<v-select data-vv-scope="add-articulo" multiple class="w-full pb-1 pt-1" v-model="selectedImpuestos" name="impuestos" data-vv-as="Impuestos" :options="impuestos">
							<div slot="no-options">No hay opciones disponibles.</div>
						</v-select>
						<span class="text-danger text-sm">{{ errors.first('impuestos', 'add-articulo') }}</span>
					</div>
					<div class="vx-col w-full" :class="classPrices">
						<label class="text-sm opacity-75">
							Retenciones
							<!--<span class="text-danger text-sm">(*)</span>-->
						</label>
						<v-select data-vv-scope="add-articulo" multiple class="w-full pb-1 pt-1" v-model="selectedRetenciones" name="retenciones" data-vv-as="Retenciones" :options="retenciones">
							<div slot="no-options">No hay opciones disponibles.</div>
						</v-select>
						<span class="text-danger text-sm">{{ errors.first('retenciones', 'add-articulo') }}</span>
					</div>
				</div>
			</div>
			<div v-show="activeTab == 1">
				<div class="vx-col w-full md:w-12/12" v-if="isPaquete" :key="uniqueKey + 'divPack'">
					<div class="vx-row">
						<div class="vx-col w-full mb-4">
							<vx-input-group>
								<v-select v-model="selectedArticuloPack" :filterable="false" placeholder="Seleccione o busque un articulo" :options="searchedArticulos" @search="onSearchArticulos">
									<div slot="no-options">No hay opciones disponibles.</div>
									<template slot="option" slot-scope="option">
										<div class="d-center"><b>{{ option.codigo_barras }}</b>: {{ option.nombre }}</div>
									</template>
    								<template slot="selected-option" slot-scope="option">
										<div class="d-center"><b>{{ option.codigo_barras }}</b>: {{ option.nombre }}</div>
    								</template>
								</v-select>
								<template slot="append">
									<div class="append-text bg-primary">
										<vs-button icon-pack="feather" icon="icon-plus" @click="addArticuloPack"></vs-button>
									</div>
								</template>
							</vx-input-group>
							<span class="text-danger text-sm">Solo se mostraran los primeros 15 articulo que coincidad con la busqueda, para encontrar un producto en especifico sugerimos usar el <b>CODIGO DE BARRAS</b></span><br/>
						</div>
						<div class="vx-col w-full">
							<span class="leading-none font-medium mt-6">Articulos incluidos en el paquete</span>
							<vs-table :hoverFlat="false" stripe noDataText="Aun no has agregado articulos al paquete" :data="paqueteProductos" class="mt-4">
								<template slot="thead">
									<vs-th>Cantidad</vs-th>
									<vs-th>Codigo de barras</vs-th>
									<vs-th>Articulo</vs-th>
									<vs-th></vs-th>
								</template>
								<template slot-scope="{data}">
									<vs-tr :data="articulo" :key="indextr" v-for="(articulo, indextr) in data">
										<vs-td>
											<vs-input :key="'articulo' + indextr" data-vv-scope="add-articulo" :name="'articulo' + indextr" v-validate="'required|numeric|min_value:1'" data-vv-as="Impuestos" v-model="articulo.cantidad" />
											<span class="text-danger text-sm">{{ errors.first('articulo' + indextr, 'add-articulo') }}</span>
										</vs-td>
										<vs-td>{{ articulo.codigo_barras }}</vs-td>
										<vs-td>{{ articulo.nombre }}</vs-td>
										<vs-td>
											<vs-button @click="onDeleteFromPackage(indextr)" title="Eliminar del paquete" icon-pack="feather" size="large" icon="icon-x" color="danger"  type="flat"></vs-button>
										</vs-td>
									</vs-tr>
								</template>
							</vs-table>
						</div>
					</div>
				</div>
			</div>
			</form>
    	</vs-popup>
  	</div>
</template>
<script>
import vSelect from 'vue-select'
import articuloService from '@services/articulos'
import _ from 'lodash'

export default {
	components: {
		vSelect
	},
    props: {
        show: {
            type: Boolean,
            required: true
		},
		articuloData: {
			type: Object
		}
	},
	computed: {
		isServicio() {
			if (this.selectedTipoProducto) {
				if ( this.selectedTipoProducto.value === 2) {
					return true
				}
			}
			return false
		},
		isPaquete() {
			if (this.selectedTipoProducto) {
				if ( this.selectedTipoProducto.value === 3) {
					return true
				}
			}
			return false
		},
		classPrices() {
			let classes = 'md:w-3/12 mt-4'
			if (this.isServicio || this.isPaquete) {
				classes = 'md:w-1/5 mt-4'
			}
			return classes
		},
        rulesInventarioMaximo() {
            if (this.articulo.minimo) {
                return 'required|numeric|min_value:' + this.articulo.minimo
            }
            return 'required|numeric'
        },
        rulesIventarioMinimo() {
            if (this.articulo.maximo) {
                return 'required|numeric|max_value:' + this.articulo.maximo
            }
            return 'required|numeric'
        },
	},
	watch: {
        show: function() {
			this.activeTab = 0
			this.showPopup = this.show
			this.uniqueKey = (new Date()).getTime()
			if (this.articuloData) {
				this.articulo = _.clone(this.articuloData)
				this.selectedUnidadCompra = {
					value: this.articulo.unidad_compra.id,
					label: this.articulo.unidad_compra.unidad
				}

				this.selectedUnidadVenta = {
					value: this.articulo.unidad_venta.id,
					label: this.articulo.unidad_venta.unidad
				}

				this.selectedTipoProducto = {
					value: this.articulo.tipo_producto.id,
					label: this.articulo.tipo_producto.tipo
				}

				this.selectedGrupoProfeco = {
					value: this.articulo.grupo_profeco.id,
					label: this.articulo.grupo_profeco.ver_nombre
				}

				this.selectedFamilia = {
					value: this.articulo.familia.id,
					label: this.articulo.familia.familia
				}

				this.selectedCategoria = {
					value: this.articulo.familia.categoria.id,
					label: this.articulo.familia.categoria.categoria
				}

				if (this.articulo.almacen) {
					this.selectedAlmacen = {
						value: this.articulo.almacen.id,
						label: this.articulo.almacen.almacen
					}
				}

				this.selectedTipoProductoSAT = {
					value: this.articulo.sat_producto_servicio.id,
					label: this.articulo.sat_producto_servicio.clave
				}

				if (this.articulo.impuestos) {
					this.selectedImpuestos = _.map(this.articulo.impuestos, impuesto => ({ value: impuesto.sat_impuesto.id, label: impuesto.sat_impuesto.impuesto }))
					this.selectedRetenciones = _.map(this.articulo.retenciones, retencion => ({ value: retencion.sat_impuesto.id, label: retencion.sat_impuesto.impuesto }))
				}

				if (this.articulo.precios.length) {
					_.forEach(this.articulo.precios, (precio) => {
						if (precio.precios_id === 1) {
							this.preciosVenta.precio1 = precio.precio
						} else if (precio.precios_id === 2) {
							this.preciosVenta.precio2 = precio.precio
						}
					})
				}

				//PAQUETE
				if (this.articulo.tipos_producto_id === 3) {
					this.paqueteProductos = _.map(this.articulo.paquete, (articuloPack) => {
						return {
							articulos_id: articuloPack.articulos_id,
							cantidad: articuloPack.cantidad,
							nombre: articuloPack.articulo.nombre,
							codigo_barras: articuloPack.articulo.codigo_barras
						}
					})
				}

				delete this.articulo.unidad_compra
				delete this.articulo.unidad_venta
				delete this.articulo.tipo_producto
				delete this.articulo.sat_producto_servicio
				delete this.articulo.familia
				delete this.articulo.almacen
				delete this.articulo.grupo_profeco
				delete this.articulo.impuestos
				delete this.articulo.retenciones
				delete this.articulo.precios

				this.tipoProductoDisabled = true
				this.title = 'MODIFICAR ARITCULO'
			}
			
			if (!this.showPopup) {
				this.title = 'NUEVO ARITCULO'
				for (let member in this.articulo) 
					this.articulo[member] = null

				this.preciosVenta.precio1 = null
				this.preciosVenta.precio2 = null

				this.selectedGrupoProfeco = null
				this.selectedUnidadVenta = null
				this.selectedUnidadCompra = null
				this.selectedTipoProducto = null
				this.selectedTipoProductoSAT = null
				this.selectedAlmacen = null
				this.selectedImpuestos = null
				this.selectedRetenciones = null
				this.selectedFamilia = null
				this.selectedCategoria = null

				this.tipoProductoDisabled = false
				delete this.articulo.id
				this.articulo.status = 1

				let matcher = {
					scope: 'add-articulo',
					vmId: this.$validator.id
				}
            	this.$validator.reset(matcher)
			} else {
			}
		},
		selectedTipoProducto: function() {
			if (this.selectedTipoProducto) {
				if (this.selectedTipoProducto.value === 2) {
					this.showField = false
				} else {
					this.showField = true
				}

				if (this.selectedTipoProducto.value === 2 || this.selectedTipoProducto.value === 1) {
					this.paqueteProductos = []
				}
			}
		}
	},
	data() {
		return {
			uniqueKey: 0,
			currentTimestamp: 1,
			title: 'NUEVO ARTICULO',
			activeTab: 2,
			tipoProductoDisabled: false,
			showField: true,
			showPopup: false,
			unidades: [],
			almacenes: [],
			impuestos: [],
			retenciones: [],
			tiposProductos: [],
			tiposProductosSAT: [],
			gruposProfeco: [],
			familias: [],
			categorias: [],
			searchedArticulos: [],
			selectedArticuloPack: null,
			selectedGrupoProfeco: null,
			selectedUnidadVenta: null,
			selectedUnidadCompra: null,
			selectedTipoProducto: null,
			selectedTipoProductoSAT: null,
			selectedAlmacen: null,
			selectedImpuestos: null,
			selectedRetenciones: null,
			selectedFamilia: null,
			selectedCategoria: null,
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
			},
			preciosVenta: {
				precio1: null,
				precio2: null
			},
			paqueteProductos: []
		}
    },
    methods: {
		onDeleteFromPackage(index) {
			this.paqueteProductos.splice(index, 1)
		},
		addArticuloPack() {
			let articulo = _.clone(this.selectedArticuloPack)
			this.selectedArticuloPack = null
			let exists = _.find(this.paqueteProductos, (o) => o.articulos_id === articulo.id)
			if (!exists) {
				this.paqueteProductos.push({
					articulos_id: articulo.id,
					cantidad: 0,
					nombre: articulo.nombre,
					codigo_barras: articulo.codigo_barras
				})
			} else {				
				this.$vs.notify({
					color: "warning",
					position: "top-center",
					title: "Advertencia",
					text: "El articulo ya es parte del paquete"
				});
			}
		},
		onSearchArticulos(search, loading) {
			loading(true);
			this.search(loading, search, this);
		},
		search: _.debounce((loading, search, vm) => {
			if (!search) {
				vm.searchedArticulos = []
				loading(false)
			} else {
				articuloService.getAll({
					page: 1,
					per_page: 15,
					search: search,
					tipo_producto: 1
				}).then(res => {
					vm.searchedArticulos = res.data.data//_.map(res.data.data, (articulo) => ({ value: articulo.id, label: articulo.nombre }))
					loading(false)
				})
			}
		}, 350),
		save() {
			let self = this
            self.$validator.validate('add-articulo.*').then(result => {
				if (result) {
					self.articulo.familias_id = self.selectedFamilia.value
					self.articulo.unidades_compra_id = self.selectedUnidadCompra.value
					self.articulo.unidades_venta_id = self.selectedUnidadVenta.value
					self.articulo.sat_productos_servicios_id = self.selectedTipoProductoSAT.value
					self.articulo.tipos_producto_id = self.selectedTipoProducto.value
					self.articulo.grupos_profeco_id = self.selectedGrupoProfeco.value
					self.articulo.impuestos = _.map(self.selectedImpuestos, (impuesto) => impuesto.value)
					self.articulo.retenciones = _.map(self.selectedRetenciones, (retencion) => retencion.value)

					self.articulo.facturable = self.articulo.facturable || 0
					self.articulo.caduca = self.articulo.caduca || 0
					self.articulo.rentable = self.articulo.rentable || 0

					if (!(self.isServicio || self.isPaquete)) {
						self.articulo.almacenes_id = self.selectedAlmacen.value
					}

					if (self.isPaquete && self.paqueteProductos.length === 0) {
						self.$vs.notify({
							color: "warning",
							position: "top-center",
							title: "Advertencia",
							text: "Estas creando un paquete pero aun no has agregado ningun articulo a este, porfavor ve a la pestaña Articulos del paquete para agregar al menos un articulo"
						});
						return false
					}

					let articuloData = _.clone(self.articulo)
					let data = {
						articulo: articuloData,
						precios: self.preciosVenta,
						paqueteArticulos: self.paqueteProductos
					}

					let promise
					if (self.articulo.id) {
						let id = data.articulo.id
						promise = articuloService.update(id, data)
					} else {
						promise = articuloService.create(data)
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
				} else {
					self.$vs.notify({
						color: "warning",
						position: "top-center",
						title: "Advertencia",
						text: "Se han detectado errores de validacion, porfavor revisa todos los campos"
					});
				}
			})
		},
		getFamilias() {
			this.selectedFamilia = null
			return articuloService.getFamilias(this.selectedCategoria.value).then((response) => this.familias = response.data.data)
		},
        cancel() {
			this.$emit('update:show', false)
			this.$emit('on-cancel')
        }
    },
    created() { 
		this.$emit('update:show', JSON.parse(this.show))
		let self = this
      	self.$vs.loading()
		Promise.all([
			articuloService.tiposProductos().then((response) => this.tiposProductos = response.data.data),
			articuloService.grouposProfeco().then((response) => this.gruposProfeco = response.data.data),
			articuloService.almacenes().then((response) => this.almacenes = response.data.data),
			articuloService.categorias().then((response) => this.categorias = response.data.data),
			articuloService.impuestos().then((response) => this.impuestos = response.data.data),
			articuloService.retenciones().then((response) => this.retenciones = response.data.data),
			articuloService.unidades().then((response) => this.unidades = response.data.data),
			articuloService.productosServicios().then((response) => this.tiposProductosSAT = response.data.data)
		]).then(allPromises => {
			self.$vs.loading.close()
		})
    }
}
</script>
<style>
.options li{
	display: inline-block;
}

[dir] .tab-action-btn-fill-conatiner.con-vs-tabs .vs-tabs--content {
  height: 12px;
  padding: 0px !important;
  padding-bottom: 2px !important;
}
</style>
