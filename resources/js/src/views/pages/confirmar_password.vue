<template >
  <div class="centerx">
    <vs-prompt
      type="confirm"
      title="Confirmar contraseña"
      class="password-checker"
      @cancel="cancel"
      @accept="acceptAlert"
      :active.sync="showChecker"
      accept-text="Confirmar"
      cancel-text="(Esc) Cancelar"
    >
      <div class="con-exemple-prompt">
        Ingrese su contraseña para
        <span class="accion_nombre">{{accionNombre}}</span>.
        <vs-input
          id="password"
          ref="password"
          type="password"
          class="w-full pt-3 pb-3"
          placeholder="Contraseña"
          v-model.trim="pass"
          @keyup.enter="acceptAlert"
        />
      </div>
    </vs-prompt>
  </div>
</template>
<script>
import usuarios from "@services/Usuarios";
export default {
  props: {
    show: {
      type: Boolean,
      required: true
    },
    callbackOnSuccess: {
      type: Function,
      required: true
    },
    accion: {
      type: String,
      required: true
    }
  },

  data() {
    return {
      pass: ""
    };
  },
  computed: {
    validPassword() {
      return !!this.pass;
    },
    showChecker: {
      get() {
        return this.show;
      },
      set(newValue) {
        return newValue;
      }
    },
    accionNombre() {
      return this.accion;
    }
  },
  methods: {
    acceptAlert() {
      if (!this.validPassword) {
        this.pass = "";
        return;
      }
      if (usuarios.cancel) {
        usuarios.cancel("Operation canceled by the user.");
      }
      //se verificq que exista una contraseña y se procede a realizar la confirmacion al servidor
      usuarios
        .confirmPassword(this.pass)
        .then(res => {
          if (res.status == 200) {
            //preocede a cimplir la peticion
            this.pass = "";
            this.cancel();
            this.callbackOnSuccess();
          }
        })
        .catch(err => {
          this.$vs.notify({
            title: "Permiso denegado",
            text: err.response.data.error,
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-center",
            time: "4000"
          });
          this.pass = "";
        });
    },
    cancel() {
      this.pass = "";
      this.$emit("closeVerificar");
    }
  },
  mounted() {
    //cerrando el confirmar con esc
    document.body.addEventListener("keyup", e => {
      if (e.keyCode === 27) {
        if (this.showChecker) {
          //CIERRO EL CONFIRMAR AL PRESONAR ESC
          this.cancel();
        }
      }
    });
  }
};
</script>