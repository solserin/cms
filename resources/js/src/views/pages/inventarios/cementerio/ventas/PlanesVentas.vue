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
                    radius
                    color="danger"
                    type="flat"
                    icon="remove_circle_outline"
                    @click="remover_tarifa(indextr,indexprecio)"
                  >{{precio.meses}} Meses</vs-button>
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
                  >{{ errors.first(('meses'+indextr+indexprecio)) }}</span>
                </div>
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-3/12 px-2">
                <label class="text-sm opacity-75">
                  <strong>Precio Neto</strong>
                </label>
                <vs-input
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
                  >{{ errors.first(('precio'+indextr+indexprecio)) }}</span>
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
                  type="text"
                  class="w-full pb-1 pt-1"
                  placeholder="Pago Inicial"
                  v-model="precio.enganche_inicial"
                  :disabled="meses(precio.tipo_precios_id)"
                />
              </div>
              <div class="w-full sm:w-12/12 md:w-6/12 lg:w-6/12 xl:w-2/12 px-2">
                <label class="text-sm opacity-75">
                  <strong>Pagos Mensuales</strong>
                </label>
                <div class="mt-5">{{((precio.precio_neto-precio.enganche_inicial)/precio.meses)}}</div>
              </div>
            </div>
            <div class="w-full px-2">
              <vs-button
                radius
                color="success"
                type="flat"
                icon="add_circle_outline"
                class="float-right mt-4 mb-2"
                @click="agregar_tarifa(indextr)"
              ></vs-button>
            </div>
          </div>
          <vs-divider />
          <div slot="footer">
            <vs-row vs-justify="flex-end"></vs-row>
          </div>
        </vs-card>
      </div>
    </div>
  </div>
</template>
<script>
import cementerio from "@services/cementerio";
export default {
  data() {
    return {
      tipo_propiedades: []
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
      this.tipo_propiedades[index].precios.push({
        precio_neto: "",
        meses: "",
        enganche_inicial: "",
        tipo_precios_id: 2
      });
    },
    //remover tarifa
    remover_tarifa(indextr, indexprecio) {
      console.log(indextr);
      console.log(indexprecio);
      this.tipo_propiedades[indextr].precios.splice(indexprecio, 1);
    },

    meses(aplica_mes) {
      if (aplica_mes == 1) {
        return true;
      } else return false;
    }
  },
  created() {
    this.precios_tarifas();
  }
};
</script>