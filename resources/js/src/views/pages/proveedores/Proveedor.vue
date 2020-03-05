<template>
	<div class="centerx">
    	<vs-popup class="usuarios" close="cancelar" title="Proveedor" :active="showPopup" button-close-hidden >
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
					<vs-input name="Nombre" icon-pack="feather" icon="icon-book"  data-vv-validate-on="blur" v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el nombre comercial"/>
				</div>
				<div class="vx-col w-full md:w-4/12">
					<label class="text-sm opacity-75">
						Razon Social
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input name="Nombre" icon-pack="feather" icon="icon-book-open" data-vv-validate-on="blur" v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese la razon social"/>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						RFC
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input name="Nombre" icon-pack="feather" icon="icon-trello" data-vv-validate-on="blur" v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el RFC"/>
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
					<vs-input name="Nombre" icon-pack="feather" icon="icon-user" data-vv-validate-on="blur" v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el nombre del contacto"/>
				</div>
				<div class="vx-col w-full md:w-4/12">
					<label class="text-sm opacity-75">
						Telefono de contacto
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input name="Nombre" icon-pack="feather" icon="icon-phone" data-vv-validate-on="blur" v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el telefono de contacto"/>
				</div>
				<div class="vx-col w-full md:w-3/12">
					<label class="text-sm opacity-75">
						Correo electronico
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input name="Nombre" icon-pack="feather" icon="icon-at-sign" data-vv-validate-on="blur" v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el correo electronico"/>
				</div>
			</div>
			<div class="vx-row w-full md:mt-4">
				<div class="vx-col w-full md:w-5/12">
					<label class="text-sm opacity-75">
						Sitio WEB
					</label>
					<vs-input name="Nombre" icon-pack="feather" icon="icon-chrome" data-vv-validate-on="blur" v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el sitio web"/>
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
					<vs-input icon-pack="feather" icon="icon-map" name="pais" data-vv-validate-on="blur" v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el pais"/>
					<span class="text-danger text-sm" v-show="errors.has('estado')">{{ errors.first('pais') }}</span>
				</div>
				<div class="vx-col w-full md:w-1/3">
					<label for class="vs-input--label">Estado
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input icon-pack="feather" icon="icon-map" name="Nombre" data-vv-validate-on="blur" v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese el estado"/>
					<span class="text-danger text-sm" v-show="errors.has('estado')">{{ errors.first('estado') }}</span>
				</div>
				<div class="vx-col w-full md:w-1/3">
					<label for class="vs-input--label">Ciudad
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input icon-pack="feather" icon="icon-map" name="ciudad" data-vv-validate-on="blur" v-validate="'required'" type="text" class="w-full pb-1 pt-1" placeholder="Ingrese la ciudad"/>
					<span class="text-danger text-sm" v-show="errors.has('ciudad')">{{ errors.first('ciudad') }}</span>
				</div>
			</div>
			<div class="vx-row md:mt-4">
				<div class="vx-col w-full md:w-4/12">
					<label for class="vs-input--label">Calle
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input
						icon-pack="feather"
						icon="icon-map"
						name="direccion_calle"
						data-vv-as="Calle"
						v-validate="'required'"
						class="w-full"
					/>
					<span
						class="text-danger text-sm"
						v-show="errors.has('direccion_calle')"
					>{{ errors.first('direccion_calle') }}</span>
				</div>
				<div class="vx-col w-full md:w-2/12">
					<label for class="vs-input--label">Num. Ext.
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input
						icon-pack="feather"
						icon="icon-map"
						name="direccion_numero"
						data-vv-as="Numero"
						v-validate="'required'"
						placeholder="Numero exterior"
						class="w-full"
					/>
					<span
						class="text-danger text-sm"
						v-show="errors.has('direccion_calle')"
					>{{ errors.first('direccion_numero') }}</span>
				</div>
				<div class="vx-col w-full md:w-2/12">
					<label for class="vs-input--label">Num. Int.
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input
						icon-pack="feather"
						icon="icon-map"
						name="direccion_numero"
						data-vv-as="Numero"
						v-validate="'required'"
						placeholder="Numero Interior"
						class="w-full"
					/>
				</div>
				<div class="vx-col w-full md:w-4/12">
					<label for class="vs-input--label">Colonia
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input
						icon-pack="feather"
						icon="icon-map"
						name="colonia"
						data-vv-as="Colonia"
						v-validate="'required'"
						class="w-full"
					/>
					<span
						class="text-danger text-sm"
						v-show="errors.has('colonia')"
					>{{ errors.first('colonia') }}</span>
				</div>
			</div>
			<div class="vx-row md:mt-4">
				<div class="vx-col w-full md:w-2/12">
					<label for class="vs-input--label">C.P.
						<span class="text-danger text-sm">(*)</span>
					</label>
					<vs-input
						icon-pack="feather"
						icon="icon-map"
						name="cp"
						data-vv-as="C.P."
						v-validate="'required|alpha_num'"
						class="w-full"
					/>
					<span
						class="text-danger text-sm"
						v-show="errors.has('cp')"
					>{{ errors.first('cp') }}</span>
				</div>
			</div>
    	</vs-popup>
  	</div>
</template>
<script>
export default {
    props: {
        show: {
            type: Boolean,
            required: true
        }
    },
	watch: {
        show: function() {
            console.log('qweqwe')
            this.showPopup = this.show
        },
	},
	data() {
		return {
            showPopup: false
		}
    },
    methods: {
        cancel() {
            this.$emit('update:show', false)
        },
        guardar() {
        }
    },
    created() {
        this.$emit('update:show', JSON.parse(this.show))
    }
}
</script>