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


    //obtiene los tipos de propieades que hay
    tipoPropiedades() {
        let self = this
        return new Promise((resolve, reject) => {
            axios.get('/inventarios/cementerio/tipoPropiedades')
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

    //obtiene las propieades segun su tipo
    get_propiedades_by_tipo(id) {
        let call = "/inventarios/cementerio/get_propiedades_by_tipo"
        return new Promise((resolve, reject) => {
            axios.get(call, {
                    params: {
                        id_propiedad_tipo: id
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


    //retorna los datos de columnas_filas para saber en que numero de lote inicia y acaba una fila de una terraza
    get_columna_fila_terraza(propiedades_id, fila) {
        let call = "/inventarios/cementerio/get_columna_fila_terraza"
        return new Promise((resolve, reject) => {
            axios.get(call, {
                    params: {
                        propiedades_id: propiedades_id,
                        fila: fila
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



    //retorna los precios de las diferenres propiedades
    precios_tarifas() {
        let call = "/inventarios/cementerio/precios_tarifas"
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

    //update precios tarifas
    actualizar_precios_tarifas(datos) {
        let call = "/inventarios/cementerio/actualizar_precios_tarifas"
        return new Promise((resolve, reject) => {
            axios.post(call, datos)
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },
}
