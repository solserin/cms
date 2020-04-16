import axios from "@/axios.js"
import axiosSuper from 'axios'
const CancelToken = axiosSuper.CancelToken
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    /**obtengo el blob del pdf */
    get_pdf(service_end_point, param) {
        return new Promise((resolve, reject) => {
            axios.get(service_end_point, {
                    responseType: "blob",
                    params: param
                })
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    send_pdf(service_end_point, param) {
        return new Promise((resolve, reject) => {
            axios.get(service_end_point, {
                    params: param
                })
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },


}
