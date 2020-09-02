import Vue from "vue"
import Vuex from "vuex"
import axios from "axios"

Vue.use(Vuex)
Vuex.Store.prototype.$axios = axios

export default new Vuex.Store({
   state: {
      currentUser: null
   },
   mutations: {
      setCurrentUser(state, payload) {
         state.currentUser = payload
      }
   },
   actions: {
      Login({ commit }, form) {
         return this.$axios.post("https://localhost:8080/admin/api/Login.php", form)
         .then(res => res.data)
         .then(json => {
            if ( !json.state.error ) {
               commit("setCurrentUser", json.data)
            } else {
               throw new Error(json.state.message)
            }
         })
      },
      Logout({ commit }) {
         return this.$axios.post("http://localhost:8080/admin/api/Logout.php")
         .then(res => res.data)
         .then(json => {
            if ( !json.state.error ) {
               commit("setCurrentUser", null)
            } else {
               throw new Error(json.state.message)
            }
         })
      },
      fetchUser({ commit }) {
         return this.$axios.get("http://localhost:8080/admin/api/Login.php")
         .then(res => res.data)
         .then(json => {
            console.log( json )
            if ( !json.state.error ) {
               commit("setCurrentUser", json.data)
            } else {
               throw new Error(json.state.message)
            }
            return json
         })
      }
   }
})