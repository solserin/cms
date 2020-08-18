<template >
  <div class="centerx">
    <vs-popup
      class="forms-popups normal-forms inline-header-forms searcher_terrenos"
      fullscreen
      title="Catálogo de Artículos y Servicios por Lote"
      :active.sync="showVentana"
      ref="buscador_terrenos"
    >
      <!--inicio de buscador-->
      <div class="py-3">
        <vx-card no-radius title="Filtros de selección" refresh-content-action @refresh="reset">
          <template slot="no-body">
            <div>
              <div class="flex flex-wrap px-4 py-4">
                <div class="w-full sm:w-12/12 md:w-2/12 lg:w-2/12 xl:w-2/12 px-2">
                  <label class="text-sm opacity-75 font-bold">Cód. Barras</label>
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
                    <span class="text-danger text-sm">{{ errors.first("numero_control") }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>

                <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
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
                    <span class="text-danger">{{ errors.first("categoria") }}</span>
                  </div>
                </div>

                <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12 px-2">
                  <label class="text-sm opacity-75 font-bold">Descripción</label>
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
                    <span class="text-danger text-sm">{{ errors.first("descripcion") }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
              </div>
            </div>
          </template>
        </vx-card>
        <div class="resultados_clientes mt-10">
          <vs-table
            :sst="true"
            :max-items="serverOptions.per_page"
            :data="articulos"
            stripe
            noDataText="0 Resultados"
          >
            <template slot="header">
              <h3>Lista actualizada de clientes registrados</h3>
            </template>
            <template slot="thead">
              <vs-th>Núm. Venta</vs-th>
              <vs-th>Núm. Convenio</vs-th>
              <vs-th>Titular</vs-th>
              <vs-th>Plan Funerario</vs-th>
              <vs-th>Estado</vs-th>
              <vs-th>Seleccionar</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td :data="data[indextr].id">
                  <span class="font-semibold">{{ data[indextr].id }}</span>
                </vs-td>
                <vs-td :data="data[indextr].id">{{ data[indextr].id }}</vs-td>

                <vs-td :data="data[indextr].id">{{ data[indextr].id }}</vs-td>
                <vs-td :data="data[indextr].id">{{ data[indextr].id }}</vs-td>
                <vs-td :data="data[indextr].id">{{ data[indextr].id }}</vs-td>
                <vs-td :data="data[indextr].id">
                  <img
                    width="25"
                    class="cursor-pointer"
                    src="@assets/images/checked.svg"
                    @click="
                      retornarSeleccion(
                        data[indextr]
                      )
                    "
                  />
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
          <div>
            <vs-pagination v-if="verPaginado" :total="this.total" v-model="actual" class="mt-3"></vs-pagination>
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
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";

export default {
  components: {
    "v-select": vSelect,
    Datepicker,
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
        this.$refs["buscador_terrenos"].$el.querySelector(
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
      disabledDates: {
        from: new Date(),
      },
      articulos: [],
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
      spanishDatepicker: es,
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
        if (origen == "titular") {
          if (this.serverOptions.titular.trim() == "") {
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
      this.serverOptions.per_page = 12;
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
      if (datos.operacion_status != 0) {
        /**retorna los datos seleccionados a la venta que los solicita */
        this.$emit("retornoPlan", datos);
        this.$emit("closeBuscador");
      } else {
        /**error, venta cancelada */
        this.$vs.notify({
          title: "Servicios Funerarios",
          text: "Seleccione otra opción, esta operación fue cancelada.",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          time: 6000,
        });
      }
    },
    retorno_id(dato) {
      this.get_data("", this.actual);
    },
  },
};
</script>