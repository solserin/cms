import axios from "@/axios.js"
import axiosSuper from 'axios'
const CancelToken = axiosSuper.CancelToken
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    //obtiene la distribucion del cementerio
    getDistribucion() {
        let call = "/inventarios/cementerio/get_cementerio"
        return new Promise((resolve, reject) => {
            axios.get(call)
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    getDistribucionById(id) {
        let call = "/inventarios/cementerio/propiedadesById"
        return new Promise((resolve, reject) => {
            axios.get(call, {
                    params: {
                        id_propiedad: id
                    }
                })
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },





    //obtengo todos los usuarios para poder aseginar cual es vendedor y cual no
    getUsuariosVendedores() {
        let self = this
        return new Promise((resolve, reject) => {
            axios.get('/inventarios/cementerio/get_usuarios_para_vendedores')
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    if (axiosSuper.isCancel(error)) {
                        reject(error.message)
                    } else {
                        reject(error)
                    }
                })
        })
    },


    //obtengo los posibles tipos de venta para la propiedad
    get_ventas_referencias_propiedades() {
        let self = this
        return new Promise((resolve, reject) => {
            axios.get('/inventarios/cementerio/get_ventas_referencias_propiedades')
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    if (axiosSuper.isCancel(error)) {
                        reject(error.message)
                    } else {
                        reject(error)
                    }
                })
        })
    },
}
