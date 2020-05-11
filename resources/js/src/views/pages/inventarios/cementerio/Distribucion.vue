<template >
  <div class="centerx">
    <!--ordenando primero los tipode propiedad que son linales, tipo uniplex, nicho,duplex, triplex, cuqdriplex sin terraza-->
    <div v-if="getDatosAreas.tipo_propiedades_id!=4"></div>
    <div v-else>
      <!--incia el dibujo de el area segun sus filas y lotes-->
      <div class="flex flex-wrap">
        <div class="w-full px-2" v-if="getDatosAreas.filas>0">
          <vs-table :data="getDatosAreas.filas" noDataText=" ">
            <template slot-scope="{data}">
              <vs-tr :key="index_fila" v-for="(fila, index_fila) in getDatosAreas.filas">
                <vs-td class="bg-primary bold text-white h-16">{{alfabeto[fila-1]}}</vs-td>
                <vs-td
                  :key="index_columna"
                  v-for="(columna, index_columna) in getDatosAreas.columnas"
                  class="hover:bg-primary hover:text-white cursor-pointer bold"
                >{{( getDatosAreas.columnas+1)-columna}}</vs-td>
                <vs-td class="bg-primary bold text-white">{{alfabeto[fila-1]}}</vs-td>
              </vs-tr>
            </template>
          </vs-table>
        </div>
      </div>

      <!--fin de dibujo de mapa-->
    </div>
  </div>
</template>
<script>
/**VARIABLES GLOBALES */
import { alfabeto } from "@/VariablesGlobales";
export default {
  components: {},
  props: {
    datosAreas: {
      required: true
    }
  },
  watch: {},
  computed: {
    getDatosAreas: {
      get() {
        return this.datosAreas;
      },
      set(newValue) {
        return newValue;
      }
    }
  },
  data() {
    return {
      alfabeto: alfabeto
    };
  },
  methods: {},
  created() {}
};
</script>