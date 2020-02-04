<template>
  <div>
    <vs-input
      v-validate="'required|email|min:3'"
      data-vv-validate-on="blur"
      name="email"
      icon-no-border
      icon="icon icon-user"
      icon-pack="feather"
      label-placeholder="Email"
      v-model="email"
      class="w-full"
    />
    <span class="text-danger text-sm">{{ errors.first('email') }}</span>

    <vs-input
      data-vv-validate-on="blur"
      v-validate="'required|min:6|max:10'"
      type="password"
      name="password"
      icon-no-border
      icon="icon icon-lock"
      icon-pack="feather"
      label-placeholder="Password"
      v-model="password"
      class="w-full mt-6"
    />
    <span class="text-danger text-sm">{{ errors.first('password') }}</span>

    <div class="flex flex-wrap justify-between my-5">
      <vs-checkbox v-model="checkbox_remember_me" class="mb-3">Recordarme</vs-checkbox>
      <router-link to="/pages/forgot-password">Olvidó su contraseña?</router-link>
    </div>
    <div class="flex flex-wrap justify-between mb-3">
      <vs-button :disabled="!validateForm" @click="loginJWT">Login</vs-button>
    </div>
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
    },

    registerUser() {
      if (!this.checkLogin()) return;
      this.$router.push("/pages/register").catch(() => {});
    }
  }
};
</script>

