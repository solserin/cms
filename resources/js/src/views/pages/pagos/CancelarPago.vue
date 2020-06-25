<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups-cancelar_cementerio normal-forms cancelar_cementerio background-header-forms"
      fullscreen
      title="Cancelar Movimientos de Cobranza"
      :active.sync="showVentana"
      ref="cancelar_pago"
    >
      <div class="pb-5">
        <div class="flex flex-wrap">
          <div class="w-full text-center py-1">
            <div class="w-full">
              <span class="text-lg font-medium text-dark">
                ¿Está seguro de querer cancelar este movimiento?
              </span>
            </div>
            <div class="w-full py-2 px-2">
              <span class="text-base">
                Una vez realizado el proceso de cancelación no habrá manera de
                volver a habilitar este movimiento. Es recomendable llevar a
                cabo este proceso una vez esté seguro de que es necesario.
              </span>
            </div>
          </div>
        </div>
        <div class="flex flex-wrap">
          <div
            class="w-full sm:w-12/12 md:w-12/12 lg:w-6/12 xl:w-6/12 px-2 text-center mt-16"
          >
            <img width="400" src="@assets/images/cancelar.png" />
          </div>
          <div class="w-full sm:w-12/12 md:w-12/12 lg:w-6/12 xl:w-6/12 px-2">
            <div class="w-full pt-1 text-center">
              <span class="font-medium text-dark text-lg"
                >Operación de Tipo:</span
              >
              <div class="py-1">
                <span
                  class="capitalize"
                  v-if="datosPago.tipo_operacion_texto"
                  >{{ datosPago.tipo_operacion_texto }}</span
                >
              </div>
            </div>

            <div class="w-full text-center ">
              <span class="font-medium text-dark text-lg"
                >Movimientos a Cancelar:</span
              >
              <div class="py-1" v-if="datosPago">
                <div class="capitalize">
                  Clave
                  <span class="font-medium text-black">{{ datosPago.id }}</span
                  >, {{ datosPago.movimientos_pagos_texto }}, $
                  <span class="font-medium text-black"
                    >{{ datosPago.monto_pago | numFormat("0,000.00") }} Pesos
                    mxn</span
                  >
                </div>
                <div
                  class="capitalize py-2"
                  :key="indextr"
                  v-for="(subpago, indextr) in datosPago.subpagos"
                >
                  Clave
                  <span class="font-medium text-black">{{ subpago.id }}</span
                  >, {{ subpago.movimientos_pagos_texto }}, $
                  <span class="font-medium text-black"
                    >{{ subpago.monto_pago | numFormat("0,000.00") }} Pesos
                    mxn</span
                  >
                </div>
              </div>
            </div>

            <div class="w-full py-3">
              <label class="">Seleccione un motivo de cancelación:</label>
            </div>
            <div class="w-full">
              <v-select
                :options="motivos"
                :clearable="false"
                v-model="form.motivo"
                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                class="pb-1 pt-1 large_select"
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

        <div class="flex flex-wrap px-2">
          <div class="py-5">
            <label class=""
              >Agregue un comentario respecto a la cancelación de este
              movimiento:</label
            >
          </div>
          <vs-textarea
            class="pb-1 pt-1"
            label="Detalle de la cancelación..."
            height="170px"
            v-model="form.comentario"
            ref="comentario"
          />
        </div>

        <div class="flex flex-wrap">
          <div class="w-full pt-6">
            <div class="text-center">
              <vs-button
                color="danger"
                class=""
                size="small"
                @click="acceptAlert"
              >
                <img
                  width="25px"
                  class="cursor-pointer"
                  src="@assets/images/cancel.svg"
                />
                <span class="texto-btn">Cancelar Movimiento</span>
              </vs-button>
            </div>
          </div>
        </div>
      </div>

      <Password
        :show="openConfirmar"
        :callback-on-success="callback"
        @closeVerificar="openConfirmar = false"
        :accion="'Cancelar venta de propiedad'"
      ></Password>

      <ConfirmarDanger
        :show="openConfirmarSinPassword"
        :callback-on-success="callBackConfirmar"
        @closeVerificar="openConfirmarSinPassword = false"
        :accion="accionConfirmarSinPassword"
        :confirmarButton="botonConfirmarSinPassword"
      ></ConfirmarDanger>
    </vs-popup>
  </div>
</template>
<script>
import Password from "@pages/confirmar_password";
import vSelect from "vue-select";
import pagos from "@services/pagos";
import ConfirmarDanger from "@pages/ConfirmarDanger";
export default {
  components: {
    Password,
    "v-select": vSelect,
    ConfirmarDanger
  },
  props: {
    show: {
      type: Boolean,
      required: true
    },
    id_pago: {
      type: Number,
      required: true,
      default: 0
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.$refs["cancelar_pago"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() => {
          this.$refs["comentario"].$el.querySelector("textarea").focus();
        });

        (async () => {
          this.$vs.loading();
          try {
            let res = await pagos.get_pago_id(this.getPagoId);
            this.datosPago = res.data[0];
            this.$vs.loading.close();
          } catch (err) {
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
                  time: 8000
                });
              } else {
                this.$vs.notify({
                  title: "Error",
                  text:
                    "Ha ocurrido un error al tratar de cargar el catálogo de vendedores, por favor reintente.",
                  iconPack: "feather",
                  icon: "icon-alert-circle",
                  color: "danger",
                  position: "bottom-right",
                  time: "9000"
                });
              }
            }

            this.cerrarVentana();
          }
        })();
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
    },
    getPagoId: {
      get() {
        return this.id_pago;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  data() {
    return {
      botonConfirmarSinPassword: "",
      accionConfirmarSinPassword: "",
      callBackConfirmar: Function,
      openConfirmarSinPassword: false,
      callback: Function,
      openConfirmar: false,
      errores: [],
      datosPago: [],
      motivos: [
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
        pago_id: "",
        motivo: {
          label: "ERROR AL CAPTURAR",
          value: 3
        },
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
              time: "8000"
            });
            return;
          } else {
            (async () => {
              this.callback = await this.cancelar_pago;
              this.openConfirmar = true;
            })();
          }
        })
        .catch(() => {});
    },
    cancelar() {
      this.botonConfirmarSinPassword = "Salir y limpiar";
      this.accionConfirmarSinPassword =
        "Esta acción limpiará los datos que capturó en el formulario.";
      this.openConfirmarSinPassword = true;
      this.callBackConfirmar = this.cerrarVentana;
    },
    cerrarVentana() {
      this.openConfirmarSinPassword = false;
      this.form.comentario = "";
      (this.form.motivo = {
        label: "ERROR AL CAPTURAR",
        value: 3
      }),
        this.$emit("closeCancelarPago");
      return;
    },
    async cancelar_pago() {
      this.errores = [];
      this.$vs.loading();
      this.form.pago_id = this.getPagoId;
      try {
        let res = await pagos.cancelar_pago(this.form);
        console.log("cancelar_pago -> res", res);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Movimientos de Cobranza",
            text: "Se ha cancelado el movimiento correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000
          });
          this.$emit("closeCancelarPago", res.data);
        } else {
          this.$vs.notify({
            title: "Movimientos de Cobranza",
            text: "Error al cancelar el movimiento, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 8000
          });
        }
        this.$vs.loading.close();
      } catch (err) {
        if (err.response) {
          console.log("cancelar_pago -> err.response", err.response);
          //console.log("modificarVenta -> err.response", err.response);
          if (err.response.status == 403) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 8000
            });
          } else if (err.response.status == 422) {
            //checo si existe cada error
            this.errores = err.response.data.error;
            if (this.errores.pago_id) {
              //la propiedad esa ya ha sido vendida
              this.$vs.notify({
                title: "Movimientos de Cobranza",
                text: "No se ha seleccionado un movimiento a cancelar",
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 5000
              });
            }

            this.$vs.notify({
              title: "Movimientos de Cobranza",
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
              title: "Movimientos de Cobranza",
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
  created() {},
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", e => {
      if (e.keyCode === 27) {
        if (this.showVentana) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          //this.cancelar();
        }
      }
    });
  }
};
</script>
