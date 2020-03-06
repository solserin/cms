<template>
	<div>
		<vx-card ref="filterCard" title="Filtros de seleccion">
			<div class="flex flex-wrap">
				<div class="w-full">
					<vs-button icon-pack="feather" icon="icon-user-plus" color="success" size="small" class="float-right" @click="showProveedor = true">Agregar</vs-button>
					<vs-button icon-pack="feather" icon="icon-printer" color="primary" size="small" class="float-right mr-3" @click="openPDF">PDF</vs-button>
				</div>
			</div>
			<div class="flex flex-wrap">
				<div class="w-full md:w-2/12 lg:w-2/12 xl:w-2/12 mb-4 px-2">
					<label class="text-sm opacity-75">Mostrar</label>
					<v-select :options="mostrarOptions" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="mostrar" class="mb-4 sm:mb-0" />
				</div>
				<div class="w-full md:w-2/12 lg:w-2/12 xl:w-2/12 mb-4 px-2">
					<label class="text-sm opacity-75">Estado</label>
					<v-select :options="estadosOptions" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="estado" class="mb-4 md:mb-0" />
				</div>
				<div class="w-full md:w-7/12 mb-4 px-2">
					<label class="text-sm opacity-75">Buscar:</label>
					<vs-input v-model="serverOptions.search" v-on:keyup.enter="loadProveedores(true)" v-on:blur="loadProveedores(true)" class="w-full" icon="search" placeholder="Filtrar cualquier columna"/>
				</div>
				<div class="w-full md:w-1/12 lg:w-1/12 xl:w-1/12 mb-4">
					<vs-button type="border" size="small" @click="reset" color="primary" line-position="top" class="mt-8">Resetear</vs-button>
				</div>
			</div>
		</vx-card>
		<vs-table class="mt-5" :max-items="serverOptions.per_page.value" :data="proveedores" stripe noDataText="0 Resultados">

			<template slot="header">
				<h3 class="pb-5 text-primary">Listado de Usuarios</h3>
			</template>
			<template slot="thead">
				<vs-th>Clave</vs-th>
				<vs-th>Nombre comercial</vs-th>
				<vs-th>Razon social</vs-th>
				<vs-th>RFC</vs-th>
				<vs-th>Contacto</vs-th>
				<vs-th>Telefono</vs-th>
				<vs-th>Acciones</vs-th>
			</template>
			<template slot-scope="{data}">
				<vs-tr :data="proveedor" :key="indextr" v-for="(proveedor, indextr) in data">
					<vs-td>{{proveedor.id}}</vs-td>
					<vs-td>{{proveedor.nombre_comercial}}</vs-td>
					<vs-td>{{proveedor.razon_social}}</vs-td>
					<vs-td>{{proveedor.rfc}}</vs-td>
					<vs-td>{{proveedor.nombre_contacto}}</vs-td>
					<vs-td>{{proveedor.telefono}}</vs-td>
					<vs-td>                                    
						<div class="flex flex-start">
							<vs-button title="Editar" size="large" icon-pack="feather" icon="icon-edit" color="dark" type="flat" @click="editProveedor(proveedor)"></vs-button>
							<vs-button v-if="proveedor.status === 1" title="Desactivar" icon-pack="feather" size="large" icon="icon-shield-off" color="danger"  type="flat" @click="changeStatus(proveedor, 2)"></vs-button>
							<vs-button  v-if="proveedor.status === 2" title="Activar" icon-pack="feather" size="large" icon="icon-shield" color="success" type="flat" @click="changeStatus(proveedor, 1)"></vs-button>
						</div>
					</vs-td>
				</vs-tr>
			</template>
		</vs-table>
		<div>
			<vs-pagination v-show="showPaginate" :total="totalPages" v-model="serverOptions.page" class="mt-8"></vs-pagination>
		</div>
		<Proveedor :proveedor-data="currentProveedor" @on-cancel="cancelProveedor" :show.sync="showProveedor" @on-close="closedProveedor" />
    	<Password :show="openStatus" :callback-on-success="callback" @closeVerificar="closeStatus" :accion="accionNombre" />
		<PDFViewer :show="showPDF" :pdf="PDFLink" @closePdf="showPDF = false" />
	</div>
</template>
<script>
import { mostrarOptions } from "@/VariablesGlobales";

import vSelect from 'vue-select'
import Proveedor from './Proveedor'
import proveedorService from '@services/proveedores'
import Password from '@/views/pages/confirmar_password'
import PDFViewer from "@/views/pages/pdf_viewer";
import _ from 'lodash'

export default {
	components: {
        vSelect,
		Proveedor,
		Password,
		PDFViewer
	},
	data() {
		return {
			showPDF: false,
			PDFLink: '',
			accionNombre: '',
			callback: Function,
			openStatus: false,
			currentProveedor: null,
            showProveedor: false,
			mostrar: null,
			estado: null,
            mostrarOptions: mostrarOptions,
            showPaginate: true,
            totalPages: 1,
            serverOptions: {
                page: 1,
                per_page: 15,
				search: "",
				estado: null
            },
			proveedores: [],
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
			this.loadProveedores(true);
		},
		estado: function(newValue, oldValue) {
			if (newValue) {
				this.serverOptions.estado = this.estado.value
			} else {
				this.serverOptions.estado = null
			}
			this.loadProveedores(true);
		},
        'serverOptions.page': function (newValue, oldValue) {
            this.loadProveedores(false)
        }
	},
	methods: {
		cancelProveedor() {
			this.currentProveedor = null
		},
		closedProveedor() {
			this.currentProveedor = null
			this.loadProveedores(true)
		},
		loadProveedores(resetPage) {
            let self = this
            if (proveedorService.cancel) {
                proveedorService.cancel()
            }

            if (resetPage) {
                self.serverOptions.page = 1
			}

			self.showPaginate = false
			proveedorService.getAll(this.serverOptions).then((response) => {
				if (response.status === 200) {
					self.proveedores = response.data.data
					self.totalPages = response.data.last_page
					self.showPaginate = true
				}
			})
		},
		editProveedor(proveedor) {
			this.showProveedor = true
			this.currentProveedor = _.clone(proveedor)
		},
		changeStatus(proveedor, status) {
			let self = this
			let mProveedor = {
				id: _.clone(proveedor.id),
				status: status
			}

			if (status == 2) {
				self.accionNombre = 'Desactivar Proveedor'
			} else {
				self.accionNombre = 'Activar Proveedor'
			}
			self.callback = function () {
      			self.$vs.loading();
				proveedorService.update(mProveedor.id, mProveedor).then(response => {
          			self.$vs.loading.close();
					if (response.data.proveedor > 0) {
						self.$vs.notify({
							title: self.accionNombre,
							text: "Se ha realizado la accion exitosamente",
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "success",
							time: 4000
						});
						self.loadProveedores()
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
			this.PDFLink = '/empresa/inventario/proveedores-pdf?' + 'nombre=' + this.serverOptions.search + '&estado=' + ((this.serverOptions.estado === null || this.serverOptions.estado === 0) ? '' : this.serverOptions.estado)
			this.showPDF = true
		},
		reset() {
			this.estado = null
			this.mostrar = null
			this.serverOptions.search = ''
			this.loadProveedores(true)
		}
	},
	created: function () {
		this.loadProveedores(true)
	}
}
</script>