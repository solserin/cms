<template>
    <div class="centerx">
        <vs-popup fullscreen  close="cancelar" :title="title" :active="showPopup" button-close-hidden>
            <div class="vx-row">
                <div class="vx-col w-full">
                    <vs-button
                    @click="save"
                    icon-pack="feather"
                    icon="icon-save"
                    color="success"
                    size="small"
                    class="float-right"
                    >Guardar</vs-button>
                    <vs-button
                    @click="cancel"
                    icon-pack="feather"
                    icon="icon-x"
                    color="danger"
                    size="small"
                    class="float-right mr-5"
                    >Cancelar</vs-button>
                </div>
            </div>
            <div class="vx-row w-full">
                <div class="vx-col w-full">
                    <div class="flex items-end">
                        <feather-icon icon="ArchiveIcon" class="mr-2" svgClasses="w-5 h-5" />
                        <span class="leading-none font-medium">Informacion basica</span>
                    </div>
                </div>
            </div>
            <vs-divider />
            <div class="vx-row mb-4 mt-3">
                <div class="vx-col w-full md:w-3/12">
                    <label for="" class="vs-input--label">Fecha*:</label>
                    <datepicker
                        data-vv-scope="compra"
                        :language="spanishDatepicker" 
                        :disabled-dates="disabledDates" 
                        name="fecha_tnep" 
                        data-vv-as="Fecha de la compra"  
                        v-validate="'required'" 
                        format="yyyy-MM-dd" 
                        placeholder="Seleccionar fecha" 
                        v-model="fecha">
                    </datepicker>
                    <span class="text-danger text-sm" v-show="errors.has('fecha_tnep', 'compra')">{{ errors.first('fecha_tnep', 'compra') }}</span>
                </div>
                <div class="vx-col w-full md:w-6/12">
                    <label for="" class="vs-input--label">Proveedor:</label>
                    <v-select
                        data-vv-scope="compra"
                        v-model="selectedProveedor" 
                        :clearable="false" 
                        name="proveedor" 
                        data-vv-as="Proveedor" 
                        v-validate="'required'" 
                        placeholder="Seleccione un proveedor" 
                        :options="proveedores">
                        <div  slot="no-options">No hay opciones disponibles.</div>
                    </v-select>
                    <span class="text-danger text-sm" v-show="errors.has('proveedor', 'compra')">{{ errors.first('proveedor', 'compra') }}</span>
                </div>
                <div class="vx-col w-full md:w-3/12">                
                    <vx-tooltip color="dark" text="Referencia a la factura de esta compra">
                        <vs-input data-vv-scope="compra" v-model="compra.referencia_factura" class="w-full uppercase" icon-pack="feather" icon="icon-book" data-vv-as="Referencia de factura" v-validate="'required'" label="Referencia de factura*:" name="referencia_factura"/>
                    </vx-tooltip>
                    <span class="text-danger text-sm" v-show="errors.has('referencia_factura', 'compra')">{{ errors.first('referencia_factura', 'compra') }}</span>
                </div>
            </div>
            <div class="vx-row mb-4 mt-3">
                <div class="vx-col w-full md:w-3/12">
                    <label for="" class="vs-input--label">Metodo de pago:</label>
                    <v-select 
                        data-vv-scope="compra"
                        v-model="selectedMetodo" 
                        :clearable="false" 
                        name="metodo" 
                        data-vv-as="Meto de pago" 
                        v-validate="'required'" 
                        placeholder="Seleccione un metodo de pago" 
                        :options="metodosPago">
                        <div  slot="no-options">No hay opciones disponibles.</div>
                    </v-select>
                    <span class="text-danger text-sm" v-show="errors.has('metodo', 'compra')">{{ errors.first('metodo', 'compra') }}</span>
                </div>
                <div class="vx-col w-full md:w-3/12" v-show="ruleDigitos.show">                
                    <vs-input data-vv-scope="compra" v-model="detallePagoCompra.digitos" class="w-full uppercase" icon-pack="feather" icon="icon-book" data-vv-as="Ultimos 4 digitos" v-validate="ruleDigitos.rule" label="4 ult. digitos*:" name="digitos"/>
                    <span class="text-danger text-sm" v-show="errors.has('digitos', 'compra')">{{ errors.first('digitos', 'compra') }}</span>
                </div>
                <div class="vx-col w-full md:w-3/12" v-show="ruleNumCheque.show">
                    <vs-input data-vv-scope="compra" v-model="detallePagoCompra.num_cheque" class="w-full uppercase" icon-pack="feather" icon="icon-book" data-vv-as="Numero de cheque" v-validate="ruleNumCheque.rule" label="Num. Cheque*:" name="cheque"/>
                    <span class="text-danger text-sm" v-show="errors.has('cheque', 'compra')">{{ errors.first('cheque', 'compra') }}</span>
                </div>
                <div class="vx-col w-full md:w-3/12" v-show="ruleNumTransferencia.show">
                    <vs-input data-vv-scope="compra" v-model="detallePagoCompra.num_transferencia" class="w-full uppercase" icon-pack="feather" icon="icon-book" data-vv-as="Numero de transferencia" v-validate="ruleNumTransferencia.rule" label="Num. Transferencia*:" name="transferencia"/>
                    <span class="text-danger text-sm" v-show="errors.has('transferencia', 'compra')">{{ errors.first('transferencia', 'compra') }}</span>
                </div>
                <div class="vx-col w-full md:w-3/12" v-show="ruleBanco.show">
                    <vs-input data-vv-scope="compra" v-model="detallePagoCompra.banco" class="w-full uppercase" icon-pack="feather" icon="icon-book" data-vv-as="Banco" v-validate="ruleBanco.rule" label="Banco*:" name="banco"/>
                    <span class="text-danger text-sm" v-show="errors.has('banco', 'compra')">{{ errors.first('banco', 'compra') }}</span>
                </div>
            </div>
            <div class="vx-row mt-4">
                <div class="vx-col w-full">
                    <div class="flex items-end">
                        <feather-icon icon="ArchiveIcon" class="mr-2" svgClasses="w-5 h-5" />
                        <span class="leading-none font-medium">Articulos de la compra</span>
                    </div>
                </div>
            </div>
            <vs-divider /> 
            <div class="vx-row mb-4 mt-3">
                <div class="vx-col w-full">
                    <vx-input-group>    
                        <v-select
                            v-model="selectedArticulo"
                            :filterable="false"
                            placeholder="Seleccione o busque un articulo"
                            :options="searchedArticulos"
                            @search="onSearchArticulos">
                            <div slot="no-options">No hay opciones disponibles.</div>
                            <template slot="option" slot-scope="option">
                                <div class="d-center">
                                    <div class="vx-row">
                                        <div class="vx-col">
                                            <img class="rounded h-16 w-16" :src="option.imagen ? 'data:image/png;base64, ' + option.imagen : require('@assets/images/no-image-icon.png')"/>
                                        </div>
                                        <div class="vx-col w-10/12">
                                            <b>Codigo de barras:</b>&nbsp;{{ option.codigo_barras }}<br/>
                                            <b>Articulo:</b>&nbsp;{{ option.nombre }}
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template slot="selected-option" slot-scope="option">
                                <div class="d-center">
                                    <b>Codigo de barras:</b>&nbsp;{{ option.codigo_barras }}<br/>
                                    <b>Articulo:</b>&nbsp;{{ option.nombre }}
                                </div>
                            </template>
                        </v-select>
                        <template slot="append">
                            <div class="append-text bg-primary">
                                <vs-button icon-pack="feather" icon="icon-plus" @click="addArticulo"></vs-button>
                            </div>
                        </template>
                    </vx-input-group>
                    <span class="text-danger text-sm">
                    Solo se mostraran los primeros 15 articulo que coincidan con la busqueda, para encontrar un producto en especifico sugerimos usar el
                    <b>CODIGO DE BARRAS</b>
                    </span>
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col w-full">
                    <vs-table
                        :hoverFlat="false"
                        stripe
                        noDataText="Aun no has agregado articulos a la compra"
                        :data="articulosCompra"
                        class="mt-4"
                    >
                        <template slot="thead">
                            <vs-th>Codigo de barras</vs-th>
                            <vs-th>Articulo</vs-th>
                            <vs-th>Precio Neto(Unidad)</vs-th>
                            <vs-th>Cantidad</vs-th>
                            <vs-th></vs-th>
                        </template>
                        <template slot-scope="{data}">
                        <vs-tr :data="articulo" :key="indextr" v-for="(articulo, indextr) in data">
                            <vs-td>{{ articulo.codigo_barras }}</vs-td>
                            <vs-td>{{ articulo.nombre }}</vs-td>
                            <vs-td>
                                <vs-input
                                    data-vv-scope="compra"
                                    :name="'articulo-precio' + indextr"
                                    v-validate="'required|numeric|min_value:1|decimal:2'"
                                    data-vv-as="Precio"
                                    v-model="articulo.precio"
                                />
                                <span
                                    class="text-danger text-sm"
                                >{{ errors.first('articulo-precio' + indextr, 'compra') }}</span>
                            </vs-td>
                            <vs-td>
                                <vs-input
                                    data-vv-scope="compra"
                                    :name="'articulo' + indextr"
                                    v-validate="'required|numeric|min_value:1'"
                                    data-vv-as="Cantidad"
                                    v-model="articulo.cantidad"
                                />
                                <span
                                    class="text-danger text-sm"
                                >{{ errors.first('articulo' + indextr, 'compra') }}</span>
                            </vs-td>
                            <vs-td>
                                <vs-button
                                    @click="onDeleteFromCompra(indextr)"
                                    title="Eliminar de la compra"
                                    icon-pack="feather"
                                    size="large"
                                    icon="icon-x"
                                    color="danger"
                                    type="flat"
                                ></vs-button>
                            </vs-td>
                        </vs-tr>
                        </template>
                    </vs-table>
                </div>
            </div>
        </vs-popup>
    </div>
</template>
<script>
import _ from 'lodash'
import vSelect from 'vue-select'
import Datepicker from 'vuejs-datepicker'
import { es } from 'vuejs-datepicker/dist/locale'
import { format, compareAsc, parse } from 'date-fns'

import articuloService from '@services/articulos'
import proveedorService from '@services/proveedores'
import compraService from '@services/compras'

export default {
    components: {
        vSelect,
        Datepicker
    },
    props: {
        show: {
            type: Boolean,
            required: true
        }
    },
    watch: {
        show: function() {
            this.showPopup = this.show
        }
    },
    computed: {

        ruleNumCheque() {
            if (this.selectedMetodo === null) {
                return {
                    show: false,
                    rule: 'required'
                }
            }
            
            if (this.selectedMetodo.value === 2) {
                return {
                    show: true,
                    rule: 'required|numeric'
                }
            }

            return {
                show: false,
                rule: ''
            }
        },
        ruleBanco() {
            if (this.selectedMetodo === null) {
                return {
                    show: false,
                    rule: 'required'
                }
            }

            if (this.selectedMetodo.value === 2 || this.selectedMetodo.value === 3 || this.selectedMetodo.value === 4 || this.selectedMetodo.value === 5) {
                return {
                    show: true,
                    rule: 'required'
                }
            }

            return {
                show: false,
                rule: ''
            }
        },
        ruleDigitos() {
            if (this.selectedMetodo === null) {
                return {
                    show: false,
                    rule: 'required'
                }
            }

            if (this.selectedMetodo.value === 3 || this.selectedMetodo.value === 4) {
                return {
                    show: true,
                    rule: 'required|digits:4'
                }
            }

            return {
                show: false,
                rule: ''
            }
        },
        ruleNumTransferencia() {
            if (this.selectedMetodo === null) {
                return {
                    show: false,
                    rule: 'required'
                }
            }

            if (this.selectedMetodo.value === 5) {
                return {
                    show: true,
                    rule: 'required'
                }
            }

            return {
                show: false,
                rule: ''
            }
        }
    },
    data() {
        return {
            showPopup: false,
            title: 'NUEVA COMPRA',
            fecha: null,
            selectedProveedor: null,
            selectedMetodo: null,
            metodosPago: [],
            proveedores: [],
            searchedArticulos: [],
            articulosCompra: [],
            selectedArticulo: null,
            spanishDatepicker: es,
            disabledDates: {
                from: new Date()
            },
            compra: {
                fecha_compra: null,
                referencia_factura: null,
                metodos_pago_id: null,
                total_neto: null,
                proveedores_id: null
            },
            detallePagoCompra: {
                num_cheque: null,
                digitos: null,
                banco: null,
                num_transferencia: null
            },
        }
    },
    methods: {
        cancel() {
            this.$emit("update:show", false)
            this.$emit("on-cancel")
        },
        onDeleteFromCompra(index) {
            this.articulosCompra.splice(index, 1);
        },
        addArticulo() {
            let articulo = _.clone(this.selectedArticulo);
            this.selectedArticulo = null;
            let exists = _.find(
                this.articulosCompra,
                o => o.articulos_id === articulo.id
            );
            if (!exists) {
                this.articulosCompra.push({
                    articulos_id: articulo.id,
                    cantidad: 0,
                    nombre: articulo.nombre,
                    codigo_barras: articulo.codigo_barras,
                    precio: 0
                });
            } else {
                this.$vs.notify({
                    color: "warning",
                    position: "top-center",
                    title: "Advertencia",
                    text: "El articulo ya es parte de la compra"
                });
            }
        },
        onSearchArticulos(search, loading) {
            loading(true)
            this.search(loading, search, this);
        },
        search: _.debounce((loading, search, vm) => {
            if (!search) {
                vm.searchedArticulos = [];
                loading(false);
            } else {
                articuloService
                .getAll({
                    page: 1,
                    per_page: 15,
                    search: search,
                    tipo_producto: 1,
                    image: true
                })
                .then(res => {
                    vm.searchedArticulos = res.data.data; //_.map(res.data.data, (articulo) => ({ value: articulo.id, label: articulo.nombre }))
                    loading(false);
                });
            }
        }, 350),
        save() {
            let self = this
            this.$validator.validate('compra.*').then((r) => {
                if (r) {
                    this.compra.fecha_compra = format(this.fecha, 'yyy-MM-dd')
                    this.compra.metodos_pago_id = this.selectedMetodo.value
                    this.compra.proveedores_id = this.selectedProveedor.value
                    this.compra.productos = this.productos
                    this.compra.total_neto = this.totalCompra
                    this.compra.detalle_pago_compra = this.detallePagoCompra
                    if (this.articulosCompra.length <= 0) {
                        self.$vs.notify({
                            color: "warning",
                            position: "top-center",
                            title: "Advertencia",
                            text: "Estas creando una compra, porfavor agrega al menos un articulo"
                        })
                        return false
                    }
                    self.$vs.loading()
                    compraService.saveCompra(this.compra).then((response) => {
                        self.$vs.loading.close()
                        if (response.status === 201){
                            self.$vs.notify({
                                color:'success',
                                position:'top-center',
                                title: 'Completado',
                                text:'Se ha registrado la compra, A continuacion sera redirigido al inventario'
                            })
                            setTimeout(() => {
                                self.$router.go()
                            }, 2000)
                        }
                    }).catch(e => {
                        self.$vs.loading.close()
                        self.$vs.notify({
                            color:'warning',
                            position:'top-center',
                            title: 'Advertencia',
                            text:'Algo ha sucedido, vuelva a intentarlo'
                        })
                    })
                }
            })
        }
    },
    created: function() {
        let self = this
        this.$emit("update:show", JSON.parse(this.show))

        compraService.getMetodosPago().then((response) => {
            if (response.status === 200) {
                self.metodosPago = _.map(response.data.data, (metodo) => {
                    return  {
                        value: metodo.id,
                        label: metodo.metodo
                    }
                })
            }
        })
        proveedorService.getActiveProveedores().then((response) => {
            if (response.status === 200) {
                self.proveedores = _.map(response.data, (proveedor) => {
                    return  {
                        value: proveedor.id,
                        label: proveedor.nombre_comercial + ' (' + proveedor.rfc + ')'
                    }
                })
            }
        })
    }
}
</script>