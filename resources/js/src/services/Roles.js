import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    get_modulos_permisos() {
        let call = "/get_modulos_permisos";
        return new Promise((resolve, reject) => {
            axios
                .get(call)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    },

    /**agregar rol */
    add_roles(param) {
        return new Promise((resolve, reject) => {
            axios
                .post("/add_roles", param)
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });
        });
    }
};
