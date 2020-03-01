import axios from "@/http/axios/index.js"

export default {
  updateRegistroPublico(data) {
    return axios.post('/empresa/registro-publico', data)
  },
  getRegistroPublico() {
    return axios.get('/empresa/registro-publico')
  },
  updateFuneraria(data) {
    return axios.post('/empresa/funeraria', data)
  },
  getFuneraria() {
    return axios.get('/empresa/funeraria')
  },
  updateCementerio(data) {
    return axios.post('/empresa/cementerio', data)
  },
  getCementerio() {
    return axios.get('/empresa/cementerio')
  },
  updateCrematorio(data) {
    return axios.post('/empresa/crematorio', data)
  },
  getCrematorio() {
    return axios.get('/empresa/crematorio')
  },
  updateVelatorio(data) {
    return axios.post('/empresa/velatorio', data)
  },
  getVelatorio() {
    return axios.get('/empresa/velatorio')
  },
  getFacturacion() {
    return axios.get('/empresa/facturacion')
  },
  uploadFacturacion(data) {
    return axios.post('/empresa/facturacion', data, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  }
}