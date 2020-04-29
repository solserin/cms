<template >
  <div class="centerx">
    <vs-popup
      class="searcher_clientes aet-popup"
      close="cancelar"
      title="búsqueda de Clientes | Filtros de selección"
      :active.sync="showVentana"
      ref="buscador_cliente"
    >
      <!--inicio de buscador-->
      <div class="py-3 px-2">
        <vx-card no-radius refresh-content-action @refresh="reset">
          <template slot="no-body">
            <div>
              <div class="flex flex-wrap">
                <div class="w-full sm:w-12/12 md:w-8/12 lg:w-10/12 xl:w-10/12 px-2">
                  <label class="text-sm opacity-75 font-bold">Nombre</label>
                  <vs-input
                    ref="nombre_cliente"
                    name="nombre_cliente"
                    data-vv-as=" "
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ej. Juan Pérez"
                    maxlength="12"
                    v-model="serverOptions.cliente"
                    v-on:keyup.enter="get_data(1)"
                    v-on:blur="get_data(1,'blur')"
                  />
                  <div>
                    <span class="text-danger text-sm">{{ errors.first('nombre_cliente') }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
                <div class="w-full sm:w-12/12 md:w-4/12 lg:w-2/12 xl:w-2/12 px-2">
                  <label class="text-sm opacity-75 font-bold">Núm. Cliente</label>
                  <vs-input
                    name="num_cliente"
                    data-vv-as=" "
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="Ej. 1258"
                    maxlength="12"
                    v-model="serverOptions.id_cliente"
                  />
                  <div>
                    <span class="text-danger text-sm">{{ errors.first('num_cliente') }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
                <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                  <label class="text-sm opacity-75 font-bold">RFC</label>
                  <vs-input
                    name="rfc"
                    data-vv-as=" "
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="ej. DIS961210RG9"
                    maxlength="12"
                    v-model="serverOptions.rfc"
                  />
                  <div>
                    <span class="text-danger text-sm">{{ errors.first('rfc') }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
                <div class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2">
                  <label class="text-sm opacity-75 font-bold">Celular</label>
                  <vs-input
                    name="celular"
                    data-vv-as=" "
                    type="text"
                    class="w-full pb-1 pt-1"
                    placeholder="ej. 6691435645"
                    maxlength="12"
                    v-model="serverOptions.celular"
                  />
                  <div>
                    <span class="text-danger text-sm">{{ errors.first('celular') }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
                <div
                  class="w-full sm:w-12/12 md:w-4/12 lg:w-4/12 xl:w-4/12 px-2"
                  style="z-index: 52002 !important;"
                >
                  <label class="text-sm opacity-75 font-bold">
                    <span>Nacionalidad</span>
                  </label>
                  <v-select
                    :options="nacionalidades"
                    :clearable="false"
                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                    v-model="serverOptions.nacionalidad"
                    class="pb-1 pt-1"
                    name="nacionalidad"
                    data-vv-as=" "
                  >
                    <div slot="no-options">Seleccione una opción</div>
                  </v-select>
                  <div>
                    <span class="text-danger text-sm">{{ errors.first('nacionalidad') }}</span>
                  </div>
                  <div class="mt-2"></div>
                </div>
              </div>
            </div>
          </template>
        </vx-card>
        <div class="resultados_clientes mt-5">
          <vs-table
            :sst="true"
            @search="handleSearch"
            @change-page="handleChangePage"
            @sort="handleSort"
            v-model="selected"
            :max-items="serverOptions.per_page.value"
            :data="clientes"
            stripe
            noDataText="0 Resultados"
          >
            <template slot="thead">
              <vs-th>Núm. Cliente</vs-th>
              <vs-th>Nombre</vs-th>
              <vs-th>Domicilio</vs-th>
              <vs-th>Celular</vs-th>
              <vs-th>Nacionalidad</vs-th>
              <vs-th>Seleccionar</vs-th>
            </template>
            <template slot-scope="{data}">
              <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                <vs-td :data="data[indextr].id">
                  <span class="font-semibold">{{data[indextr].id}}</span>
                </vs-td>
                <vs-td :data="data[indextr].nombre">{{data[indextr].nombre}}</vs-td>
                <vs-td :data="data[indextr].direccion">{{data[indextr].direccion}}</vs-td>
                <vs-td :data="data[indextr].celular">
                  <span class="font-medium">{{data[indextr].celular}}</span>
                </vs-td>

                <vs-td
                  :data="data[indextr].nacionalidad['id']"
                >{{data[indextr].nacionalidad['nacionalidad']}}</vs-td>
                <vs-td :data="data[indextr].id_user">
                  <vs-button
                    class="ml-8"
                    title="Seleccionar"
                    size="large"
                    icon-pack="feather"
                    icon="icon-check-circle"
                    color="success"
                    type="flat"
                    @click="openModificar(data[indextr].id)"
                  ></vs-button>
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
          <vs-divider />
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
import clientes from "@services/clientes";
import vSelect from "vue-select";
import Datepicker from "vuejs-datepicker";
import { es } from "vuejs-datepicker/dist/locale";

export default {
  components: {
    "v-select": vSelect,
    Datepicker
  },
  props: {
    show: {
      type: Boolean,
      required: true
    }
  },
  watch: {
    actual: function(newValue, oldValue) {
      this.get_data(this.actual);
    },
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.$nextTick(() =>
          this.$refs["nombre_cliente"].$el.querySelector("input").focus()
        );
        this.$refs["buscador_cliente"].$el.querySelector(
          ".vs-icon"
        ).onclick = () => {
          this.cancelar();
        };
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
      selected: [],
      nacionalidades: [],
      disabledDates: {
        from: new Date()
      },
      clientes: [],
      serverOptions: {
        page: "",
        per_page: "",
        id_cliente: "",
        rfc: "",
        celular: "",
        cliente: "",
        nacionalidad: {
          value: "122",
          label: "Mexicana"
        }
      },
      verPaginado: true,
      total: 0,
      actual: 1,
      spanishDatepicker: es
    };
  },
  methods: {
    reset(card) {
      card.removeRefreshAnimation(500);
      this.serverOptions.id_cliente = "";
      this.serverOptions.cliente = "";
      this.serverOptions.rfc = "";
      this.serverOptions.celular = "";
      this.serverOptions.cliente = "";
      this.get_data(this.actual);
    },
    cancelar() {
      this.$emit("closeBuscador");
      return;
    },
    get_nacionalidades() {
      clientes
        .get_nacionalidades()
        .then(res => {
          //le agrego las nacionalidades
          this.nacionalidades = [];
          this.nacionalidades.push({ label: "Seleccione 1", value: "" });
          res.data.forEach(element => {
            this.nacionalidades.push({
              label: element.nacionalidad,
              value: element.id
            });
          });
          this.serverOptions.nacionalidad = this.nacionalidades[0];
        })
        .catch(err => {});
    },
    get_data(page, evento = "") {
      if (evento == "blur") {
        if (
          this.serverOptions.cliente != "" ||
          this.serverOptions.cliente == ""
        ) {
          //la funcion no avanza
          return false;
        }
      }
      let self = this;
      if (clientes.cancel) {
        clientes.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = 5;
      this.serverOptions.status = 1;
      clientes
        .get_clientes(this.serverOptions)
        .then(res => {
          //console.log("get_data -> res", res);
          this.clientes = res.data.data;
          this.total = res.data.last_page;
          this.actual = res.data.current_page;
          this.verPaginado = true;
          this.$vs.loading.close();
        })
        .catch(err => {
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
                time: 4000
              });
            }
          }
        });
    },
    handleSearch(searching) {},
    handleChangePage(page) {},
    handleSort(key, active) {}
  },
  created() {
    this.get_nacionalidades();
    this.get_data(this.actual);
  }
};
</script>