import axios from "@/axios.js"
import axiosSuper from 'axios'
const CancelToken = axiosSuper.CancelToken
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,

    //obtiene las propieades del cementerio
    get_cementerio() {
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
    //obtiene los usuarios para cargar los vendedores
    get_vendedores() {
        let call = "/inventarios/cementerio/get_vendedores"
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

    //obtiene las 3 tipos de venta segun la antiguedad
    get_antiguedades() {
        let call = "/inventarios/cementerio/get_antiguedades_venta"
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


    //obtiene las formas de pago con la clave del sat segun las necesidades del sistema
    get_sat_formas_pago() {
        let call = "/inventarios/cementerio/get_sat_formas_pago"
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


    guardarVenta(datos) {
        let call = "/inventarios/cementerio/guardar_venta"
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

        return axios.get('/inventarios/cementerio/get_ventas_referencias_propiedades')


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
        return axios.get(call)
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
