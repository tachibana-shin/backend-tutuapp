import Vue from "vue"
import VueRouter from "vue-router"

import Editor from "../pages/Editor.vue"
import AppManager from "../pages/AppManager.vue"
import iCloud from "../pages/iCloudManager.vue"
import Error404 from "../pages/404.vue"
import Login from "../pages/Login.vue"
import Logout from "../pages/Logout.vue"
import MyAccount from "../pages/MyAccount.vue"

Vue.use(VueRouter)

const routes = [
   {
      path: "/",
      component: AppManager,
      meta: {
         title: "Admin App Manager - FreeiOS"
      }
   },
   {
      path: "/editor/:id",
      component: Editor,
      meta: {
         title: "Upgrade App - FreeiOS"
      }
   },
   {
      path: "/upload",
      component: Editor,
      meta: {
         upload: true,
         title: "Update App - FreeiOS"
      }
   },
   {
      path: "/icloud",
      component: iCloud,
      meta: {
         title: "iCloud Manager - FreeiOS"
      }
   },
   {
      path: "/login",
      component: Login,
      meta: {
         title: "Login Admin - FreeiOS"
      }
   },
   {
      path: "/myaccount",
      component: MyAccount,
      meta: {
         title: "My Account Admin - FreeiOS"
      }
   },
   {
      path: "/logout",
      component: Logout,
      meta: {
         title: "Logout - FreeiOS"
      }
   },
   {
      path: "*",
      component: Error404,
      meta: {
         title: "404 Not Found - FreeiOS"
      }
   }
]

export default new VueRouter({
   mode: "history",
   base: "/admin/",
   scrollBehavior(to, from, saved) {
      return saved || { x: 0, y : 0 }
   },
   routes,
   linkActiveClass: "active"
})
