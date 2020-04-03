<template>
  <div>
    <div class="flex flex-wrap bg-white login-template font-sans">
      <div class="w-full sm:w-12/12 md:w-7/12 lg:w-7/12 xl:w-7/12">
        <div id="login-informacion" class="h-screen bg-black text-center">
          <img src="../../../../../../public/images/aeternus/siiga.png" class="mt-10" />

          <div class="flex flex-wrap">
            <div class="w-full sm:w-10/12 md:w-8/12 lg:w-8/12 xl:w-8/12 mr-auto ml-auto">
              <p
                class="text-white sistema-title my-5"
              >Sistema Integral de Información y Gerencia Administrativa</p>
              <h1 class="mt-3 text-lg text-white">Aeternus Funerales</h1>
              <p class="mt-3 text-base text-white">©2020 Todos los Derechos Reservados</p>
            </div>
          </div>
        </div>
      </div>
      <div class="w-full sm:w-12/12 md:w-5/12 lg:w-5/12 xl:w-5/12">
        <div class="flex flex-wrap">
          <div class="w-full sm:w-10/12 md:w-8/12 lg:w-8/12 xl:w-8/12 mr-auto ml-auto px-2">
            <div class="login-form justify-center items-center md:mt-32">
              <h4 class="iniciar-sesion my-5">Inicia sesión</h4>
              <div class="w-full my-2">
                <label class="text-sm opacity-75">Correo electrónico</label>
                <vs-input
                  v-validate="'required|email|min:3'"
                  name="email"
                  icon-no-border
                  data-vv-as=" "
                  icon="icon icon-user"
                  icon-pack="feather"
                  placeholder="Correo electrónico"
                  v-model="email"
                  class="w-full pb-1 pt-1"
                />
                <span class="text-danger text-sm">{{ errors.first('email') }}</span>
              </div>
              <div class="w-full">
                <label class="text-sm opacity-75">Contraseña</label>
                <vs-input
                  v-validate="'required|min:6|max:25'"
                  type="password"
                  name="password"
                  data-vv-as=" "
                  icon-no-border
                  icon="icon icon-lock"
                  icon-pack="feather"
                  placeholder="Contraseña"
                  v-model="password"
                  class="w-full pb-1 pt-1"
                  v-on:keyup.enter="loginJWT"
                />
                <span class="text-danger text-sm">{{ errors.first('password') }}</span>
              </div>

              <div
                class="flex flex-wrap justify-between my-5 text-sm text-black"
              >Esta página está protegida y sujeta a las Políticas de privacidad y a las Condiciones de la empresa. Acceso solo a personal autorizado.</div>

              <!-- <div class="flex flex-wrap justify-between my-5">
                <vs-checkbox v-model="checkbox_remember_me" class="mb-3 hidden">Recordarme</vs-checkbox>
              </div>
              -->
              <div class="flex flex-wrap justify-between">
                <vs-button class="w-full font-medium" @click="loginJWT">Inicia sesión</vs-button>
              </div>

              <div class="flex flex-wrap justify-between py-10">
                <router-link to="/pages/forgot-password">¿Olvidaste tu contraseña?</router-link>
              </div>

              <div class="flex flex-wrap justify-between py-10">
                <a href="#" target="_blank" class="tutorial text-base">
                  <feather-icon icon="PlayIcon" class="mr-2" svgClasses="w-5 h-5" />
                  <span class="mt-10 text-black">Ver guía de usuario "Inicio de Sesión"</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="vx-col sm:w-1/2 md:w-1/2 lg:w-3/4 xl:w-3/5 sm:m-0 m-4"></div>
  </div>
</template>


<script>
import { mapGetters } from "vuex";
import store from "../../../store/store";
export default {
  data() {
    return {
      email: "admin@admin.com",
      password: "password",
      checkbox_remember_me: false
    };
  },
  computed: {
    validateForm() {
      return !this.errors.any() && this.email != "" && this.password != "";
    },
    ...mapGetters({
      isLoggedIn: "auth/isLoggedIn"
    })
  },
  methods: {
    checkLogin() {
      // If user is already logged in notify
      if (localStorage.getItem("accessToken")) {
        location.reload();
        return false;
        // Close animation if passed as payload
        // this.$vs.loading.close()
        this.$vs.notify({
          title: "Atención",
          text: "Su cuenta ya ha iniciado sesión!",
          iconPack: "feather",
          icon: "icon-alert-circle",
          color: "warning"
        });
        return false;
      }
      return true;
    },
    loginJWT() {
      if (!this.checkLogin()) return;

      // Loading
      this.$vs.loading();

      const payload = {
        checkbox_remember_me: this.checkbox_remember_me,
        userDetails: {
          email: this.email,
          password: this.password
        }
      };

      this.$store
        .dispatch("auth/loginJWT", payload)
        .then(() => {
          this.$vs.loading.close();
        })
        .catch(error => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: "Error",
            text: "Debe ingresar un usuario y contraseña correcto!",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger"
          });
        });
    }
  }
};
</script>

<style lang="scss">
.login-tabs-container {
  min-height: 505px;

  .con-tab {
    padding-bottom: 14px;
  }

  .con-slot-tabs {
    margin-top: 1rem;
  }
}
</style>
