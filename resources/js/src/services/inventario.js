import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    get_tipo_articulos() {
        return axios.get("/inventario/get_tipo_articulos/");
    },


    get_categorias() {
        return axios.get("/inventario/get_categorias/");
    },

    get_unidades() {
        return axios.get("/inventario/get_unidades/");
    },

    get_sat_unidades() {
        return axios.get("/inventario/get_sat_unidades/");
    }
};
