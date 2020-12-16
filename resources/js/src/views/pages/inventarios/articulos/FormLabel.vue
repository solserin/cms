<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups normal-forms venta-propiedades background-header-forms articulos"
      fullscreen
      close="cancelar"
      :title="title"
      :active.sync="showVentana"
      ref="formulario"
    >
      <div
        class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2"
        v-if="form.lotes[0]"
      >
        <vx-card no-radius>
          <div class="flex flex-wrap">
            <div
              class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-1 px-2"
            >
              <h1 class="font-bold">Artículo Seleccionado</h1>
            </div>
            <div
              class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 mb-1 px-2"
            >
              <div class="mt-3">
                <div class="text-left">
                  <label class="text-md font-bold opacity-75"
                    >Descripción
                  </label>
                </div>
                <div class="text-left mt-3">
                  <label class="text-md opacity-75">{{ descripcion }} </label>
                </div>
              </div>
            </div>
            <div
              class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 mb-1 px-2"
            >
              <div class="mt-3">
                <div class="text-left">
                  <label class="text-md font-bold opacity-75"
                    >Existencia
                  </label>
                </div>
                <div class="text-left mt-3">
                  <label class="text-md opacity-75"
                    >{{ existencia }}
                    <label class="text-md font-bold opacity-75">Piezas </label>
                  </label>
                </div>
              </div>
            </div>

            <div
              class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 mb-1 px-2"
            >
              <vs-button class="float-right mr-12" size="small" color="success">
                <img
                  class="cursor-pointer img-btn"
                  src="@assets/images/printlote.svg"
                />
                <span class="texto-btn">Imprimir Todos</span>
              </vs-button>
            </div>
          </div>
        </vx-card>
      </div>
      <!--comienzo de tabla-->
      <vs-table
        :sst="true"
        @search="handleSearch"
        @change-page="handleChangePage"
        @sort="handleSort"
        :data="form.lotes"
        noDataText="0 Resultados"
        class="mt-6"
      >
        <template slot="header">
          <h3>Lotes Disponibles del artículo</h3>
        </template>
        <template slot="thead">
          <vs-th>Id Artículo</vs-th>
          <vs-th>Código de Barras</vs-th>
          <vs-th>Artículo</vs-th>
          <vs-th>Acciones</vs-th>
        </template>
        <template slot-scope="{ data }">
          <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].id">
              <span class="font-semibold">{{ data[indextr].id }}</span>
            </vs-td>
            <vs-td :data="data[indextr].id">
              <span class="font-semibold">{{ data[indextr].id }}</span>
            </vs-td>
            <vs-td :data="data[indextr].id">
              <span class="font-semibold">{{ data[indextr].id }}</span>
            </vs-td>

            <vs-td :data="data[indextr].id">
              <div class="flex flex-start">
                <img
                  class="cursor-pointer img-btn ml-auto"
                  src="@assets/images/printlote.svg"
                  title="Etiquetar"
                  @click="openFormLabels(data[indextr].id)"
                  v-if="data[indextr].tipo_articulos_id == 1"
                />
                <img
                  class="cursor-pointer img-btn ml-auto mr-1"
                  src="@assets/images/na.svg"
                  title="No etiquetable"
                  v-else
                />
                <img
                  class="cursor-pointer img-btn ml-auto mr-1"
                  src="@assets/images/edit.svg"
                  title="Modificar"
                  @click="openModificar(data[indextr].id)"
                />
                <img
                  v-if="data[indextr].status == 1"
                  class="cursor-pointer img-btn-32 mr-auto ml-3"
                  src="@assets/images/switchon.svg"
                  title="Deshabilitar"
                  @click="
                    deleteArticulo(data[indextr].id, data[indextr].descripcion)
                  "
                />
                <img
                  v-else
                  class="cursor-pointer img-btn-32 mr-auto ml-3"
                  src="@assets/images/switchoff.svg"
                  title="Habilitar"
                  @click="
                    altaArticulo(data[indextr].id, data[indextr].descripcion)
                  "
                />
              </div>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
      <!--fin de tabla-->
    </vs-popup>

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
      :accion="'He revisado la información y quiero registrar a este artículo'"
      :confirmarButton="'Guardar Artículo'"
    ></ConfirmarAceptar>
  </div>
</template>
<script>
import vSelect from "vue-select";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import inventario from "@services/inventario";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
/**VARIABLES GLOBALES */

export default {
  components: {
    "v-select": vSelect,
    ConfirmarDanger,
    ConfirmarAceptar,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    id_articulo: {
      type: Number,
      required: false,
      default: 0,
    },
  },
  watch: {
    show: function (newValue, oldValue) {
      this.limpiarValidation();
      if (newValue == true) {
        //cargo nacionalidades
        this.$refs["formulario"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancelar();
        };
        this.$nextTick(() => {
          //this.$refs["nombre_comercial"].$el.querySelector("input").focus()
        });

        (async () => {
          this.title = "Imprimir etiquetado de inventario";
          await this.get_inventario();
        })();
      }
    },
  },
  computed: {
    showVentana: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      },
    },
    get_articulo_id: {
      get() {
        return this.id_articulo;
      },
      set(newValue) {
        return newValue;
      },
    },
  },
  data() {
    return {
      title: "",
      accionConfirmarSinPassword: "",
      botonConfirmarSinPassword: "",
      openConfirmarSinPassword: false,
      callback: Function,
      datos_articulo: [],
      /**form */
      opciones_sino: [
        {
          value: 1,
          label: "SI",
        },
        {
          value: 0,
          label: "NO",
        },
      ],
      descripcion: "",
      existencia: 0,
      form: {
        lotes: [],
        id_articulo: 0,
      },
      errores: [],
    };
  },
  methods: {
    async get_inventario() {
      this.$vs.loading();
      try {
        let res = await inventario.get_inventariable_etiquetado();
        let datos = res.data;
        console.log(
          "🚀 ~ file: FormLabel.vue ~ line 168 ~ get_articulo_by_id ~ datos",
          datos
        );

        /**reviso que tenga lotes el articulo seleccionado */
        if (datos.length > 0) {
          for (let index = 0; index < datos.length; index++) {
            if (datos[index].id == this.get_articulo_id) {
              /**aqui esta el articulo */
              this.descripcion = datos[index].descripcion;
              this.existencia = datos[index].existencia;
              this.form.id_articulo = this.get_articulo_id;

              /**cargando solo los lotes con existencia */
              datos[index].inventario.forEach((element) => {
                if (element.existencia > 0) {
                  this.form.lotes.push({
                    lote_id: element.lotes_id,
                    articulos_id: element.articulos_id,
                    existencia: element.existencia,
                    cantidad_imprimir: element.existencia,
                  });
                }
              });
              break;
            }
          }
        } else {
          this.$vs.notify({
            title: "Impresión de etiquetas",
            text: "El artículo seleccionado no tiene lotes en el inventario.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-right",
            time: "4000",
          });
          this.cerrarVentana();
        }

        this.$vs.loading.close();
      } catch (error) {
        this.$vs.loading.close();
        this.$vs.notify({
          title: "Modificar Proveedor",
          text: "Ocurrió un error al traer la informacion, reintente.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "bottom-right",
          time: "4000",
        });
        this.cerrarVentana();
      }
    },
    acceptAlert() {
      this.$validator
        .validateAll()
        .then((result) => {
          if (!result) {
            this.$vs.notify({
              title: "Guardar Artículo",
              text: "Verifique que todos los datos han sido capturados",
              iconPack: "feather",
              icon: "icon-alert-circle",
              color: "danger",
              position: "bottom-right",
              time: "4000",
            });
          } else {
            this.errores = [];
            (async () => {
              /**modificar, se valida con password */
              //this.form.id_articulo_modificar = this.get_articulo_id;
              //this.callback = await this.modificar_articulo;
              //this.operConfirmar = true;
            })();
          }
        })
        .catch(() => {});
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
    limpiarVentana() {},
    limpiarValidation() {
      this.$validator.pause();
      this.$nextTick(() => {
        this.$validator.errors.clear();
        this.$validator.fields.items.forEach((field) => field.reset());
        this.$validator.fields.items.forEach((field) =>
          this.errors.remove(field)
        );
        this.$validator.resume();
      });
    },
    closeChecker() {
      this.operConfirmar = false;
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
  },
  created() {},
};
</script>
