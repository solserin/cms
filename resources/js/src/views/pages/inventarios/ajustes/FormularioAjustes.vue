<template >
  <div class="centerx">
    <vs-popup
      class="normal-forms venta-propiedades background-header-forms"
      fullscreen
      close="cancelar"
      :title="title"
      :active.sync="showVentana"
      ref="formulario"
    >
      <div class="flex flex-wrap">
        <div class="w-full sm:w-1/6 ml-auto md:w-1/6 lg:w-1/6 xl:w-1/6 px-2 mb-1">
          <vs-button
            color="success"
            size="small"
            class="w-full ml-auto"
            @click="openBuscador = true"
          >
            <img class="cursor-pointer img-btn" src="@assets/images/searcharticulo.svg" />
            <span class="texto-btn">Buscar Articulos</span>
          </vs-button>
        </div>
      </div>
      <div class="flex flex-wrap px-2">
        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
          <vx-card no-radius title="Filtros de selección">
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2">
                <label class="text-base opacity-75 font-medium">Datos del Ajuste</label>
                <v-select
                  :options="tipoAjustes"
                  :clearable="false"
                  :dir="$vs.rtl ? 'rtl' : 'ltr'"
                  v-model="form.tipoAjuste"
                  class="mb-4 md:mb-0 mt-1"
                />
              </div>
              <div class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 mb-4 px-2">
                <label class="text-base opacity-75 font-medium">Nota:</label>
                <vs-input
                  class="w-full mt-1"
                  maxlength="250"
                  placeholder="Nota sobre el ajuste"
                  v-model.trim="form.nota"
                />
              </div>
            </div>
          </vx-card>
        </div>
        <div class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 mt-5">
          <vs-table :data="form.ajuste" noDataText="0 Resultados">
            <template slot="header">
              <h3>Lista de Artículos a Inventariar</h3>
            </template>
            <template slot="thead">
              <vs-th>Núm. Artículo</vs-th>
              <vs-th>Código Barras</vs-th>
              <vs-th>Descripción</vs-th>
              <vs-th>Núm. Lote</vs-th>
              <vs-th>Existencia Sistema</vs-th>
              <vs-th>Existencia Física</vs-th>
              <vs-th hidden>Fecha Caducidad</vs-th>
              <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td :data="data[indextr].id">
                  <span class="font-semibold">{{ data[indextr].id }}</span>
                </vs-td>
                <vs-td :data="data[indextr].codigo_barras">
                  <span class="font-semibold">{{ data[indextr].codigo_barras }}</span>
                </vs-td>
                <vs-td :data="data[indextr].descripcion">
                  <span class="uppercase">{{ data[indextr].descripcion }}</span>
                </vs-td>
                <vs-td :data="data[indextr].lote">
                  <span class="uppercase">{{ data[indextr].lote }}</span>
                </vs-td>
                <vs-td :data="data[indextr].existencia_sistema">
                  <span class="uppercase">{{ data[indextr].existencia_sistema }}</span>
                </vs-td>
                <vs-td :data="data[indextr].existencia_fisica">
                  <vs-input
                    :name="'existencia_fisica'+indextr"
                    data-vv-as=" "
                    data-vv-validate-on="blur"
                    v-validate="'required|integer|min_value:1'"
                    class="w-full sm:w-10/12 md:w-8/12 lg:w-8/12 xl:w-8/12 mr-auto ml-auto mt-1 cantidad"
                    maxlength="4"
                    v-model="form.ajuste[indextr].existencia_fisica"
                  />
                  <div>
                    <span class="text-danger text-xs">
                      {{
                      errors.first('existencia_fisica'+indextr)
                      }}
                    </span>
                  </div>
                </vs-td>
                <vs-td hidden :data="data[indextr].fecha_caducidad">
                  <span v-if="data[indextr].caduca_b==1">
                    <flat-pickr
                      :name="'fecha_venta'+indextr"
                      data-vv-as=" "
                      v-validate="'required'"
                      :config="configdateTimePickerFechasCaducidad"
                      placeholder="Fecha de caducidad"
                      class="w-full sm:w-10/12 md:w-8/12 lg:w-8/12 xl:w-8/12 mr-auto ml-auto mt-1 text-center"
                      v-model="form.ajuste[indextr].fecha_caducidad"
                    />
                    <div>
                      <span class="text-danger text-xs">
                        {{
                        errors.first("fecha_venta"+indextr)
                        }}
                      </span>
                    </div>
                  </span>

                  <span v-else class="uppercase">{{ data[indextr].fecha_caducidad }}</span>
                </vs-td>
                <vs-td :data="data[indextr].id">
                  <div class="flex flex-start">
                    <img
                      class="cursor-pointer img-btn mr-auto ml-auto"
                      src="@assets/images/cancel.svg"
                      title="Remover"
                      @click="
                  deleteArticulo(data[indextr],indextr)
                "
                    />
                  </div>
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
        </div>
      </div>
      <div class="flex flex-wrap px-2 mt-10">
        <div class="w-full px-2">
          <div class="mt-2">
            <p class="text-center">
              <span class="text-danger font-medium">Ojo:</span>
              Por favor revise la información ingresada, si todo es
              correcto de click en "Botón de Abajo”.
            </p>
          </div>
        </div>
      </div>
      <div class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 pt-6 pb-10 px-2 mr-auto ml-auto">
        <vs-button class="w-full" @click="acceptAlert()" color="success">
          <img width="25px" class="cursor-pointer" size="small" src="@assets/images/box.svg" />
          <span class="texto-btn">Ajustar Inventario</span>
        </vs-button>
      </div>
    </vs-popup>
    <Password
      :show="operConfirmar"
      :callback-on-success="callback"
      @closeVerificar="closeChecker"
      :accion="accionNombre"
    ></Password>
    <ConfirmarDanger
      :show="openConfirmarSinPassword"
      :callback-on-success="callBackConfirmar"
      @closeVerificar="openConfirmarSinPassword = false"
      :accion="accionConfirmarSinPassword"
      :confirmarButton="botonConfirmarSinPassword"
    ></ConfirmarDanger>
    <ConfirmarAceptar
      :show="openConfirmarAceptar"
      :callback-on-success="callBackConfirmarAceptar"
      @closeVerificar="openConfirmarAceptar = false"
      :accion="'He revisado la información y quiero registrar a este proveedor'"
      :confirmarButton="'Guardar Proveedor'"
    ></ConfirmarAceptar>
    <ArticulosBuscador
      :show="openBuscador"
      @closeBuscador="openBuscador = false"
      @retornoArticulo="articuloSeleccionado"
    ></ArticulosBuscador>
    <Cantidad
      :show="openCantidad"
      :articulo="articulo"
      @closeCantidad="openCantidad = false"
      @retornoCantidad="retornoCantidad"
    ></Cantidad>
  </div>
</template>
<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import vSelect from "vue-select";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import proveedores from "@services/proveedores";
import inventario from "@services/inventario";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import ArticulosBuscador from "@pages/inventarios/ajustes/searcher.vue";
import Cantidad from "@pages/inventarios/cantidad.vue";
import { configdateTimePickerFechasCaducidad } from "@/VariablesGlobales";
/**VARIABLES GLOBALES */

export default {
  components: {
    "v-select": vSelect,
    flatPickr,
    Password,
    ConfirmarDanger,
    ConfirmarAceptar,
    ArticulosBuscador,
    Cantidad
  },
  props: {
    show: {
      type: Boolean,
      required: true
    }
  },
  watch: {
    show: function(newValue, oldValue) {
      this.limpiarValidation();
      if (newValue == true) {
        this.imagen_anterior = require("@assets/images/no-image-icon.png");
        //cargo nacionalidades
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() => {
          //this.$refs["nombre_comercial"].$el.querySelector("input").focus()
        });
        (async () => {
          this.title = "Realizar Ajuste del Inventario";
        })();
      }
    }
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
  data() {
    return {
      configdateTimePickerFechasCaducidad: configdateTimePickerFechasCaducidad,
      openCantidad: false,
      openBuscador: false,
      imagen_anterior: "",
      title: "",
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      operConfirmar: false,
      openConfirmarSinPassword: false,
      callback: Function,
      callBackConfirmar: Function,
      openConfirmarAceptar: false,
      callBackConfirmarAceptar: Function,
      accionNombre: "Ajustar Inventario",
      /**form */
      tipoAjustes: [
        {
          value: "1",
          label: "Lote no Inventariado"
        },
        {
          label: "Inventario Actual",
          value: "2"
        }
      ],
      articulo: [],
      form: {
        nota: "",
        ajuste: [],
        tipoAjuste: { label: "Lote no Inventariado", value: "1" }
      },
      errores: []
    };
  },
  methods: {
    deleteArticulo(datos, indextr) {
      this.index_articulo = indextr;
      this.botonConfirmarSinPassword = "eliminar";
      this.accionConfirmarSinPassword =
        "¿Desea remover este Artículo/Servicio?";
      this.callBackConfirmar = this.remover_concepto_callback;
      this.openConfirmarSinPassword = true;
    },
    //remover beneficiario callback quita del array al beneficiario seleccionado
    remover_concepto_callback() {
      this.form.ajuste.splice(this.index_articulo, 1);
    },

    cantidad(datos) {
      this.articulo = datos;
      this.openCantidad = true;
      console.log("cantidad -> datos", datos);
    },
    articuloSeleccionado(datos) {
      /**aqui se agrega el articulo seleccionado */
      /**se verifica qur tipo de ajuste es */
      if (this.form.tipoAjuste.value == 1) {
        /**es un ajuste de articulos fuera de inventario */
        /**es caducable */
        let caducidad = datos.caduca_b == 1 ? "" : "N/A";
        this.form.ajuste.push({
          id: datos.id,
          caduca_b: datos.caduca_b,
          precio_compra: datos.precio_compra,
          caduca_texto: datos.caduca_texto,
          codigo_barras: datos.codigo_barras,
          descripcion: datos.descripcion,
          fecha_caducidad: caducidad,
          lote: "N/A",
          existencia_sistema: 0,
          existencia_fisica: 1
        });
      }
    },
    acceptAlert() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$vs.notify({
              title: "Guardar Artículo",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000"
            });
          } else {
            this.errores = [];
            (async () => {
              this.callback = await this.ajustar_inventario;
              this.operConfirmar = true;
            })();
          }
        })
        .catch(() => {});
    },
    async ajustar_inventario() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await inventario.ajustar_inventario(this.form);
        console.log("ajustar_inventario -> res", res);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Ajuste de Inventario",
            text: "Se ha guardado el ajuste correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000
          });
          this.$emit("retornar_id", res.data);
          this.cerrarVentana();
        } else {
          this.$vs.notify({
            title: "Ajuste de Inventario",
            text: "Error al guardar el ajuste, por favor reintente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            time: 4000
          });
        }
        this.$vs.loading.close();
      } catch (err) {
        if (err.response) {
          console.log("ajustar_inventario -> err.response", err.response);
          if (err.response.status == 403) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Permiso denegado",
              text: "Verifique sus permisos con el administrador del sistema.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "warning",
              time: 12000
            });
          } else if (err.response.status == 422) {
            //checo si existe cada error
            this.errores = err.response.data.error;
            this.$vs.notify({
              title: "Ajuste de Inventario",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 12000
            });
          } else if (err.response.status == 409) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Ajuste de Inventario",
              text: err.response.data.error,
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 15000
            });
          }
        }
        this.$vs.loading.close();
      }
    },
    cancel() {
      this.$emit("closeVentana");
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
      this.limpiarVentana();
      this.$emit("closeVentana");
    },
    //regresa los datos a su estado inicial
    limpiarVentana() {
      this.form.nota = "";
      this.form.ajuste = [];
      this.form.tipoAjuste = { label: "Lote no Inventariado", value: "1" };
    },
    limpiarValidation() {
      this.$validator.pause();
      this.$nextTick(() => {
        this.$validator.errors.clear();
        this.$validator.fields.items.forEach(field => field.reset());
        this.$validator.fields.items.forEach(field =>
          this.errors.remove(field)
        );
        this.$validator.resume();
      });
    },
    closeChecker() {
      this.operConfirmar = false;
    }
  },
  created() {}
};
</script>
