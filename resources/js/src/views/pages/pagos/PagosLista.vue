<template>
  <div>
    <div class="flex flex-wrap">
      <div class="w-full mb-1">
        <vs-button
          class="float-right"
          size="small"
          color="success"
          @click="formulario('agregar')"
        >
          <img class="cursor-pointer img-btn" src="@assets/images/pay.svg" />
          <span class="texto-btn">Capturar Pago</span>
        </vs-button>
      </div>
    </div>
    <div class="mt-5 vx-col w-full md:w-2/2 lg:w-2/2 xl:w-2/2">
      <vx-card
        no-radius
        title="Filtros de selección"
        refresh-content-action
        @refresh="reset"
        collapse-action
      >
        <div class="flex flex-wrap">
          <div
            class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2"
          >
            <label class="text-sm opacity-75">Mostrar</label>
            <v-select
              :options="mostrarOptions"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="mostrar"
              class="mb-4 sm:mb-0"
            />
          </div>
          <div
            class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2"
          >
            <label class="text-sm opacity-75">Estado</label>
            <v-select
              :options="estadosOptions"
              :clearable="false"
              :dir="$vs.rtl ? 'rtl' : 'ltr'"
              v-model="estado"
              class="mb-4 md:mb-0"
            />
          </div>
          <div
            class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-1 px-2"
          >
            <label class="text-sm opacity-75">Fecha de Pago</label>
            <flat-pickr
              data-vv-scope="form_calcular_adeudo"
              name="fecha_pago"
              data-vv-as=" "
              v-validate:fecha_pago_validacion_computed.immediate="'required'"
              :config="configdateTimePicker"
              v-model="serverOptions.fecha_pago"
              placeholder="Fecha del Pago"
              class="w-full"
            />
          </div>
          <div
            class="w-full sm:w-12/12 md:w-3/12 lg:w-3/12 xl:w-3/12 mb-4 px-2"
          >
            <label class="text-sm opacity-75">Número de Movimiento</label>
            <vs-input
              class="w-full"
              icon="search"
              maxlength="14"
              placeholder="Filtrar por Número de Movimiento"
              v-model="serverOptions.numero_control"
              v-on:keyup.enter="get_data(1)"
              v-on:blur="get_data(1, 'blur')"
            />
          </div>
        </div>
      </vx-card>
    </div>
    <br />
    <vs-table
      :sst="true"
      :max-items="serverOptions.per_page.value"
      :data="pagos"
      noDataText="0 Resultados"
    >
      <template slot="header">
        <h3>Listado de Movimientos Realizados</h3>
      </template>
      <template slot="thead">
        <vs-th>Núm. Movimiento</vs-th>
        <vs-th>Tipo Movimiento</vs-th>
        <vs-th>Fecha</vs-th>
        <vs-th>Tipo de Operación</vs-th>
        <vs-th>$ Monto</vs-th>
        <vs-th>Estatus</vs-th>
        <vs-th>Acciones</vs-th>
      </template>
      <template slot-scope="{ data }">
        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
          <vs-td :data="data[indextr].id">
            <span class="font-semibold">
              {{ data[indextr].id }}
            </span>
          </vs-td>
          <vs-td :data="data[indextr].movimientos_pagos_texto">
            <span v-if="data[indextr].parent_pago_id > 0">
              {{ data[indextr].movimientos_pagos_texto }}(Sobre el pago
              {{ data[indextr].parent_pago_id }}
              <img width="10" src="@assets/images/included.svg" />)
            </span>
            <span v-else>
              {{ data[indextr].movimientos_pagos_texto }}
            </span>
          </vs-td>
          <vs-td :data="data[indextr].fecha_pago_texto">
            <span class="font-medium">{{
              data[indextr].fecha_pago_texto
            }}</span>
          </vs-td>
          <vs-td :data="data[indextr].tipo_operacion_texto">{{
            data[indextr].tipo_operacion_texto
          }}</vs-td>
          <vs-td :data="data[indextr].monto_pago">
            $ {{ data[indextr].monto_pago | numFormat("0,000.00") }}
          </vs-td>
          <vs-td :data="data[indextr].status">
            <p v-if="data[indextr].status == 1" class=" font-medium">
              {{ data[indextr].status_texto }}
            </p>
            <p v-else class="text-danger font-medium">
              {{ data[indextr].status_texto }}
            </p>
          </vs-td>
          <vs-td :data="data[indextr].id">
            <div class="flex flex-start py-1">
              <img
                :class="[
                  'cursor-pointer img-btn',
                  tr.status == 1 ? 'ml-auto mr-2' : 'ml-auto mr-auto'
                ]"
                src="@assets/images/pdf.svg"
                title="Expediente"
                @click="openReporte(data[indextr])"
              />

              <!--verificar que tipo de movimeinto es, me itneresa que el id del pago a cancelar sea el del pago parent siempre-->
              <img
                width="24"
                v-if="data[indextr].status == 1"
                class="cursor-pointer mr-auto ml-2"
                src="@assets/images/cancel.svg"
                title="Cancelar Movimiento"
                @click="
                  cancelarPago(
                    data[indextr].parent_pago_id > 0
                      ? data[indextr].parent_pago_id
                      : data[indextr].id
                  )
                "
              />
            </div>
          </vs-td>
          <template class="expand-user" slot="expand"></template>
        </vs-tr>
      </template>
    </vs-table>
    <div>
      <vs-pagination
        v-if="verPaginado"
        :total="this.total"
        v-model="actual"
        class="mt-8"
      ></vs-pagination>
    </div>
    <FormularioPagos
      :show="verFormularioPagos"
      @closeVentana="closeFormularioPagos"
      @retorno_pagos="retorno_pagos"
    ></FormularioPagos>
    <Password
      :show="openStatus"
      :callback-on-success="callback"
      @closeVerificar="closeStatus"
      :accion="accionNombre"
    ></Password>
    <Reporteador
      :header="'consultar pago'"
      :show="openReportesListaLista"
      :listadereportes="ListaReportes"
      :request="request"
      @closeReportes="openReportesListaLista = false"
    ></Reporteador>
    <CancelarPago
      :show="openCancelar"
      @closeCancelarPago="closeCancelarPago"
      @retorno_pago="retorno_pago"
      :id_pago="id_pago"
    ></CancelarPago>
  </div>
</template>

<script>
import pagos from "@services/pagos";
import FormularioPagos from "@pages/pagos/FormularioPagos";
import CancelarPago from "@pages/pagos/CancelarPago";
import Reporteador from "@pages/Reporteador";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
//componente de password
import Password from "@pages/confirmar_password";

/**VARIABLES GLOBALES */
import { mostrarOptions, configdateTimePicker } from "@/VariablesGlobales";
import vSelect from "vue-select";
export default {
  components: {
    "v-select": vSelect,
    Password,
    FormularioPagos,
    Reporteador,
    CancelarPago,
    flatPickr
  },
  watch: {
    actual: function(newValue, oldValue) {
      (async () => {
        await this.get_data(this.actual);
      })();
    },
    mostrar: function(newValue, oldValue) {
      (async () => {
        await this.get_data(1);
      })();
    },
    estado: function(newVal, previousVal) {
      (async () => {
        await this.get_data(1);
      })();
    },
    "serverOptions.fecha_pago": function(newVal, previousVal) {
      if (newVal != "") {
        (async () => {
          await this.get_data(1);
        })();
      }
    }
  },
  data() {
    return {
      verFormularioPagos: false,
      openReportesListaLista: false,
      ListaReportes: [],
      request: {
        id_pago: "",
        email: "",
        destinatario: ""
      },
      id_pago: 0,
      openCancelar: false,
      configdateTimePicker: configdateTimePicker,
      serverOptions: {
        page: "",
        per_page: "",
        status: "",
        numero_control: "",
        fecha_pago: ""
      },
      activeTab: 0,
      verPaginado: true,
      total: 0,
      actual: 1,
      pagos: [],
      tipoFormulario: "",
      mostrarOptions: mostrarOptions,
      mostrar: { label: "15", value: "15" },
      estado: { label: "Todos", value: "" },
      estadosOptions: [
        {
          label: "Todos",
          value: ""
        },
        {
          label: "Activos",
          value: "1"
        },
        {
          label: "Cancelados",
          value: "0"
        }
      ],
      //fin variables
      openStatus: false,
      callback: Function,
      accionNombre: ""
    };
  },
  methods: {
    openReporte(datos) {
      /**verificandoque tipo de pago es */
      let url = "/pagos/recibo_de_pago/" + datos.id;
      this.request.id_pago = datos.id;
      if (datos.parent_pago_id > 0) {
        url = "/pagos/recibo_de_pago/" + datos.parent_pago_id;
        this.request.id_pago = datos.parent_pago_id;
      }
      this.ListaReportes = [];
      this.ListaReportes.push({
        nombre: "Recibo de Movimiento",
        url: url
      });
      //estado de cuenta
      this.request.email =
        datos.referencias_cubiertas[0].operacion_del_pago.cliente.email;

      this.request.destinatario =
        datos.referencias_cubiertas[0].operacion_del_pago.cliente.nombre;
      this.openReportesListaLista = true;
      this.$vs.loading.close();
    },
    closeFormularioPagos() {
      this.verFormularioPagos = false;
    },
    retorno_pagos(datos) {
      (async () => {
        try {
          let res = await pagos.get_pago_id(datos.id_pago);
          this.openReporte(res.data[0]);
        } catch (error) {
          this.$vs.notify({
            title: "Error",
            text:
              "Ha ocurrido un error al tratar de cargar el recibo de pago, por favor recargue la página.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-right",
            time: "9000"
          });
        }
      })();
      /* recibe un arreglo con id del pago que se acaba de registrar */
    },
    /**modulo */
    reset(card) {
      card.removeRefreshAnimation(500);
      this.serverOptions.numero_control = "";
      this.mostrar = { label: "15", value: "15" };
      this.estado = { label: "Todos", value: "" };
      this.serverOptions.fecha_pago = "";
      //this.get_data(this.actual);
    },
    async get_data(page, evento = "") {
      if (evento == "blur") {
        if (
          this.serverOptions.titular != "" ||
          this.serverOptions.titular == ""
        ) {
          //la funcion no avanza

          return false;
        }
        if (
          this.serverOptions.numero_control == "" ||
          this.serverOptions.numero_control != ""
        ) {
          //la funcion no avanza

          return false;
        }
      }
      let self = this;
      if (pagos.cancel) {
        pagos.cancel("Operation canceled by the user.");
      }
      this.$vs.loading();
      this.verPaginado = false;
      this.serverOptions.page = page;
      this.serverOptions.per_page = this.mostrar.value;
      this.serverOptions.status = this.estado.value;
      try {
        let res = await pagos.get_pagos(this.serverOptions);
        if (res.data.data) {
          this.pagos = res.data.data;
          this.total = res.data.last_page;
          this.actual = res.data.current_page;
        }
        this.verPaginado = true;
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
    closeStatus() {
      this.openStatus = false;
    },

    cancelarPago(id_pago) {
      this.id_pago = id_pago;
      this.openCancelar = true;
    },
    formulario(tipo) {
      this.tipoFormulario = tipo;
      this.verFormularioPagos = true;
    },

    closeListaReportes() {
      this.openReportesLista = false;
      this.id_pago = 0;
      (async () => {
        await this.get_data(this.actual);
      })();
    },
    closeCancelarPago(dato) {
      this.openCancelar = false;
      (async () => {
        await this.get_data(this.actual);
      })();
    },
    retorno_pago(dato) {
      this.openCancelar = false;
      (async () => {
        try {
          let res = await pagos.get_pago_id(dato);
          this.openReporte(res.data[0]);
        } catch (error) {
          this.$vs.notify({
            title: "Error",
            text:
              "Ha ocurrido un error al tratar de cargar el recibo de pago, por favor recargue la página.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-right",
            time: "9000"
          });
        }
      })();
    }
  },
  created() {
    (async () => {
      await this.get_data(this.actual);
    })();
  }
};
</script>
