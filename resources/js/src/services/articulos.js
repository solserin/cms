import axios from '@/http/axios/index.js'
import axiosSuper from 'axios'
const CancelToken = axiosSuper.CancelToken

export default {
    cancel: null,
    create(data) {
        return axios.post('/empresa/inventario/articulos', data)
    },
    update(id, data) {
        return axios.put('/empresa/inventario/articulos/' + id, data)
    },
    getAll(param) {
        let self = this
        return axios.get('/empresa/inventario/articulos', {
            cancelToken: new CancelToken((c) => {
                self.cancel = c
            }),
            params: param
        })
    },
    tiposProductos() {
        return axios.get('/tipos-productos')
    },
    grouposProfeco() {
        return axios.get('/grupos-profeco')
    },
    almacenes() {
        return axios.get('/almacenes')
    },
    categorias() {
        return axios.get('/categorias')
    },
    impuestos() {
        return axios.get('/impuestos')
    },
    retenciones() {
        return axios.get('/retenciones')
    },
    unidades() {
        return axios.get('/unidades')
    },
    productosServicios() {
        return axios.get('/productos-servicios')
    }
}