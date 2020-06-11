<template >
  <div class="centerx">
    <vs-popup
      class="confirmarAceptar confirmar contrasena"
      close="cancelar"
      title="contraseña"
      :active.sync="showChecker"
      ref="contra"
    >
      <div class="text-center password_icono"></div>
      <div class="text-center seguro-mensaje mt-3">Confirmar con Contraseña</div>
      <div class="text-center seguro-texto mt-3">
        Para poder
        <span class="font-semibold text-primary">{{accionNombre}}</span>, es necesario que verifique su identidad mediante contraseña. Esto reduce el riesgo de que usuarios no autorizados realicen operaciones sin su autorización.
      </div>
      <div class="flex flex-wrap mt-2">
        <div class="w-full px-5">
          <vs-input
            maxlength="50"
            size="large"
            ref="contra"
            type="password"
            class="w-full pt-3 pb-3"
            placeholder="Contraseña"
            v-model.trim="pass"
            @keyup.enter="acceptAlert"
          />
        </div>
        <div class="w-full sm:w-6/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 mt-5">
          <div class="mt-2">
            <vs-button
              color="danger"
              class="float-right mr-2"
              size="small"
              type="border"
              @click="cancel"
            >
              <img width="25px" class="cursor-pointer" src="@assets/images/cancel.svg" />
              <span class="texto-btn">(Esc) Cancelar</span>
            </vs-button>
          </div>
        </div>
        <div class="w-full sm:w-6/12 md:w-6/12 lg:w-6/12 xl:w-6/12 px-2 mt-5">
          <div class="mt-2">
            <vs-button size="small" color="success" class="float-left ml-2" @click="acceptAlert">
              <img width="25px" class="cursor-pointer" src="@assets/images/checked.svg" />
              <span class="texto-btn">(Ent) Confirmar</span>
            </vs-button>
          </div>
        </div>
      </div>
    </vs-popup>
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
  watch: {
    show: function(newValue, oldValue) {
      if (newValue == true) {
        this.$nextTick(() =>
          this.$refs["contra"].$el.querySelector("input").focus()
        );
        this.$refs["contra"].$el.querySelector(".vs-icon").onclick = () => {
          this.cancel();
        };
      }
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
          this.$nextTick(() =>
            this.$refs["contra"].$el.querySelector("input").focus()
          );
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