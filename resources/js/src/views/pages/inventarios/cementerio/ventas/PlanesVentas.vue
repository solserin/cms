<template >
  <div>
    <div class="flex flex-wrap">
      <div
        :key="indextr"
        v-for="(dato, indextr) in tipo_propiedades"
        class="w-full sm:w-12/12 md:w-12/12 lg:w-12/12 xl:w-12/12 mb-4 px-2"
      >
        <vs-card class="cardx card-tarifas" fixedHeight>
          <div slot="header">
            <h3>{{dato.tipo}}</h3>
          </div>
          <div>
            <div
              :key="indexprecio"
              v-for="(precio, indexprecio) in dato.precios"
              class="flex flex-wrap"
            >
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-2/12 px-2">
                <label class="text-sm opacity-75">
                  <strong>Tipo Precio</strong>
                </label>
                <div class="mt-5" v-if="precio.tipo_precios_id==1">0 - 1 Meses / Contado</div>
                <div class="mt-2" v-else>
                  <vs-button
                    color="danger"
                    type="flat"
                    size="small"
                    icon="remove_circle_outline"
                    @click="remover_tarifa(indextr,indexprecio)"
                  >Quitar - {{precio.meses}} Meses</vs-button>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-2/12 px-2">
                <label class="text-sm opacity-75">
                  <strong>Meses</strong>
                </label>
                <vs-input
                  v-if="precio.tipo_precios_id==1"
                  type="text"
                  class="w-full pb-1 pt-1"
                  v-model="precio.meses"
                  :disabled="meses(precio.tipo_precios_id)"
                />
                <vs-input
                  v-else
                  :data-vv-scope="'add-'+indextr"
                  :name="'meses'+indextr+indexprecio"
                  data-vv-as="Meses"
                  data-vv-validate-on="blur"
                  v-validate="'required|numeric|min:1|max:2|between:2,64'"
                  maxlength="2"
                  type="text"
                  class="w-full pb-1 pt-1"
                  v-model="precio.meses"
                  placeholder="Meses"
                  :disabled="meses(precio.tipo_precios_id)"
                />
                <div>
                  <span
                    class="text-danger text-sm"
                  >{{ errors.first(('meses'+indextr+indexprecio),('add-'+indextr)) }}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75">
                  <strong>Precio Neto</strong>
                </label>
                <vs-input
                  :data-vv-scope="'add-'+indextr"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Precio"
                  v-model="precio.precio_neto"
                  :name="'precio'+indextr+indexprecio"
                  data-vv-as="Precio Neto"
                  data-vv-validate-on="blur"
                  v-validate="'required|min_value:1|max_value:500000'"
                  maxlength="6"
                />
                <div>
                  <span
                    class="text-danger text-sm"
                  >{{ errors.first(('precio'+indextr+indexprecio),('add-'+indextr)) }}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75">
                  <strong>Pago Inicial</strong>
                </label>
                <vs-input
                  v-if="precio.tipo_precios_id==1"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Pago Inicial"
                  v-model="precio.precio_neto"
                  :disabled="meses(precio.tipo_precios_id)"
                />
                <vs-input
                  v-else
                  :data-vv-scope="'add-'+indextr"
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Pago Inicial"
                  v-model="precio.enganche_inicial"
                  :disabled="meses(precio.tipo_precios_id)"
                  :name="'pago_inicial'+indextr+indexprecio"
                  data-vv-as="Pago Inicial"
                  data-vv-validate-on="blur"
                  v-validate="'required|min_value:1|max_value:500000'"
                  maxlength="6"
                />
                <div>
                  <span
                    class="text-danger text-sm"
                  >{{ errors.first(('pago_inicial'+indextr+indexprecio),('add-'+indextr)) }}</span>
                </div>

                <div v-if="errores[indexprecio + '.' + 'enganche_inicial'] && index_datos==indextr">
                  <span
                    class="text-danger text-sm"
                  >{{errores[indexprecio + '.' + 'enganche_inicial'][0]}}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-2/12 px-2">
                <label class="text-sm opacity-75">
                  <strong>Pagos Mensuales</strong>
                </label>
                <div
                  v-if="precio.tipo_precios_id==1"
                  class="mt-5 pago_mensual"
                >$ {{0 | numFormat('0,000.00')}} Pesos.</div>
                <div
                  v-else
                  class="mt-5 pago_mensual"
                >$ {{((precio.precio_neto-precio.enganche_inicial)/precio.meses) | numFormat('0,000.00')}} Pesos.</div>
              </div>
            </div>
            <div class="flex flex-wrap pt-4">
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <div class="mt-5">
                  <p
                    class="precio_contado"
                  >$ {{tipo_propiedades[indextr].precios[0].precio_neto | numFormat('0,000.00')}} Pesos</p>
                </div>
                <div class="mt-5">
                  <p class="precio_contado_dato">Precio de Contado / Uso Inmediato</p>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <div class="mt-5">
                  <vs-button
                    type="flat"
                    size="small"
                    class="float-right"
                    color="success"
                    icon="add_circle_outline"
                    @click="agregar_tarifa(indextr)"
                  >Nuevo</vs-button>
                </div>
              </div>
            </div>
          </div>
          <vs-divider />
          <div>
            <div class="flex flex-wrap">
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <p>Última Actualización: {{tipo_propiedades[indextr].precios[0].fecha_hora}}</p>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2">
                <vs-button
                  size="small"
                  class="float-right"
                  color="success"
                  icon="add_circle_outline"
                  @click="abrirPassword(tipo_propiedades[indextr],indextr)"
                >Guardar</vs-button>
              </div>
            </div>
          </div>
        </vs-card>
      </div>
    </div>
    <!--componente de confirmar sin contraseña-->
    <Confirmar
      :show="operConfirmar"
      :callback-on-success="callback"
      @closeVerificar="operConfirmar=false"
      :accion="'¿Desea eliminar este plan de mensualidades? Los datos quedarán eliminados del sistema.'"
      :confirmarButton="'Eliminar'"
    ></Confirmar>

    <Password
      :show="openPassword"
      :callback-on-success="callbackPassword"
      @closeVerificar="openPassword=false"
      :accion="accionPassword"
    ></Password>
    <!--fin de compornentes-->
  </div>
</template>
<script>
import Confirmar from "../../../Confirmar";
import Password from "../../../confirmar_password";
import cementerio from "@services/cementerio";
export default {
  components: {
    Confirmar,
    Password
  },
  data() {
    return {
      tipo_propiedades: [],
      errores: [],
      operConfirmar: false,
      openPassword: false,
      accionPassword: "",
      callback: Function,
      callbackPassword: Function,
      propiedad_id: "",
      precio_id: "",

      //datos que mando para actualizar
      datos_actualizar: [],
      index_datos: 0
    };
  },
  computed: {},
  methods: {
    //traigo los tipos de propieades que hay
    precios_tarifas() {
      cementerio
        .precios_tarifas()
        .then(res => {
          this.tipo_propiedades = res.data;
        })
        .catch(err => {});
    },

    //agregar tarifa
    agregar_tarifa(index) {
      //verifico si todos los datos estan completos para dejarle agregar nuevos
      let errores_de_captura_en_datos = 0;
      this.tipo_propiedades[index].precios.forEach(element => {
        if (
          element.meses === "" ||
          element.precio_neto === "" ||
          element.enganche_inicial === ""
        ) {
          errores_de_captura_en_datos += 1;
        }
      });
      if (errores_de_captura_en_datos > 0) {
        //no paso
        this.$vs.notify({
          title: "Error",
          text: "Verifique que todos los datos han sido capturados",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "danger",
          position: "top-center",
          time: "4000"
        });
      } else {
        //si paso la prueba de toodos los datos
        this.tipo_propiedades[index].precios.push({
          precio_neto: "",
          meses: "",
          enganche_inicial: "",
          tipo_precios_id: 2
        });
      }
    },
    //remover tarifa
    remover_tarifa(indextr, indexprecio) {
      this.propiedad_id = indextr;
      this.precio_id = indexprecio;

      this.callback = this.remover_tarifa_callback;
      this.operConfirmar = true;
    },

    remover_tarifa_callback() {
      this.tipo_propiedades[this.propiedad_id].precios.splice(
        this.precio_id,
        1
      );
    },

    meses(aplica_mes) {
      if (aplica_mes == 1) {
        return true;
      } else return false;
    },

    //funcion que manda actualizar los datos 'primero abrimos el password checjker y dejamos los datos listos para madanr'
    abrirPassword(datos, index_datos) {
      this.$validator
        .validate("add-" + index_datos + ".*")
        .then(result => {
          if (!result) {
            return;
          } else {
            this.errores = [];
            //se confirma la cntraseña
            this.openPassword = true;
            //aqui creo los datos a actualizar
            this.datos_actualizar = datos.precios;
            this.index_datos = index_datos;
            this.callbackPassword = this.ActualizarDatos;
          }
        })
        .catch(() => {});
    },

    ActualizarDatos() {
      //aqui mando actualizar los datos
      cementerio
        .actualizar_precios_tarifas(this.datos_actualizar)
        .then(res => {
          console.log(res);
        })
        .catch(err => {
          //checo si existe cada error
          this.errores = err.response.data.error;
          for (let index = 0; index < this.datos_actualizar.length; index++) {
            if (err.response.data.error[index + "." + "enganche_inicial"]) {
              console.log(
                err.response.data.error[index + "." + "enganche_inicial"][0]
              );
            }
          }
        });
    }
  },
  created() {
    this.precios_tarifas();
  }
};
</script>

<style lang="scss" scoped>
.precio_contado {
  color: rgba(var(--vs-primary), 1) !important;
  font-size: 1.25rem !important;
}

.precio_contado_dato {
  font-size: 1rem;
  color: rgba(var(--vs-textos_theme), 1) !important;
}

.pago_mensual {
  font-size: 1rem;
  color: rgba(var(--vs-primary), 1) !important;
}

[dir] .vs-button.small:not(.includeIconOnly) {
  font-size: 0.85rem !important;
}
</style>