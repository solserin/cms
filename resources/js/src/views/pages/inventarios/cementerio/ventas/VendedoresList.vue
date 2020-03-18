<template>
  <div>
    <div class="mt-2">
      <span class="text-danger text-sm" v-if="this.errores.roles_set">{{errores.roles_set[0]}}</span>
    </div>
    <vs-table stripe noDataText="0 Resultados" :data="modulos">
      <template slot="header">
        <h3 class="pb-5 text-primary">Indique que usuarios están activos en ventas</h3>
      </template>
      <template slot="thead">
        <vs-th>Sección</vs-th>
        <vs-th>Módulo</vs-th>
        <vs-th>Agregar</vs-th>
        <vs-th>Editar</vs-th>
        <vs-th>Eliminar</vs-th>
        <vs-th>Consultar</vs-th>
      </template>
      <template slot-scope="{data}">
        <vs-tr :key="indextr" v-for="(tr, indextr) in modulos">
          <vs-td :data="modulos[indextr].seccion">{{modulos[indextr].seccion}}</vs-td>
          <vs-td :data="modulos[indextr].modulo">{{modulos[indextr].modulo}}</vs-td>
          <vs-td v-for="items in 4" :key="items">
            <vs-checkbox v-model="form.roles_set" :vs-value="modulos[indextr].mod_id+'_'+items"></vs-checkbox>
          </vs-td>
        </vs-tr>
      </template>
    </vs-table>
    <Password
      :show="verConfirmar"
      :callback-on-success="callback"
      @closeVerificar="closeChecker"
      :accion="accionNombre"
    ></Password>
  </div>
</template>

<script>
import cementerio from "@services/cementerio";
import usuarios from "@services/Usuarios";
//componente de password
import Password from "../../../confirmar_password";
/**VARIABLES GLOBALES */
import { rolesOptions } from "../../../../../VariablesGlobales";
import vSelect from "vue-select";

import {
  UserPlusIcon,
  PrinterIcon,
  DeleteIcon,
  PlusCircleIcon
} from "vue-feather-icons";
export default {
  watch: {
    roles: function(newValue, oldValue) {
      this.form.id = this.roles.value;
      this.getPermisosRol();
    }
  },
  components: {
    "v-select": vSelect,
    UserPlusIcon,
    PrinterIcon,
    DeleteIcon,
    PlusCircleIcon,
    Password
  },
  computed: {
    validateForm() {
      return !this.errors.any() && this.form.rol != "";
    }
  },
  data() {
    return {
      accionNombre: "",
      callback: Function,
      verConfirmar: false,
      rolesOptions: [],
      roles: { label: "Seleccione 1", value: "" },
      form: {
        rol: "",
        rol_modificar: "",
        id: "",
        roles_set: []
      },
      errores: [],
      usuarios: []
    };
  },
  methods: {
    get_modulos() {
      cementerio
        .getUsuariosVendedores()
        .then(res => {
          console.log(res);
          this.usuarios = res.data.data;
        })
        .catch(err => {});
    },

    closeChecker() {
      this.verConfirmar = false;
    }
  },
  created() {
    this.get_modulos();
  }
};
</script>

<style lang="scss">
.vs-con-table .vs-con-tbody .con-vs-checkbox {
  -webkit-box-pack: left !important;
  justify-content: left !important;
}
</style>