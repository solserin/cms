/*=========================================================================================
  File Name: moduleAuthMutations.js
  Description: Auth Module Mutations
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

import axios from "../../http/axios/index.js"

export default {
  SET_BEARER(state, accessToken) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken
  },
  LOGOUT_USER(state) {
    axios.defaults.headers.common['Authorization'] = ' '
    state.isUserLoggedIn='',
    localStorage.removeItem("accessToken"),
    localStorage.removeItem("userInfo")
  }
}
