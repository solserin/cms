<template>
	<div>
		<vx-card ref="filterCard" title="Filtros de seleccion">
			<div class="flex flex-wrap">
				<div class="w-full">
					<vs-button icon-pack="feather" icon="icon-user-plus" color="success" size="small" class="float-right" @click="showProveedor = true">Agregar</vs-button>
					<vs-button icon-pack="feather" icon="icon-printer" color="primary" size="small" class="float-right mr-3">PDF</vs-button>
				</div>
			</div>
			<div class="flex flex-wrap">
				<div class="w-full sm:w-10/12 md:w-10/12 lg:w-10/12 xl:w-10/12 mb-4 px-2">
					<label class="text-sm opacity-75">Buscar:</label>
					<vs-input class="w-full" icon="search" placeholder="Filtrar cualquier columna"/>
				</div>
				<div class="w-full sm:w-2/12 md:w-2/12 lg:w-2/12 xl:w-2/12 mb-4 px-2">
					<label class="text-sm opacity-75">Mostrar</label>
					<v-select :options="mostrarOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'" v-model="mostrar" class="mb-4 sm:mb-0" />
				</div>
			</div>
		</vx-card>
		<vx-card title="Listado de proveedores" class="mt-5">
			<vs-table class="mt-5" :max-items="serverOptions.per_page.value" :data="proveedores" stripe noDataText="0 Resultados">
				<template slot="thead">
					<vs-th>Clave</vs-th>
					<vs-th>Nombre comercial</vs-th>
					<vs-th>Razon social</vs-th>
					<vs-th>RFC</vs-th>
					<vs-th>Contacto</vs-th>
					<vs-th>Telefono</vs-th>
				</template>
				<template slot-scope="{data}">
					<vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
						<vs-td>{{tr.id}}</vs-td>
						<vs-td>{{tr.nombre_comercial}}</vs-td>
						<vs-td>{{tr.razon_social}}</vs-td>
						<vs-td>{{tr.rfc}}</vs-td>
						<vs-td>{{tr.nombre_contacto}}</vs-td>
						<vs-td>{{tr.telefono}}</vs-td>
					</vs-tr>
				</template>
			</vs-table>
			<div>
				<vs-pagination v-if="ver" :total="this.total" v-model="actual" class="mt-8"></vs-pagination>
			</div>
		</vx-card>
		<Proveedor :show.sync="showProveedor" />
	</div>
</template>
<script>
import { mostrarOptions } from "@/VariablesGlobales";

import vSelect from 'vue-select'
import Proveedor from './Proveedor'
export default {
	components: {
        vSelect,
        Proveedor
	},
	data() {
		return {
            showProveedor: false,
            mostrar: {},
            actual: 1,
            mostrarOptions: mostrarOptions,
            ver: true,
            total: 1,
            serverOptions: {
                page: "",
                per_page: 15,
                status: "",
                rol_id: "",
                nombre: ""
            },
            proveedores: []
		}
	}
}
</script>