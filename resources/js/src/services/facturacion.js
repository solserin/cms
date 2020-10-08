import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,
    get_tipos_comprobante() {
        return axios.get('/facturacion/get_tipos_comprobante')
    },
    get_sat_formas_pago() {
        return axios.get('/facturacion/get_sat_formas_pago')
    },
    get_metodos_pago() {
        return axios.get('/facturacion/get_metodos_pago')
    },
    get_tipos_relacion() {
        return axios.get('/facturacion/get_tipos_relacion')
    },
    get_claves_productos_sat() {
        return axios.get('/facturacion/get_claves_productos_sat')
    },
    get_sat_unidades() {
        return axios.get('/facturacion/get_sat_unidades')
    },


};
