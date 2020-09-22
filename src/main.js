import Vue from "vue"
import App from "./App.vue"
import BootstrapVue from "bootstrap-vue"
import router from "./router/index.js"
import store from "./store/index.js"
import VueFab from "vue-float-action-button"
import VueLoadingOverlay from "vue-loading-overlay"
import VueProgressBar from "vue-progressbar"
import VueNotification from "vue-notification"
import axios from "axios"

Vue.use(BootstrapVue)
Vue.use(VueFab)
Vue.use(VueLoadingOverlay)
Vue.use(VueProgressBar, {
   color: 'rgb(143, 255, 199)',
   failedColor: "#f00",
   height: '5px'
})
Vue.use(VueNotification)

Vue.mixin({
   mounted() {
      this.$emit("hook:mounted")
   }
})

Vue.prototype.$error = function(title, message) {
   // fix function not render
   const run = () => {
      this.$notify({
         group: "App",
         width: "100%",
         position: "top left",
         title: title || "Error unknown",
         text: message,
         type: "error"
      })
   }
   
   if ( this.$el ) {
      setTimeout(() => run(), 10)
   } else {
      this.$once("hook:mounted", () => run())
   }
}
Vue.prototype.$success = function (title, message) {
   this.$notify({
      group: "App",
      width: "100%",
      position: "top left",
      title: title || "Success",
      text: message,
      type: "success"
   })
}
Vue.prototype.$axios = axios

new Vue({
   el: "#app",
   router,
   store,
   template: "<App/>",
   components: { App }
})