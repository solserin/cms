<template >
  <div class="centerx">
    <vs-popup
      class="searcher_clientes forms-popups normal-forms inline-header-forms"
      fullscreen
      title="Catálogo de Terrenos Vendidos"
      :active.sync="showVentana"
      ref="buscador_terrenos"
    >
      <div class="flex flex-wrap my-2">
        <div
          class="w-full sm:w-12/12 ml-auto md:w-1/5 lg:w-1/5 xl:w-1/5 mb-1 px-2"
        >
          <vs-button
            color="success"
            size="small"
            class="w-full ml-auto"
            @click="verFormularioTerrenos = true"
          >
            <img class="cursor-pointer img-btn" src="@assets/images/plus.svg" />
            <span class="texto-btn">Vender Propiedad</span>
          </vs-button>
        </div>
      </div>
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
                  class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold"
                    >Núm. Convenio</label
                  >
                  <vs-input
                    name="numero_control"
                    data-vv-as=" "
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ej. 1258"
                    maxlength="6"
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
                  class="w-full sm:w-12/12 md:w-9/12 lg:w-9/12 xl:w-9/12 px-2"
                >
                  <label class="text-sm opacity-75 font-bold"
                    >Titular de la Propiedad</label
                  >
                  <vs-input
                    ref="nombre_titular"
                    name="nombre_titular"
                    data-vv-as=" "
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ej. Juan Pérez"
                    maxlength="12"
                    v-model.trim="serverOptions.titular"
                    v-on:keyup.enter="get_data('titular', 1)"
                    v-on:blur="get_data('titular', 1, 'blur')"
                  />
                  <div>
                    <span class="text-danger text-sm">{{
                      errors.first("nombre_titular")
                    }}</span>
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
            :data="terrenos"
            stripe
            noDataText="0 Resultados"
          >
            <template slot="header">
              <h3>Lista actualizada de clientes registrados</h3>
            </template>
            <template slot="thead">
              <vs-th>Núm. Venta</vs-th>
              <vs-th>Núm. Convenio</vs-th>
              <vs-th>Núm. Título</vs-th>
              <vs-th>Titular</vs-th>
              <vs-th>Ubicación</vs-th>
              <vs-th>Estado</vs-th>
              <vs-th>Seleccionar</vs-th>
            </template>
            <template slot-scope="{ data }">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td
                  :data="data[indextr].venta_terreno"
                  :class="
                    data[indextr].operacion_status == 0 ? 'text-danger' : ''
                  "
                >
                  <span class="font-semibold">{{
                    data[indextr].venta_terreno.id
                  }}</span>
                </vs-td>
                <vs-td
                  :data="data[indextr].id"
                  :class="
                    data[indextr].operacion_status == 0 ? 'text-danger' : ''
                  "
                  >{{ data[indextr].numero_convenio }}</vs-td
                >
                <vs-td
                  :data="data[indextr].id"
                  :class="
                    data[indextr].operacion_status == 0 ? 'text-danger' : ''
                  "
                  >{{ data[indextr].numero_titulo }}</vs-td
                >
                <vs-td
                  :data="data[indextr].id"
                  :class="
                    data[indextr].operacion_status == 0 ? 'text-danger' : ''
                  "
                  >{{ data[indextr].nombre }}</vs-td
                >
                <vs-td
                  :data="data[indextr].venta_terreno"
                  :class="
                    data[indextr].operacion_status == 0 ? 'text-danger' : ''
                  "
                  >{{
                    data[indextr].venta_terreno.ubicacion_texto +
                    " (" +
                    data[indextr].venta_terreno.tipo_propiedad.tipo +
                    ")"
                  }}</vs-td
                >
                <vs-td
                  :data="data[indextr].status_texto"
                  :class="
                    data[indextr].operacion_status == 0 ? 'text-danger' : ''
                  "
                  >{{ data[indextr].status_texto }}</vs-td
                >
                <vs-td :data="data[indextr].id">
                  <img
                    width="25"
                    class="cursor-pointer"
                    src="@assets/images/checked.svg"
                    @click="
                      retornarSeleccion(
                        data[indextr].venta_terreno.ubicacion_texto +
                          ' (' +
                          data[indextr].venta_terreno.tipo_propiedad.tipo +
                          ' Convenio ' +
                          data[indextr].numero_convenio +
                          ')',
                        data[indextr].venta_terreno.id,
                        data[indextr].operacion_status
                      )
                    "
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
      <FormularioClientes
        :tipo="'agregar'"
        :show="verFormularioTerrenos"
        @closeVentana="verFormularioTerrenos = false"
        @retornar_id="retorno_id"
      ></FormularioClientes>
      <!--fin de buscador-->
    </vs-popup>
  </div>
</template>
<script>
import FormularioClientes from "@pages/clientes/FormularioClientes";
import funeraria from "@services/funeraria";
import vSelect from "vue-select";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";

export default {
  components: {
    "v-select": vSelect,
    Datepicker,
    FormularioClientes,
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
  },
  watch: {
    "serverOptions.nacionalidad": function (newVal, previousVal) {
      this.get_data("", 1);
    },
    actual: function (newValue, oldValue) {
      this.get_data("", this.actual);
    },
    show: function (newValue, oldValue) {
      if (newValue == true) {
        this.$nextTick(() =>
          this.$refs["nombre_titular"].$el.querySelector("input").focus()
        );
        this.$refs["buscador_terrenos"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };
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
      verFormularioTerrenos: false,
      selected: [],
      disabledDates: {
        from: new Date(),
      },
      terrenos: [],
      serverOptions: {
        filtro_especifico_opcion: 2,
        page: "",
        per_page: "",
        numero_control: "",
        titular: "",
      },
      verPaginado: true,
      total: 0,
      actual: 1,
      spanishDatepicker: es,
    };
  },
  methods: {
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
      funeraria
        .get_terrenos(this.serverOptions)
        .then((res) => {
          this.terrenos = res.data.data;
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
    retornarSeleccion(ubicacion = "", id = "", operacion_status = "") {
      if (operacion_status != 0) {
        /**retorna los datos seleccionados a la venta que los solicita */
        this.$emit("retornoTerreno", {
          numero_control: id,
          ubicacion: ubicacion,
        });
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