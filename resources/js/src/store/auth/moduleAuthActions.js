/*=========================================================================================
  File Name: moduleAuthActions.js
  Description: Auth Module Actions
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

import jwt from "../../http/requests/auth/jwt/index.js"
import axios from "../../http/axios/index"

import router from '@/router'

export default {
    // JWT
    loginJWT({ commit }, payload) {

      return new Promise((resolve,reject) => {
        jwt.login(payload.userDetails.email, payload.userDetails.password)
          .then(response => {
            // If there's user data in response
            if(response.data) {
              // Navigate User to homepage
              router.push(router.currentRoute.query.to || '/').catch(err => {})

              // Set accessToken
              localStorage.setItem("accessToken", response.data.access_token)

              // Set bearer token in axios
               commit("SET_BEARER", response.data.access_token)

              // Update user details(perfil del usuario)
              commit('UPDATE_USER_INFO', payload.userDetails.email, {root: true})

              resolve(response)
            }else {
              reject({message: "Error de usuario y/o contraseña."})
            }
          })
          .catch(error => { reject(error) })
      })
    },

    logout_user({ commit }) {
        return new Promise((resolve,reject)=>{
            axios.post("http://app.laravel/logout_usuario").then(resp=>{
                if(resp.code){
                    reject({message:"Error al cerrar sesion"})
                }else{
                    commit("LOGOUT_USER")
                    // Navigate User to login
                    router.push('/pages/login').catch(() => {})
                    resolve(resp)
                }
            }).catch(err=>{
                reject(err)
            })
        })
      },

    registerUserJWT({ commit }, payload) {

      const { displayName, email, password, confirmPassword } = payload.userDetails

      return new Promise((resolve,reject) => {

        // Check confirm password
        if(password !== confirmPassword) {
          reject({message: "Password doesn't match. Please try again."})
        }

        jwt.registerUser(displayName, email, password)
          .then(response => {
            // Redirect User
            //router.push(router.currentRoute.query.to || '/')

            // Update data in localStorage
            localStorage.setItem("accessToken", response.data.accessToken)
            commit('UPDATE_USER_INFO', response.data.userData, {root: true})

            resolve(response)
          })
          .catch(error => { reject(error) })
      })
    },
    fetchAccessToken() {
      return new Promise((resolve) => {
        jwt.refreshToken().then(response => { resolve(response) })
      })
    }
}
