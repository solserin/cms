import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    get_personal_recoger() {
        return axios.get("/funeraria/get_personal_recoger/");
    },
    guardar_solicitud(param) {
        let call = "/funeraria/control_solicitud/agregar";
        return axios.post(call, param);
    },

    modificar_solicitud(param) {
        let call = "/funeraria/control_solicitud/modificar";
        return axios.post(call, param);
    },

    get_solicitudes_servicios(param) {
        return axios.get("/funeraria/get_solicitudes_servicios/all/paginated", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_solicitudes_servicios_id(param) {
        return axios.get("/funeraria/get_solicitudes_servicios/" + param, {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    /**serviios del modulo */

    get_categorias() {
        return axios.get("/inventario/get_categorias/");
    },

    get_unidades() {
        return axios.get("/inventario/get_unidades/");
    },

    get_sat_unidades() {
        return axios.get("/inventario/get_sat_unidades/");
    },

    guardar_articulo(param) {
        let call = "/inventario/control_articulos/agregar";
        return axios.post(call, param);
    },

    modificar_articulo(param) {
        let call = "/inventario/control_articulos/modificar";
        return axios.post(call, param);
    },

    get_ajustes(param) {
        return axios.get("/inventario/get_ajustes/all/paginated", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    get_inventariable(param) {
        return axios.get("/inventario/get_inventario/all/paginated/0/0/0/1", {
            cancelToken: new CancelToken(c => {
                self.cancel = c;
            }),
            params: param
        });
    },

    enable_disable(param) {
        let call = "/inventario/enable_disable/enable";
        return axios.post(call, param);
    },
    delete_articulo(param) {
        let call = "/inventario/enable_disable/disable";
        return axios.post(call, param);
    }
};
