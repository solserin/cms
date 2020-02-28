<!-- =========================================================================================
    File Name: ForgotPassword.vue
    Description: FOrgot Password Page
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
  <div class="h-screen flex w-full bg-img">
    <div class="vx-col w-4/5 sm:w-4/5 md:w-3/5 lg:w-3/4 xl:w-3/5 mx-auto self-center">
      <vx-card>
        <div slot="no-body" class="full-page-bg-color">
          <div class="vx-row">
            <div class="vx-col hidden sm:hidden md:hidden lg:block lg:w-1/2 mx-auto self-center">
              <img src="@assets/images/pages/forgot-password.png" alt="login" class="mx-auto" />
            </div>
            <div class="vx-col sm:w-full md:w-full lg:w-1/2 mx-auto self-center d-theme-dark-bg">
              <div class="p-8">
                <div class="vx-card__title mb-8">
                  <h4 class="mb-4">Recuperar su contraseña</h4>
                  <p>Por favor ingrese su correo electrónico a donde enviaremos las instrucciones para recuperar su cuenta.</p>
                </div>
                <div class="vx-col sm:w-full pb-6">
                  <form @submit.prevent="submitForm">
                    <vs-input
                      type="email"
                      v-validate="'required|email|min:3'"
                      data-vv-validate-on="blur"
                      label-placeholder="Email (Usuario)"
                      v-model="email"
                      icon="icon icon-user"
                      icon-pack="feather"
                      class="w-full mb-4"
                      name="email"
                      data-vv-as="email"
                    />
                  </form>
                  <span class="text-danger text-sm">{{ errors.first('email') }}</span>
                </div>
                <vs-button type="border" to="/pages/login" class="px-4 w-full md:w-auto">Regresar</vs-button>
                <vs-button
                  class="float-right px-4 w-full md:w-auto mt-3 mb-8 md:mt-0 md:mb-0"
                  @click.prevent="submitForm"
                >Recuperar Contraseña</vs-button>
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
export default {
  data() {
    return {
      email: ""
    };
  },
  methods: {
    submitForm() {
      this.$validator.validateAll().then(result => {
        if (result) {
          const payload = {
            email: this.email
          };
          this.$vs.loading();
          axios
            .post("/password/email", payload)
            .then(resp => {
              //exito con la peticion
              this.$vs.notify({
                time: 6000,
                title: "Recuperar contraseña",
                text: resp.data,
                color: "success"
              });
              this.email = "";
              this.$vs.loading.close();
            })
            .catch(error => {
              if (error) {
                this.$vs.notify({
                  time: 6000,
                  title: "Recuperar contraseña",
                  text: "Usuario no registrado, intente nuevamente.",
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