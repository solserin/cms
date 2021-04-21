import axios from "@/axios.js";
import axiosSuper from "axios";
const CancelToken = axiosSuper.CancelToken;
const source = CancelToken.source();
let cancel;

export default {
    cancel: null,
    get_areas_firmar(id_documento) {
        return axios.get("/firmas/get_areas_firmar/"+id_documento);
    },
};
