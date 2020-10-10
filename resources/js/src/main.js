/*=========================================================================================
  File Name: main.js
  Description: main vue(js) file
  ----------------------------------------------------------------------------------------
  Item Name: Vuesax Admin - VueJS Dashboard Admin Template
  Author: Pixinvent
  Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/

import Vue from "vue";
import App from "./App.vue";

// Vuesax Component Framework
import Vuesax from "vuesax";

Vue.use(Vuesax);

// axios
import axios from "./axios.js";
Vue.prototype.$http = axios;

// API Calls
import "./http/requests";

// mock
import "./fake-db/index.js";

// Theme Configurations
import "../themeConfig.js";

// ACL
import acl from "./acl/acl";

// Globally Registered Components
import "./globalComponents.js";

// Vue Router
import router from "./router";

// Vuex Store
import store from "./store/store";

// i18n
import i18n from "./i18n/i18n";

// Vuesax Admin Filters
import "./filters/filters";

// Clipboard
import VueClipboard from "vue-clipboard2";
Vue.use(VueClipboard);

// Tour
import VueTour from "vue-tour";
Vue.use(VueTour);
require("vue-tour/dist/vue-tour.css");

// VeeValidate
import VeeValidate, { Validator } from "vee-validate";
import es from "vee-validate/dist/locale/es";
Vue.use(VeeValidate);
Validator.localize("es", es);

// Google Maps
import * as VueGoogleMaps from "vue2-google-maps";
Vue.use(VueGoogleMaps, {
    load: {
        // Add your API key here
        key: "AIzaSyB4DDathvvwuwlwnUu7F4Sow3oU22y5T1Y",
        libraries: "places" // This is required if you use the Auto complete plug-in
    }
});

// Vuejs - Vue wrapper for hammerjs
import { VueHammer } from "vue2-hammer";
Vue.use(VueHammer);

// PrismJS
import "prismjs";
// import 'prismjs/themes/prism-tomorrow.css'

// Feather font icon
require("@assets/css/iconfont.css");

Vue.config.productionTip = false;
Vue.config.devtools = false;
Vue.config.errorHandler = function(err, vm, info) {
    //console.log(`Error: ${err.toString()}\nInfo: ${info}`);
    if (info == "v-on handler") {
        /**para manejar este tipo de errores comunes solo actualizo el navegador */
        window.location.reload(true);
    }
};

Vue.config.warnHandler = function(msg, vm, trace) {
    /**que no muestre warnings */
    //console.log(`Warn: ${msg}\nTrace: ${trace}`);
};
/**importo el formnateador de numeros a moneda */
import numeral from "numeral";
import numFormat from "vue-filter-number-format";

Vue.filter("numFormat", numFormat(numeral));
Vue.use(require('vue-moment'));
const moment = require('moment')
require('moment/locale/es')
 
Vue.use(require('vue-moment'), {
    moment
})
 
/**con esta funcion valido si el usuario tiene cierto permiso sobre algun modulo, tomand
 * con parametros la url del modulo y el id del permiso
 */
import { modulo } from "@/ModuloPermisos";
Vue.prototype.$modulo = modulo;

new Vue({
    router,
    store,
    i18n,
    acl,
    render: h => h(App)
}).$mount("#app");
