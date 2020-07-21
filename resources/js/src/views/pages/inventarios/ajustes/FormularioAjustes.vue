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
        <div
          class="w-full sm:w-1/6 ml-auto md:w-1/6 lg:w-1/6 xl:w-1/6 px-2 mb-1"
        >
          <vs-button
            color="success"
            size="small"
            class="w-full ml-auto"
            @click="openBuscador = true"
          >
            <img
              class="cursor-pointer img-btn"
              src="@assets/images/searcharticulo.svg"
            />
            <span class="texto-btn">Buscar Articulos</span>
          </vs-button>
        </div>
      </div>
      <div class="flex flex-wrap px-2">
        <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
          <vx-card no-radius title="Filtros de selección">
            <div class="flex flex-wrap">
              <div
                class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2"
              >
                <label class="text-base opacity-75 font-medium"
                  >Datos del Ajuste</label
                >
                <v-select
                  :options="tipoAjustes"
                  :clearable="false"
                  :dir="$vs.rtl ? 'rtl' : 'ltr'"
                  v-model="form.tipoAjuste"
                  class="mb-4 md:mb-0 mt-1"
                />
              </div>
              <div
                class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 mb-4 px-2"
              >
                <label class="text-base opacity-75 font-medium">Nota:</label>
                <vs-input
                  class="w-full mt-1"
                  icon="search"
                  maxlength="75"
                  placeholder="Nota sobre el ajuste"
                />
              </div>
            </div>
          </vx-card>
        </div>
        <div
          class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 px-2 mt-5"
        >
          <vs-table :data="[]" noDataText="0 Resultados">
            <template slot="header">
              <h3>Artículos del Ajuste</h3>
            </template>
            <template slot="thead">
              <vs-th>Núm. Ajuste</vs-th>
              <vs-th>Fecha del Ajuste</vs-th>
              <vs-th>Realizado Por</vs-th>
              <vs-th>Acciones</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td :data="data[indextr].id">
                  <span class="font-semibold">{{ data[indextr].id }}</span>
                </vs-td>
                <vs-td :data="data[indextr].codigo_barras">
                  <span class="font-semibold">{{
                    data[indextr].codigo_barras
                  }}</span>
                </vs-td>
                <vs-td :data="data[indextr].descripcion">
                  <span class="uppercase">
                    {{ data[indextr].descripcion }}
                  </span>
                </vs-td>

                <vs-td :data="data[indextr].id_user">
                  <div class="flex flex-start">
                    <img
                      class="cursor-pointer img-btn ml-auto mr-auto"
                      src="@assets/images/pdf.svg"
                      title="Modificar"
                      @click="openModificar(data[indextr].id)"
                    />
                  </div>
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
        </div>
      </div>
      <div
        hidden
        class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 pt-6 pb-10 px-2 mr-auto ml-auto"
      >
        <vs-button class="w-full" @click="acceptAlert()" color="success">
          <img
            width="25px"
            class="cursor-pointer"
            size="small"
            src="@assets/images/save.svg"
          />
          <span class="texto-btn">Guardar Datos</span>
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
  </div>
</template>
<script>
import vSelect from "vue-select";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import proveedores from "@services/proveedores";
import inventario from "@services/inventario";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import ArticulosBuscador from "@pages/inventarios/ajustes/searcher.vue";
/**VARIABLES GLOBALES */

export default {
  components: {
    "v-select": vSelect,
    Password,
    ConfirmarDanger,
    ConfirmarAceptar,
    ArticulosBuscador
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
      accionNombre: "Modificar Artículo",
      /**form */
      tipoAjustes: [
        {
          label: "Inventario Actual",
          value: "1"
        },
        {
          value: "2",
          label: "Ingresar no Inventariados"
        }
      ],

      form: {
        ajuste: [],
        tipoAjuste: { label: "Inventario Actual", value: "1" }
      },
      errores: []
    };
  },
  methods: {
    articuloSeleccionado(datos) {
      console.log("articuloSeleccionado -> datos", datos);
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
              if (this.getTipoformulario == "agregar") {
                this.callBackConfirmarAceptar = await this.guardar_articulo;
                this.openConfirmarAceptar = true;
              } else {
                /**modificar, se valida con password */
                this.form.id_articulo_modificar = this.get_articulo_id;
                this.callback = await this.modificar_articulo;
                this.operConfirmar = true;
              }
            })();
          }
        })
        .catch(() => {});
    },
    async guardar_articulo() {
      //aqui mando guardar los datos
      this.errores = [];
      this.$vs.loading();
      try {
        let res = await inventario.guardar_articulo(this.form);
        if (res.data >= 1) {
          //success
          this.$vs.notify({
            title: "Registro de Artículos",
            text: "Se ha guardado el artículo correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000
          });
          this.$emit("retornar_id", res.data);
          this.cerrarVentana();
        } else {
          this.$vs.notify({
            title: "Registro de Artículos",
            text: "Error al guardar el artículo, por favor reintente.",
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
              title: "Registro de Artículos",
              text: "Verifique los errores encontrados en los datos.",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              time: 12000
            });
          } else if (err.response.status == 409) {
            /**FORBIDDEN ERROR */
            this.$vs.notify({
              title: "Registro de Artículos",
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
      console.log("limpiar aqui");
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
