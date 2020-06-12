<template >
  <div class="centerx">
    <vs-popup
      class="cancelar_ventas_cementerio"
      fullscreen
      title="Venta de Propiedades del Cementerio"
      :active.sync="showVentana"
      ref="lista_reportes"
    >
      <vx-card class="pt-5">
        <template slot="no-body">
          <div class="pb-5">
            <div class="flex flex-wrap">
              <div
                class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 mt-3"
              >
                <h3 class="text-xl">
                  <feather-icon
                    icon="ThumbsDownIcon"
                    class="mr-2 mb-5"
                    svgClasses="w-5 h-5"
                  />
                  <span class="font-bold text-primary uppercase">
                    <span class="uppercase"
                      >CANCELACIÓN DE VENTAS DEL CEMENTERIO</span
                    >
                  </span>
                </h3>
              </div>
            </div>
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <div class="flex flex-wrap">
                  <div class="w-full py-3">
                    <span>Titular de la Propiedad:</span>
                  </div>
                  <div class="w-full py-1">
                    <h3 class="text-xl">
                      <span class="font-bold text-black uppercase">
                        <span class="uppercase">{{
                          datosVenta.cliente_nombre
                        }}</span>
                      </span>
                    </h3>
                  </div>
                  <div class="w-full py-3">
                    <span>Fecha de la Venta:</span>
                  </div>
                  <div class="w-full py-1">
                    <h3 class="text-base">
                      <span class="text-black uppercase">
                        <span class="uppercase font-bold">{{
                          datosVenta.fecha_venta_texto
                        }}</span>
                      </span>
                    </h3>
                  </div>
                  <div class="w-full py-3">
                    <span>Claves del Contrato:</span>
                  </div>
                  <div class="w-full py-1">
                    <h3 class="text-base">
                      <span class="text-black">
                        <span class="font-bold uppercase">solicitud:</span>
                        <span class="uppercase"
                          >{{ datosVenta.numero_solicitud }},</span
                        >
                        <span class="font-bold uppercase">convenio:</span>
                        <span class="uppercase"
                          >{{ datosVenta.numero_convenio }},</span
                        >
                        <span class="font-bold uppercase">título:</span>
                        <span class="uppercase">{{
                          datosVenta.numero_titulo
                        }}</span>
                      </span>
                    </h3>
                  </div>
                  <div class="w-full py-5">
                    <span>Estatus de la Ubicación:</span>
                  </div>
                  <div class="w-full pt-2">
                    <h3 class="text-base">
                      <span class="text-black">
                        <span class="uppercase font-bold">no ocupada</span>
                      </span>
                    </h3>
                  </div>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <div class="flex flex-wrap">
                  <div class="w-full py-3">
                    <span>Precio Neto de la Propiedad:</span>
                  </div>
                  <div class="w-full py-1">
                    <h3 class="text-lg">
                      <span class="font-bold text-black uppercase">
                        <span class="uppercase"
                          >$
                          {{ datosVenta.total | numFormat("0,000.00") }} Pesos
                          mxn</span
                        >
                      </span>
                    </h3>
                  </div>
                  <div class="w-full py-3">
                    <span>Cantidad cubierta a la fecha:</span>
                  </div>
                  <div class="w-full py-1">
                    <h3 class="text-lg">
                      <span class="font-bold text-black uppercase">
                        <span class="uppercase"
                          >$
                          {{
                            datosVenta.total_pagado | numFormat("0,000.00")
                          }}
                          Pesos mxn</span
                        >
                      </span>
                    </h3>
                  </div>

                  <div class="w-full py-3">
                    <label class="text-sm opacity-75 font-bold"
                      >Ingrese la cantidad a regresar en caso de aplicar:</label
                    >
                  </div>
                  <div class="w-full">
                    <vs-input
                      v-model="form.cantidad"
                      v-validate="'decimal:2'"
                      name="cantidad"
                      data-vv-as=" "
                      type="text"
                      class="w-full"
                      placeholder=" $ 00.00 Pesos MXN"
                      maxlength="7"
                    />
                    <div>
                      <span class="text-danger text-sm">{{
                        errors.first("cantidad")
                      }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores.cantidad"
                        >{{ errores.cantidad[0] }}</span
                      >
                    </div>
                  </div>

                  <div class="w-full py-3">
                    <label class="text-sm opacity-75 font-bold"
                      >Seleccione un motivo de cancelación:</label
                    >
                  </div>
                  <div class="w-full">
                    <v-select
                      :options="motivos"
                      :clearable="false"
                      v-model="form.motivo"
                      :dir="$vs.rtl ? 'rtl' : 'ltr'"
                      class="pb-1 pt-1"
                      v-validate:motivo_computed.immediate="'required'"
                      name="plan_venta"
                      data-vv-as=" "
                    >
                      <div slot="no-options">
                        No Se Ha Seleccionado Ningún Motivo
                      </div>
                    </v-select>
                    <div>
                      <span class="text-danger text-sm">{{
                        errors.first("motivo")
                      }}</span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="text-danger text-sm"
                        v-if="this.errores['motivo.value']"
                        >{{ errores["motivo.value"][0] }}</span
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex flex-wrap mt-8">
              <div class="w-full px-2">
                <div class="pb-2">
                  <label class="text-sm opacity-75 font-bold"
                    >Agregue un comentario respecto a la cancelación de esta
                    venta:</label
                  >
                </div>
                <vs-textarea
                  class="pb-1 pt-1"
                  label="Detalle de la cancelación..."
                  height="120px"
                  v-model="form.comentario"
                  ref="comentario"
                />
              </div>
              <div class="w-full px-2 text-base text-danger pb-5">
                <span class="font-bold">Ojo:</span>
                Una vez realizado el proceso de cancelación no habrá manera de
                volver a habilitar la venta. Es recomendable llevar a cabo este
                proceso una vez esté seguro de que es necesario.
              </div>

              <div
                class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 my-5"
              >
                <vs-button
                  icon-pack="feather"
                  icon="icon-thumbs-down"
                  color="success"
                  class="float-right"
                  @click="acceptAlert()"
                  >Cancelar Venta</vs-button
                >
                <vs-button
                  icon-pack="feather"
                  icon="icon-x"
                  color="danger"
                  class="float-right mr-20"
                  @click="cancelar()"
                  >Cerrar Ventana (Esc)</vs-button
                >
              </div>
            </div>
          </div>
        </template>
      </vx-card>
      <Password
        :show="openConfirmar"
        :callback-on-success="callback"
        @closeVerificar="openConfirmar = false"
        :accion="'Cancelar venta de propiedad'"
      ></Password>
    </vs-popup>
  </div>
</template>
<script>
import Password from "@pages/confirmar_password";
import vSelect from "vue-select";
import cementerio from "@services/cementerio";
export default {
  components: {
    Password,
    "v-select": vSelect
  },
  props: {
    show: {
      type: Boolean,
      required: true
    },
    id_venta: {
      type: Number,
      required: true,
      default: 0
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.$refs["lista_reportes"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() =>
          this.$refs["comentario"].$el.querySelector("textarea").focus()
        );
      } else {
        /**cerrar ventana */
        this.datosVenta = [];
        this.total = 0;
      }
    },
    id_venta: function(newValue, oldValue) {
      if (newValue > 0) {
        /* this.form.venta_id = newValue;
        let self = this;
        if (cementerio.cancel) {
          cementerio.cancel("Operation canceled by the user.");
        }
        this.$vs.loading();
        cementerio
          .get_venta_id(newValue)
          .then(res => {
            this.datosVenta = res.data;
            this.$vs.loading.close();
          })
          .catch(err => {
            this.$vs.loading.close();
            if (err.response) {
              if (err.response.status == 403) {
                this.$vs.notify({
                  title: "Permiso denegado",
                  text:
                    "Verifique sus permisos con el administrador del sistema.",
                  iconPack: "feather",
                  icon: "icon-alert-circle",
                  color: "warning",
                  time: 4000
                });
              }
            }
          });
          */
      } else {
        this.form.venta_id = 0;
      }
    }
  },
  computed: {
    motivo_computed: function() {
      return this.form.motivo.value;
    },
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  data() {
    return {
      callback: Function,
      openConfirmar: false,
      errores: [],
      datosVenta: [],
      motivos: [
        {
          label: "FALTA DE PAGO",
          value: 1
        },
        {
          label: "A PETICIÓN DEL CLIENTE",
          value: 2
        },
        {
          label: "ERROR AL CAPTURAR",
          value: 3
        }
      ],
      form: {
        venta_id: "",
        motivo: {
          label: "FALTA DE PAGO",
          value: 1
        },
        cantidad: Number(0.0),
        comentario: ""
      }
    };
  },
  methods: {
    acceptAlert() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$vs.notify({
              title: "Error",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000"
            });
            return;
          } else {
            this.callback = this.cancelar_venta;
            this.openConfirmar = true;
          }
        })
        .catch(() => {});
    },

    cancelar() {
      this.form.comentario = "";
      this.form.cantidad = 0;
      (this.form.motivo = {
        label: "FALTA DE PAGO",
        value: 1
      }),
        this.$emit("closeCancelarVenta");
      return;
    },
    cancelar_venta() {
      this.errores = [];
      this.$vs.loading();
      cementerio
        .cancelar_venta(this.form)
        .then(res => {
          if (res.data >= 1) {
            //success
            this.$vs.notify({
              title: "Ventas de Propiedades",
              text: "Se ha cancelado la venta correctamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 5000
            });
            this.$emit("ConsultarVenta", res.data);
            this.cancelar();
          } else {
            this.$vs.notify({
              title: "Ventas de Propiedades",
              text: "Error al cancelar la venta, por favor reintente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 4000
            });
          }
          this.$vs.loading.close();
        })
        .catch(err => {
          if (err.response) {
            //console.log("modificarVenta -> err.response", err.response);
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
            } else if (err.response.status == 422) {
              //checo si existe cada error
              this.errores = err.response.data.error;
              if (this.errores.venta_id) {
                //la propiedad esa ya ha sido vendida
                this.$vs.notify({
                  title: "Seleccione una venta",
                  text: "No se ha seleccionado una venta a cancelar",
                  iconPack: "feather",
                  icon: "icon-alert-circle",
                  color: "danger",
                  time: 5000
                });
              }

              this.$vs.notify({
                title: "Cancelar Venta",
                text: "Verifique los errores encontrados en los datos.",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000
              });
            } else if (err.response.status == 409) {
              //este error es por alguna condicion que el contrano no cumple para modificar
              //la propiedad esa ya ha sido vendida
              this.$vs.notify({
                title: "Cancelar información de la venta",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 30000
              });
            }
          }
          this.$vs.loading.close();
        });
    }
  },
  created() {},
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", e => {
      if (e.keyCode === 27) {
        if (this.showVentana) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.cancelar();
        }
      }
    });
  }
};
</script>
