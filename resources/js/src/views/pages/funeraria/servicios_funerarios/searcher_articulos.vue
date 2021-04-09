<template >
  <div class="">
    <vs-popup
      class="forms-popup"
      fullscreen
      title="Catálogo de Artículos y Servicios por Lote"
      :active.sync="showVentana"
      ref="buscador_lotes"
    >
      <!--inicio de buscador-->
      <div class="py-3">
        <vx-card
          no-radius
          title="Filtros de selección"
          refresh-content-action
          @refresh="reset"
        >
          <template slot="no-body">
            <div>
              <div class="flex flex-wrap px-4 py-4">
                <div
                  class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold"
                    >Cód. Barras</label
                  >
                  <vs-input
                    name="numero_control"
                    data-vv-as=" "
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ej. 000000001"
                    maxlength="15"
                    v-model.trim="serverOptions.numero_control"
                    v-on:keyup.enter="get_data('numero_control', 1)"
                    v-on:blur="get_data('numero_control', 1, 'blur')"
                  />
                  <div>
                    <span class="text-danger text-sm">{{
                      errors.first("numero_control")
                    }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>

                <div
                  class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Categorías</span>
                    <span class="texto-importante">(*)</span>
                  </label>
                  <v-select
                    :options="categorias"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="serverOptions.categoria"
                    class="mb-4 sm:mb-0 pb-1 pt-1"
                    name="categoria"
                    data-vv-as=" "
                  >
                    <div slot="no-options">Seleccione 1</div>
                  </v-select>
                  <div>
                    <span class="text-danger">{{
                      errors.first("categoria")
                    }}</span>
                  </div>
                </div>

                <div
                  class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold"
                    >Descripción</label
                  >
                  <vs-input
                    ref="descripcion"
                    name="descripcion"
                    data-vv-as=" "
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ej. Ataúd de Madera"
                    maxlength="12"
                    v-model.trim="serverOptions.descripcion"
                    v-on:keyup.enter="get_data('descripcion', 1)"
                    v-on:blur="get_data('descripcion', 1, 'blur')"
                  />
                  <div>
                    <span class="text-danger text-sm">{{
                      errors.first("descripcion")
                    }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
              </div>
            </div>
          </template>
        </vx-card>
        <div class="mt-10">
          <vs-table
            :sst="true"
            :max-items="serverOptions.per_page"
            :data="lotes"
            stripe
            noDataText="0 Resultados"
          >
            <template slot="header">
              <h3>Lista de Artículos y Servicios por Lotes</h3>
            </template>
            <template slot="thead">
              <vs-th>Cód. Barras</vs-th>
              <vs-th>Tipo</vs-th>
              <vs-th>Categoría</vs-th>
              <vs-th>Descripción</vs-th>
              <vs-th>Lote</vs-th>
              <vs-th>$ Costo</vs-th>
              <vs-th>Existencia</vs-th>
              <vs-th>Seleccionar</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td :data="data[indextr].codigo_barras">
                  <span class="font-semibold">{{
                    data[indextr].codigo_barras
                  }}</span>
                </vs-td>
                <vs-td :data="data[indextr].tipo">{{
                  data[indextr].tipo
                }}</vs-td>

                <vs-td :data="data[indextr].categoria">{{
                  data[indextr].categoria
                }}</vs-td>
                <vs-td :data="data[indextr].descripcion">{{
                  data[indextr].descripcion
                }}</vs-td>
                <vs-td :data="data[indextr].lote">{{
                  data[indextr].num_lote_inventario
                }}</vs-td>
                <vs-td :data="data[indextr].costo_neto_normal">
                  $
                  {{ data[indextr].costo_neto_normal | numFormat("0,000.00") }}
                </vs-td>
                <vs-td :data="data[indextr].existencia">{{
                  data[indextr].existencia
                }}</vs-td>
                <vs-td :data="data[indextr].id">
                  <img
                    width="25"
                    class="cursor-pointer"
                    src="@assets/images/checked.svg"
                    @click="retornarSeleccion(data[indextr])"
                  />
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
          <div>
            <vs-pagination
              v-if="verPaginado"
              :total="this.total"
              v-model="actual"
              class="mt-3"
            ></vs-pagination>
          </div>
        </div>
      </div>

      <!--fin de buscador-->
    </vs-popup>
  </div>
</template>
<script>
import funeraria from "@services/funeraria";
import vSelect from "vue-select";

export default {
  components: {
    "v-select": vSelect,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
  },
  watch: {
    "serverOptions.categoria": function (newVal, previousVal) {
      this.get_data("", 1);
    },
    actual: function (newValue, oldValue) {
      this.get_data("", this.actual);
    },
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$nextTick(() =>
          this.$refs["descripcion"].$el.querySelector("input").focus()
        );
        this.$refs["buscador_lotes"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };
        (async () => {
          await this.get_categorias_servicio();
        })();
        this.get_data("", 1);
      } else {
        /**cerrar y limpiar el formulario */
        this.serverOptions.numero_control = "";
        this.serverOptions.titular = "";
      }
    },
    articulos: function (newValue, oldValue) {
      /**se limpian los lotes */
      this.lotes = [];
      if (newValue.length > 0) {
        /**tiene datos el arreglo */
        newValue.forEach((articulo) => {
          if (articulo.tipo_articulos_id == 2) {
            /**es de tipo servicio */
            this.lotes.push({
              id: articulo.id,
              codigo_barras: articulo.codigo_barras,
              tipo: "Servicio",
              categoria: articulo.categoria.categoria,
              descripcion: articulo.descripcion,
              lote: "N/A",
              num_lote_inventario: "N/A",
              cantidad: 1,
              costo_neto_normal: articulo.precio_venta,
              descuento_b: 0,
              costo_neto_descuento: articulo.precio_venta,
              importe: 0,
              facturable_b: articulo.grava_iva_b,
              existencia: articulo.existencia,
              plan_b: 0,
            });
          } else if (articulo.tipo_articulos_id == 1) {
            /**es de tipo articulo */
            /**checando que haya existencia para poder mostrarlo */
            if (articulo.inventario.length > 0) {
              articulo.inventario.forEach((lote) => {
                this.lotes.push({
                  id: articulo.id,
                  codigo_barras: articulo.codigo_barras,
                  tipo: "Artículo",
                  categoria: articulo.categoria.categoria,
                  descripcion: articulo.descripcion,
                  lote: lote.lotes_id,
                  num_lote_inventario: lote.num_lote_inventario,
                  cantidad: 1,
                  costo_neto_normal: articulo.precio_venta,
                  descuento_b: 0,
                  costo_neto_descuento: 0,
                  importe: 0,
                  facturable_b: articulo.grava_iva_b,
                  existencia: lote.existencia,
                  plan_b: 0,
                });
              });
            }
          }
        });
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
  },
  data() {
    return {
      categorias: [
        {
          label: "Seleccione 1",
          value: "",
        },
      ],
      selected: [],
      articulos: [],
      lotes: [],
      serverOptions: {
        categoria: {
          label: "Seleccione 1",
          value: "",
        },
        categorias_id: "",
        filtro_especifico_opcion: 1,
        page: "",
        per_page: "",
        numero_control: "",
        descripcion: "",
      },
      verPaginado: true,
      total: 0,
      actual: 1,
    };
  },
  methods: {
    async get_categorias_servicio() {
      this.$vs.loading();
      await funeraria
        .get_categorias_servicio()
        .then((res) => {
          this.categorias = [];
          this.categorias.push({ label: "Seleccione 1", value: "" });
          res.data.forEach((element) => {
            this.categorias.push({
              label: element.categoria,
              value: element.id,
            });
          });
          this.serverOptions.categoria = this.categorias[0];
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
        });
    },

    reset(card) {
      card.removeRefreshAnimation(500);
      this.serverOptions.numero_control = "";
      this.serverOptions.titular = "";
      this.get_data("", this.actual);
    },
    cancelar() {
      this.$emit("closeBuscador");
      return;
    },
    get_data(origen = "", page, evento = "") {
      if (evento == "blur") {
        return;
      } else {
        /**checando el origen */
        if (origen == "descripcion") {
          if (this.serverOptions.descripcion.trim() == "") {
            //return;
          }
        } else if (origen == "numero_control") {
          if (this.serverOptions.numero_control.trim() == "") {
            //return;
          }
        }
      }

      let self = this;
      if (funeraria.cancel) {
        funeraria.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = 24;
      this.serverOptions.categorias_id = this.serverOptions.categoria.value;
      funeraria
        .get_inventario_servicios(this.serverOptions)
        .then((res) => {
          this.articulos = res.data.data;
          this.total = res.data.last_page;
          this.actual = res.data.current_page;
          this.verPaginado = true;
          this.$vs.loading.close();
        })
        .catch((err) => {
          this.$vs.loading.close();
          this.ver = true;
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
                time: 4000,
              });
            }
          }
        });
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {},
    retornarSeleccion(datos) {
      this.$emit("LoteSeleccionado", datos);
      this.$emit("closeBuscador");
    },
  },
};
</script>