<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups big-forms planes_venta_form"
      fullscreen
      title="listado de precios para propiedades en cementerio"
      :active.sync="showVentana"
      ref="planes_cementerio"
    >
      <div class="flex flex-wrap pb-12 pt-2">
        <div class="w-full sm:w-12/12 md:w-7/12 lg:w-7/12 xl:w-7/12">
          <label class="text-sm opacity-75 font-bold">
            <span class="uppercase">Filtrar por tipo de propiedad:</span>
          </label>
          <v-select
            :options="propiedades_tipos"
            :clearable="false"
            :dir="$vs.rtl ? 'rtl' : 'ltr'"
            v-model="propiedad_tipo"
            class="pb-1 pt-1"
          >
            <div slot="no-options">Seleccione un Tipo de Propiedad</div>
          </v-select>
        </div>
        <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12">
          <vs-button class="float-right mt-6" size="small" color="success" @click="agregar()">
            <img class="cursor-pointer img-btn" src="@assets/images/plus.svg" />
            <span class="texto-btn">Agregar Precios</span>
          </vs-button>
          <vs-button
            class="float-right mr-16 mt-6"
            size="small"
            color="primary"
            @click="openReporte('Precios x Propiedad (Español)','/cementerio/lista_precios_pdf/es','')"
          >
            <img class="cursor-pointer img-btn" src="@assets/images/printer.svg" />
            <span class="texto-btn">Ver documento</span>
          </vs-button>
        </div>
      </div>

      <div v-if="propiedades.length>0">
        <div
          v-for="(propiedad,index) in propiedades"
          :key="index"
          :class="indexPrecios(index,propiedad.id)"
        >
          <vs-table :data="propiedad.precios" noDataText="0 Resultados">
            <template slot="header">
              <h3>Tipo {{propiedad.tipo}}, capacidad {{propiedad.capacidad}} Persona(s)</h3>
            </template>
            <template slot="thead">
              <vs-th>#</vs-th>
              <vs-th>Pago Inicial ($)</vs-th>
              <vs-th>Costo Neto ($)</vs-th>
              <vs-th>Costo a Pronto Pago ($)</vs-th>
              <vs-th>Abono Mensual ($)</vs-th>
              <vs-th>Desc. x Pago ($)</vs-th>
              <vs-th>Financiamiento</vs-th>
              <vs-th>Descripción</vs-th>
              <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{data}">
              <vs-tr :data="precio" :key="index_precio" v-for="(precio, index_precio) in data">
                <vs-td :data="data[index_precio].id">
                  <span class="font-semibold">{{(index_precio+1)}}</span>
                </vs-td>
                <vs-td
                  :data="data[index_precio].pago_inicial"
                >$ {{precio.pago_inicial | numFormat('0,000.00')}}</vs-td>
                <vs-td
                  :data="data[index_precio].costo_neto"
                >$ {{precio.costo_neto | numFormat('0,000.00')}}</vs-td>
                <vs-td
                  :data="data[index_precio].costo_neto"
                >$ {{precio.costo_neto_pronto_pago | numFormat('0,000.00')}}</vs-td>
                <vs-td
                  :data="data[index_precio].pago_mensual"
                >$ {{precio.pago_mensual | numFormat('0,000.00')}}</vs-td>
                <vs-td
                  :data="data[index_precio].costo_neto"
                >$ {{precio.descuento_x_pago | numFormat('0,000.00')}}</vs-td>
                <vs-td :data="data[index_precio].tipo_financiamiento">{{precio.tipo_financiamiento}}</vs-td>
                <vs-td :data="data[index_precio].descripcion">{{precio.descripcion}}</vs-td>

                <vs-td :data="data[index_precio].status">
                  <img
                    class="cursor-pointer img-btn ml-auto mr-3 mb-1"
                    src="@assets/images/edit.svg"
                    title="Modificar"
                    @click="openModificar(data[index_precio].id)"
                  />
                  <img
                    v-if="data[index_precio].status==1"
                    class="cursor-pointer img-btn-32 mr-auto ml-3"
                    src="@assets/images/switchon.svg"
                    title="Deshabilitar"
                    @click="enable_disable_precio(data[index_precio].id,data[index_precio].descripcion,'deshabilitar')"
                  />

                  <img
                    v-else
                    class="cursor-pointer img-btn-32 mr-auto ml-3"
                    src="@assets/images/switchoff.svg"
                    title="Habilitar"
                    @click="enable_disable_precio(data[index_precio].id,data[index_precio].descripcion,'habilitar')"
                  />
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
        </div>
      </div>

      <!--componente de confirmar sin contraseña-->
      <ConfirmarDanger
        :show="operConfirmar"
        :callback-on-success="callback"
        @closeVerificar="operConfirmar=false"
        :accion="'¿Desea eliminar este plan de mensualidades? Los datos quedarán eliminados del sistema.'"
        :confirmarButton="'Eliminar'"
      ></ConfirmarDanger>
      <Password
        :show="openPassword"
        :callback-on-success="callbackPassword"
        @closeVerificar="openPassword=false"
        :accion="accionPassword"
      ></Password>

      <FormularioPrecios
        :id_precio="id_precio_modificar"
        :tipo="tipoFormulario"
        :show="verFormularioPrecios"
        @closeVentana="verFormularioPrecios = false"
        @retornar_id="retorno_id"
      ></FormularioPrecios>

      <Reporteador
        :header="'Consultar precios x propiedad'"
        :show="openReportesLista"
        :listadereportes="ListaReportes"
        :request="request"
        @closeReportes="openReportesLista=false;"
      ></Reporteador>
      <!--fin de compornentes-->
    </vs-popup>
  </div>
</template>
<script>
import Reporteador from "@pages/Reporteador";
import ConfirmarDanger from "@pages/ConfirmarDanger";
import Password from "@pages/confirmar_password";
import cementerio from "@services/cementerio";
import FormularioPrecios from "@pages/cementerio/ventas/FormularioPrecios";
import vSelect from "vue-select";
export default {
  props: {
    show: {
      type: Boolean,
      required: true
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.$refs["planes_cementerio"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };

        (async () => {
          /**manda traer los financiamientps */
          await this.get_financiamientos();
        })();
      } else {
        /**cerrar ventana */
        this.datosVenta = [];
        this.total = 0;
      }
    }
  },
  components: {
    ConfirmarDanger,
    Password,
    "v-select": vSelect,
    FormularioPrecios,
    Reporteador
  },
  data() {
    return {
      /**reporteador */
      openReportesLista: false,
      ListaReportes: [],
      request: {
        id_tipo_propiedad: "",
        email: ""
      },
      /**reportrador */
      selected: [],
      propiedades: [],
      propiedades_tipos: [],
      propiedad_tipo: {},
      verFormularioPrecios: false,
      tipoFormulario: "",
      id_precio_modificar: 0,
      /** */
      operConfirmar: false,
      openPassword: false,
      accionPassword: "",
      callback: Function,
      callbackPassword: Function
      //datos que mando para actualizar
    };
  },
  computed: {
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  methods: {
    openReporte(nombre_reporte = "", link = "", parametro = "") {
      this.ListaReportes = [];
      /**agrego los reportes de manera manual */
      this.ListaReportes.push({
        nombre: "Precios x Propiedad (Español)",
        url: "/cementerio/lista_precios_pdf/esf"
      });
      this.ListaReportes.push({
        nombre: "Precios x Propiedad (Inglés)",
        url: "/cementerio/lista_precios_pdf/en"
      });
      //estado de cuenta
      this.request.email = "";
      this.request.id_tipo_propiedad = this.propiedad_tipo.value;
      this.openReportesLista = true;
      this.$vs.loading.close();
    },
    openModificar(id_precio) {
      this.tipoFormulario = "modificar";
      this.id_precio_modificar = id_precio;
      this.verFormularioPrecios = true;
    },
    agregar() {
      this.tipoFormulario = "agregar";
      this.verFormularioPrecios = true;
    },
    indexPrecios(index, propiedad_id) {
      if (this.propiedad_tipo.value != "") {
        if (propiedad_id != this.propiedad_tipo.value) {
          return "hidden";
        }
      } else {
        if (index == 0) {
          return "";
        } else {
          return "pt-16";
        }
      }
    },
    async get_financiamientos() {
      try {
        this.$vs.loading();
        let res = await cementerio.get_financiamientos();

        this.propiedades = res.data;
        /**llenando los tipos de propiedad para el select */
        this.propiedades_tipos = [];
        this.propiedades_tipos.push({
          label: "Todas las Propiedades",
          value: ""
        });
        this.propiedades.forEach(element => {
          this.propiedades_tipos.push({
            label: "Tipo " + element.tipo,
            value: element.id
          });
          //la primero opcion
          this.propiedad_tipo = this.propiedades_tipos[0];
        });
        this.$vs.loading.close();
      } catch (err) {
        this.$vs.loading.close();
        this.ver = true;
        if (err.response) {
          if (err.response.status == 403) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 4000
            });
          }
        }
      }
    },
    cancelar() {
      this.$emit("closePlanesCementerio");
      return;
    },
    retorno_id(dato) {
      this.get_financiamientos();
    },
    enable_disable_precio(id_precio, precio, accion) {
      this.accionPassword = accion + " precio " + precio;
      this.id_precio_modificar = id_precio;
      this.openPassword = true;
      this.callbackPassword = this.enable_disable;
    },
    enable_disable() {
      this.$vs.loading();
      let datos = {
        id_precio: this.id_precio_modificar
      };
      cementerio
        .enable_disable(datos)
        .then(res => {
          this.$vs.loading.close();
          this.get_financiamientos();
          if (res.data >= 1) {
            this.$vs.notify({
              title: "Cambiar estatus del precio",
              text: "Se ha actualizado el estatus exitosamente.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "success",
              time: 4000
            });
          } else {
            this.$vs.notify({
              title: "Cambiar estatus del precio",
              text: "No se realizaron cambios.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "primary",
              time: 4000
            });
          }
        })
        .catch(err => {
          this.$vs.loading.close();
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
            } else if (err.response.status == 422) {
              /**error de validacion */
              this.errores = err.response.data.error;
            } else if (err.response.status == 409) {
              /**FORBIDDEN ERROR */
              this.$vs.notify({
                title: "Cambiar estatus del precio",
                text: err.response.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time: 8000
              });
            }
          }
        });
    }
  },
  created() {}
};
</script>