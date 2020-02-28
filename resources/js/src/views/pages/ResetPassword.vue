<!-- =========================================================================================
    File Name: ResetPassword.vue
    Description: Reset Password Page
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
  <div class="h-screen flex w-full bg-img">
    <div class="vx-col sm:w-3/5 md:w-3/5 lg:w-3/4 xl:w-3/5 mx-auto self-center">
      <vx-card>
        <div slot="no-body" class="full-page-bg-color">
          <div class="vx-row">
            <div class="vx-col hidden sm:hidden md:hidden lg:block lg:w-1/2 mx-auto self-center">
              <img src="@assets/images/pages/reset-password.png" alt="login" class="mx-auto" />
            </div>
            <div class="vx-col sm:w-full md:w-full lg:w-1/2 mx-auto self-center d-theme-dark-bg">
              <div class="p-8">
                <div class="vx-card__title mb-8">
                  <h4 class="mb-4">Cambiar Contraseña</h4>
                  <p>Por favor ingrese su correo y su nueva contraseña.</p>
                </div>
                <form @submit.prevent="submitForm">
                  <vs-input
                    type="email"
                    icon="icon icon-user"
                    icon-pack="feather"
                    v-validate="'required|email|min:3'"
                    data-vv-validate-on="blur"
                    label-placeholder="Email"
                    v-model="email"
                    class="w-full mb-8"
                    name="email"
                    data-vv-as="email"
                    v-on:keyup.enter="submitForm"
                  />
                  <span class="text-danger text-sm pb-4">{{ errors.first('email') }}</span>
                  <vs-input
                    icon="icon icon-unlock"
                    icon-pack="feather"
                    type="password"
                    v-validate="'required|min:3'"
                    data-vv-validate-on="blur"
                    label-placeholder="Contraseña"
                    v-model="password"
                    class="w-full mb-8"
                    name="password"
                    data-vv-as="Contraseña"
                    v-on:keyup.enter="submitForm"
                  />
                  <span class="text-danger text-sm pb-4">{{ errors.first('password') }}</span>
                  <vs-input
                    icon="icon icon-unlock"
                    icon-pack="feather"
                    type="password"
                    v-validate="'required|min:3'"
                    data-vv-validate-on="blur"
                    label-placeholder="Confirmar Contraseña"
                    v-model="password_confirmation"
                    class="w-full mb-8"
                    name="confirmar"
                    data-vv-as="Confirmar Contraseña"
                    v-on:keyup.enter="submitForm"
                  />
                  <span class="text-danger text-sm pb-4">{{ errors.first('confirmar') }}</span>
                </form>
                <div class="flex flex-wrap justify-between flex-col-reverse sm:flex-row mt-5">
                  <vs-button
                    type="border"
                    to="/pages/login"
                    class="w-full sm:w-auto mb-8 sm:mb-auto mt-3 sm:mt-auto"
                  >Regresar al login</vs-button>
                  <vs-button class="w-full sm:w-auto" @click.prevent="submitForm">Cambiar</vs-button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </vx-card>
    </div>
  </div>
</template>

<script>
import axios from "../../http/axios/index";
/**VARIABLES GLOBALES */
export default {
  data() {
    return {
      email: "",
      password: "",
      password_confirmation: ""
    };
  },
  methods: {
    submitForm() {
      this.$validator.validateAll().then(result => {
        if (result) {
          const payload = {
            email: this.email,
            password: this.password,
            password_confirmation: this.password_confirmation,
            token: this.$route.params.token
          };
          this.$vs.loading();
          axios
            .post("/password/reset", payload)
            .then(resp => {
              //exito con la peticion
              this.$vs.notify({
                time: 6000,
                title: "Cambiar Contraseña",
                text: resp.data,
                color: "success"
              });
              this.email = "";
              this.password = "";
              this.password_confirmation = "";
              this.$vs.loading.close();
            })
            .catch(error => {
              if (error) {
                this.$vs.notify({
                  time: 8000,
                  title: "Error",
                  text:
                    "Verifique sus datos: [usuario, contraseñas o cree un nuevo correo de renovación de contraseñas] e intente nuevamente.",
                  color: "danger"
                });
              }
              this.$vs.loading.close();
            });
        } else {
          // error de validacion de datos
        }
      });
      return false;
    }
  }
};
</script>
