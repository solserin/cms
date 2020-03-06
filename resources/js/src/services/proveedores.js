import axios from '@/http/axios/index.js'
import axiosSuper from 'axios'
const CancelToken = axiosSuper.CancelToken

export default {
    cancel: null,
    create(data) {
        return axios.post('/empresa/inventario/proveedores', data)
    },
    update(id, data) {
        return axios.put('/empresa/inventario/proveedores/' + id, data)
    },
    getAll(param) {
        let self = this
        return axios.get('/empresa/inventario/proveedores', {
            cancelToken: new CancelToken((c) => {
                self.cancel = c
            }),
            params: param
        })
    }
}