<template>
    <div class="centerx">
        <vs-popup
            class="forms-popup popup-50"
            close="cancelar"
            :title="'ENTREGA DE CONVENIOS'"
            :active.sync="showVista"
            ref="formulario"
        >
            <div class="p-8">
                <div class="form-group">
                    <div class="title-form-group">
                        Datos del convenio
                    </div>
                    <div class="form-group-content">
                        <div class="flex flex-wrap">
                            <div class="w-full">
                                <div class="flex flex-wrap">
                                    <div class="w-full xl:w-12/12 px-2">
                                        <!--resumen-->
                                        <div class="flex flex-wrap">
                                            <div
                                                class="p-4 w-full mx-auto rounded-lg size-base border-gray-solid-1 rounded-lg"
                                            >
                                                <div
                                                    class="size-base font-bold color-black-900 uppercase pb-6 text-center"
                                                >
                                                    Resumen del Convenio
                                                </div>
                                                <div
                                                    class="flex flex-wrap color-copy"
                                                >
                                                    <div class="w-full">
                                                        <div
                                                            class="flex flex-wrap pb-6"
                                                        >
                                                            <div
                                                                class="w-full text-center font-medium color-black-900 uppercase"
                                                            >
                                                                <span
                                                                    v-if="
                                                                        getDatosConvenio.tipo ==
                                                                            'terreno'
                                                                    "
                                                                    >Ubicación
                                                                    de la
                                                                    Propiedad</span
                                                                >
                                                            </div>
                                                            <div
                                                                class="w-full text-center font-medium color-copy pt-2"
                                                            >
                                                                <span
                                                                    class="capitalize"
                                                                >
                                                                    <span>{{
                                                                        getDatosConvenio.producto
                                                                    }}</span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="w-full">
                                                        <div
                                                            class="flex flex-wrap"
                                                        >
                                                            <div
                                                                class="w-full xl:w-6/12 text-center font-medium color-black-900 uppercase"
                                                            >
                                                                Titular
                                                            </div>
                                                            <div
                                                                class="w-full xl:w-6/12 text-center font-medium color-copy"
                                                            >
                                                                <span>{{
                                                                    getDatosConvenio.titular
                                                                }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--resumen-->
                                    </div>

                                    <div class="w-full input-text px-2 mt-6">
                                        <label>Nota u observación:</label>
                                        <vs-textarea
                                            class="w-full mt-3"
                                            label="Descripción..."
                                            height="170px"
                                            v-model="form.nota"
                                            ref="comentario"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bottom-buttons-section">
                <div class="text-advice">
                    <span class="ojo-advice">Ojo:</span>
                    Por favor revise la información ingresada, si todo es
                    correcto de click en el "Botón de Abajo”.
                </div>

                <div class="w-full" v-if="getDatosConvenio.status == 0">
                    <vs-button
                        class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
                        color="success"
                        @click="Guardar()"
                    >
                        <span>Marcar Convenio Como Entregado</span>
                    </vs-button>
                </div>
                <div class="w-full" v-else>
                    <vs-button
                        class="w-full sm:w-full md:w-auto md:ml-2 my-2 md:mt-0"
                        color="danger"
                        @click="Guardar()"
                    >
                        <span>Marcar Convenio Como No</span>
                    </vs-button>
                </div>
            </div>
        </vs-popup>
        <Password
            :show="openStatus"
            :callback-on-success="callback"
            @closeVerificar="openStatus = false"
            :accion="accionNombre"
        ></Password>
    </div>
</template>
<script>
import Password from "@pages/confirmar_password";
import cementerio from "@services/cementerio";
export default {
    components: {
        Password
    },
    props: {
        show: {
            type: Boolean,
            required: true
        },
        datos: {
            type: Array,
            required: true,
            default: {}
        }
    },
    watch: {
        show: function(newValue, oldValue) {
            if (newValue == true) {
                this.$refs["formulario"].$el.querySelector(
                    ".vs-icon"
                ).onclick = () => {
                    this.cerrar();
                };
                this.form.nota = this.getDatosConvenio.nota;
            } else {
                //limpiar
                this.form.nota = "";
            }
        }
    },

    data() {
        return {
            openStatus: false,
            callback: Function,
            accionNombre: "",
            form: {
                id: "",
                tipo: "",
                nota: "",
                action: ""
            },
            errores: []
        };
    },
    computed: {
        showVista: {
            get() {
                return this.show;
            },
            set(newValue) {
                return newValue;
            }
        },
        getDatosConvenio: {
            get() {
                return this.datos;
            },
            set(newValue) {
                return newValue;
            }
        },
        getInfo() {
            return 1;
        }
    },
    methods: {
        cerrar() {
            this.$emit("closeEntregarConvenio");
        },
        Guardar() {
            this.form.tipo = this.getDatosConvenio.tipo;
            this.form.id = this.getDatosConvenio.id;
            this.form.action = this.getDatosConvenio.status == 0 ? 1 : 0;

            if (this.getDatosConvenio.status == 0) {
                this.accionNombre = "Marcar convenio como entregado";
            } else {
                this.accionNombre = "Desmarcar convenio como entregado";
            }

            //llamando a la function segun su cotnrolador
            if (this.form.tipo == "terreno") {
            }
            (async () => {
                this.callback = await this.actualizar;
                this.openStatus = true;
            })();
        },
        async actualizar() {
            this.$vs.loading();
            try {
                let res = await cementerio.actualizar_status_convenio(
                    this.form
                );
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Entrega de convenios",
                        text:
                            "Se ha actulizado la entrega del convenio correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000
                    });
                    this.$emit("closeEntregarConvenio");
                } else {
                    this.$vs.notify({
                        title: "Entrega de convenios",
                        text: "Error, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 4000
                    });
                }
                this.$vs.loading.close();
            } catch (err) {
                if (err.response) {
                    if (err.response.status == 403) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Permiso denegado",
                            text:
                                "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 4000
                        });
                    } else if (err.response.status == 409) {
                        this.$vs.notify({
                            title: "Cancelar entrega de convenio",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 30000
                        });
                    }
                }
                this.$vs.loading.close();
            }
        }
    },
    mounted() {
        //cerrando el confirmar con esc
        document.body.addEventListener("keyup", e => {
            if (e.keyCode === 27) {
                if (this.showNota) {
                    //CIERRO EL CONFIRMAR AL PRESONAR ESC
                    this.cerrar();
                }
            }
        });
    }
};
</script>
