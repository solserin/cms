<template>
	<div>
		<vx-card ref="filterCard" title="Filtros de seleccion">
			<div class="flex flex-wrap mb-3">
				<div class="w-full">
					<vs-button icon-pack="feather" icon="icon-user-plus" color="success" size="small" class="float-right"  @click="showArticulo = true">Agregar</vs-button>
					<vs-button icon-pack="feather" icon="icon-user-plus" color="success" size="small" class="float-right mr-3" @click="showCompra = true">Compra</vs-button>
					<vs-button icon-pack="feather" icon="icon-printer" color="primary" size="small" class="float-right mr-3" @click="openPDF">PDF</vs-button>
				</div>
			</div>
			<div class="flex flex-wrap">
				<div class="w-full md:w-1/6 mb-4 px-2">
					<label class="text-sm opacity-75">Mostrar</label>
					<v-select :options="mostrarOptions" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="mostrar" class="mb-4 sm:mb-0" />
				</div>
				<div class="w-full md:w-1/6 mb-4 px-2">
					<label class="text-sm opacity-75">Tipo de producto:</label>
					<v-select @input="customFilter" v-model="selectedTipoProducto" :clearable="true" :options="tiposProductos">
						<div slot="no-options">No hay opciones disponibles.</div>
					</v-select>
				</div>
				<div class="w-full md:w-1/6 mb-4 px-2">
					<label class="text-sm opacity-75">Grupo profeco:</label>
					<v-select @input="customFilter" v-model="selectedGrupoProfeco" :clearable="true" :options="gruposProfeco">
						<div slot="no-options">No hay opciones disponibles.</div>
					</v-select>
				</div>
				<div class="w-full md:w-1/6 mb-4 px-2">
					<label class="text-sm opacity-75">Almacen:</label>
					<v-select @input="customFilter" v-model="selectedAlmacen" :clearable="true" :options="almacenes">
						<div slot="no-options">No hay opciones disponibles.</div>
					</v-select>
				</div>
				<div class="w-full md:w-1/6 mb-4 px-2">
					<label class="text-sm opacity-75">Categoria:</label>
					<v-select v-model="selectedCategoria" :clearable="true" :options="categorias" @input="getFamilias">
						<div slot="no-options">No hay opciones disponibles.</div>
					</v-select>
				</div>
				<div class="w-full md:w-1/6 mb-4 px-2">
					<label class="text-sm opacity-75">Familia:</label>
					<v-select @input="customFilter" v-model="selectedFamilia" :clearable="true" :options="familias">
						<div slot="no-options">No hay opciones disponibles.</div>
					</v-select>
				</div>
			</div>
			<div class="flex flex-wrap">
				<div class="w-full md:w-11/12 mb-4 px-2">
					<label class="text-sm opacity-75">Buscar:</label>
					<vs-input v-model="serverOptions.search" v-on:keyup.enter="loadArticulos(true)" v-on:blur="loadArticulos(true)" class="w-full" icon="search" placeholder="Filtrar cualquier columna"/>
				</div>
				<div class="w-full md:w-1/12 lg:w-1/12 xl:w-1/12 mb-4">
					<vs-button type="border" size="small" @click="reset" color="primary" line-position="top" class="mt-8">Resetear</vs-button>
				</div>
			</div>
		</vx-card>
		<vs-table class="mt-5" :max-items="serverOptions.per_page.value" :data="articulos" stripe noDataText="0 Resultados">

			<template slot="header">
				<h3 class="pb-5 text-primary">Listado de Articulos</h3>
			</template>
			<template slot="thead">
				<vs-th>Codigo de barras</vs-th>
				<vs-th>Tipo de producto</vs-th>
				<vs-th>Nombre</vs-th>
				<vs-th>Almacen</vs-th>
				<vs-th>Localizacion</vs-th>
				<vs-th>Existencia</vs-th>
				<vs-th>Acciones</vs-th>
			</template>
			<template slot-scope="{data}">
				<vs-tr :data="articulo" :key="indextr" v-for="(articulo, indextr) in data">
					<vs-td>{{ articulo.codigo_barras }}</vs-td>
					<vs-td>{{ articulo.tipo_producto.tipo }}</vs-td>
					<vs-td>{{ articulo.nombre }}</vs-td>
					<vs-td>{{ articulo.almacen ? articulo.almacen.almacen : 'N/A' }}</vs-td>
					<vs-td>{{ articulo.almacen ? articulo.localizacion : 'N/A' }}</vs-td>
					<vs-td>{{ articulo.tipos_producto_id === 3 || articulo.tipos_producto_id === 2 ? 'N/A' : articulo.existencia }}</vs-td>
					<vs-td>                                    
						<div class="flex flex-start">
							<vs-button title="Editar" size="large" icon-pack="feather" icon="icon-edit" color="dark" type="flat" @click="editArticulo(articulo)"></vs-button>
							<vs-button v-if="articulo.status === 1" title="Desactivar" icon-pack="feather" size="large" icon="icon-shield-off" color="danger"  type="flat" @click="changeStatus(articulo, 2)"></vs-button>
							<vs-button v-if="articulo.status === 2" title="Activar" icon-pack="feather" size="large" icon="icon-shield" color="success" type="flat" @click="changeStatus(articulo, 1)"></vs-button>
							<vs-button title="PDF" size="large" icon-pack="feather" icon="icon-download" color="dark" type="flat" @click="articuloPDF(articulo)"></vs-button>
						</div>
					</vs-td>
				</vs-tr>
			</template>
		</vs-table>
		<div>
			<vs-pagination v-show="showPaginate" :total="totalPages" v-model="serverOptions.page" class="mt-8"></vs-pagination>
		</div>
		<Articulo :articulo-data="currentArticulo" @on-cancel="cancelArticulo" :show.sync="showArticulo" @on-close="closedArticulo" />
		<Compra :show.sync="showCompra" />
    	<Password :show="openStatus" :callback-on-success="callback" @closeVerificar="closeStatus" :accion="accionNombre" />
		<PDFViewer :show="showPDF" :pdf="PDFLink" @closePdf="showPDF = false" />
	</div>
</template>
<script>
import { mostrarOptions } from "@/VariablesGlobales";

import vSelect from 'vue-select'
import Articulo from './Articulo'
import Compra from './Compra'
import articuloService from '@services/articulos'
import Password from '@/views/pages/confirmar_password'
import PDFViewer from "@/views/pages/pdf_viewer";
import _ from 'lodash'

export default {
	components: {
        vSelect,
		Articulo,
		Compra,
		Password,
		PDFViewer
	},
	data() {
		return {
			selectedGrupoProfeco: null,
			selectedTipoProducto: null,
			selectedAlmacen: null,
			selectedFamilia: null,
			selectedCategoria: null,
			showCompra: false,
			showPDF: false,
			PDFLink: '',
			accionNombre: '',
			callback: Function,
			openStatus: false,
			currentArticulo: null,
            showArticulo: false,
			mostrar: null,
			estado: null,
            mostrarOptions: mostrarOptions,
            showPaginate: true,
            totalPages: 1,
            serverOptions: {
                page: 1,
                per_page: 15,
				search: "",
				estado: null,
				tipo_producto: null,
				grupo_profeco: null,
				almacen: null,
				familia: null
            },
			articulos: [],
			tiposProductos: [],
			gruposProfeco: [],
			almacenes: [],
			categorias: [],
			familias: [],
			estadosOptions: [
				{
					value: null,
					label: 'Todos'
				},
				{
					value: 1,
					label: 'Activo'
				}, {
					value: 2,
					label: 'Inactivo'
				}
			]
		}
	},
	watch: {
		mostrar: function(newValue, oldValue) {
			if (newValue) {
				this.serverOptions.per_page = this.mostrar.value
			} else {
				this.serverOptions.per_page = 15
			}
			this.loadArticulos(true);
		},
		estado: function(newValue, oldValue) {
			if (newValue) {
				this.serverOptions.estado = this.estado.value
			} else {
				this.serverOptions.estado = null
			}
			this.loadArticulos(true);
		},
        'serverOptions.page': function (newValue, oldValue) {
            this.loadArticulos(false)
        }
	},
	methods: {
		customFilter() {
			this.serverOptions.tipo_producto = this.selectedTipoProducto ? this.selectedTipoProducto.value : null
			this.serverOptions.grupo_profeco = this.selectedGrupoProfeco ? this.selectedGrupoProfeco.value : null
			this.serverOptions.almacen = this.selectedAlmacen ? this.selectedAlmacen.value : null
			this.serverOptions.familia = this.selectedFamilia ? this.selectedFamilia.value : null
            this.loadArticulos(true)
		},
		getFamilias() {
			this.selectedFamilia = null
			if (!this.selectedCategoria) {
				this.familias = []
                this.customFilter()
				return false
			}
			return articuloService.getFamilias(this.selectedCategoria.value).then((response) => {
				this.familias = response.data.data
                this.loadArticulos(true)
			})
		},
		cancelArticulo() {
			this.currentArticulo = null
		},
		closedArticulo() {
			this.currentArticulo = null
			this.loadArticulos(true)
		},
		loadArticulos(resetPage) {
            let self = this
            if (articuloService.cancel) {
                articuloService.cancel()
            }
			
            if (resetPage) {
                self.serverOptions.page = 1
			}

			self.showPaginate = false
			articuloService.getAll(this.serverOptions).then((response) => {
				if (response.status === 200) {
					self.articulos = response.data.data
					self.totalPages = response.data.last_page
					self.showPaginate = true
				}
			})
		},
		editArticulo(articulo) {
			this.showArticulo = true
			this.currentArticulo = _.clone(articulo)
		},
		changeStatus(articulo, status) {
			let self = this
			let mArticulo = {
				id: _.clone(articulo.id),
				status: status
			}

			if (status == 2) {
				self.accionNombre = 'Desactivar Articulo'
			} else {
				self.accionNombre = 'Activar Articulo'
			}
			self.callback = function () {
      			self.$vs.loading();
				articuloService.update(mArticulo.id, mArticulo).then(response => {
          			self.$vs.loading.close();
					if (response.data.articulo > 0) {
						self.$vs.notify({
							title: self.accionNombre,
							text: "Se ha realizado la accion exitosamente",
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "success",
							time: 4000
						});
						self.loadArticulos()
					} else {
						self.$vs.notify({
							title: self.accionNombre,
							text: "No se realizaron cambios.",
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "primary",
							time: 4000
						});
					}
				}).catch(error => {
					self.$vs.loading.close();
					self.$vs.notify({
						title: self.accionNombre,
						text: "No se realizaron cambios.",
						iconPack: "feather",
						icon: "icon-alert-circle",
						color: "primary",
						time: 4000
					});
				})
			}

			this.openStatus = true

		},
		closeStatus() {
			this.openStatus = false
		},
		openPDF() {
			this.serverOptions.tipo_producto = this.selectedTipoProducto ? this.selectedTipoProducto.value : null
			this.serverOptions.grupo_profeco = this.selectedGrupoProfeco ? this.selectedGrupoProfeco.value : null
			this.serverOptions.almacen = this.selectedAlmacen ? this.selectedAlmacen.value : null
			this.serverOptions.familia = this.selectedFamilia ? this.selectedFamilia.value : null
			let options = []
			if (this.serverOptions.tipo_producto !== null) {
				options.push('tipo_producto=' + this.serverOptions.tipo_producto)
			}
			if (this.serverOptions.grupo_profeco !== null) {
				options.push('grupo_profeco=' + this.serverOptions.grupo_profeco)
			}
			if (this.serverOptions.almacen !== null) {
				options.push('almacen=' + this.serverOptions.almacen)
			}
			if (this.serverOptions.familia !== null) {
				options.push('familia=' + this.serverOptions.familia)
			}

			let optionstr = options.join('&')

			this.PDFLink = '/empresa/inventario/articulos-pdf?' + 'nombre=' + this.serverOptions.search + '&' + optionstr
			this.showPDF = true
		},
		articuloPDF(articulo) {
			this.PDFLink = '/empresa/inventario/articulos-pdf/' + articulo.id
			this.showPDF = true
		},
		reset() {
			this.estado = null
			this.mostrar = null
			this.serverOptions.search = ''

			this.selectedTipoProducto = null
			this.selectedGrupoProfeco = null
			this.selectedAlmacen = null
			this.selectedCategoria = null
			this.selectedFamilia = null
			this.customFilter()
			this.loadArticulos(true)
		}
	},
	created: function () {
		let self = this
      	self.$vs.loading()
		Promise.all([
			articuloService.tiposProductos().then((response) => this.tiposProductos = response.data.data),
			articuloService.grouposProfeco().then((response) => this.gruposProfeco = response.data.data),
			articuloService.almacenes().then((response) => this.almacenes = response.data.data),
			articuloService.categorias().then((response) => this.categorias = response.data.data)
		]).then(allPromises => {
			self.$vs.loading.close()
		})
		this.loadArticulos(true)
	}
}
</script>