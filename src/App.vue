<template>
   <div id="app">
      <vue-progress-bar />
      <notifications position="top left" group="App" width="100%" />
      <Header-App />
      <router-view />
   </div>
</template>
<style>
   .notification-content {
      word-wrap: wrap !important;
   }
</style>
<script>
   import HeaderApp from "./components/HeaderApp.vue"
   import 'vue-loading-overlay/dist/vue-loading.css';
   import 'bootstrap/scss/bootstrap.scss'
   import '@/awesome/scss/fontawesome.scss'
   import '@/awesome/scss/brands.scss'
   import '@/awesome/scss/duotone.scss'
   import '@/awesome/scss/light.scss'
   import '@/awesome/scss/regular.scss'
   import '@/awesome/scss/solid.scss'
   import '@/awesome/scss/v4-shims.scss'
   
   export default {
      components: { HeaderApp },
      mounted() {
         //  [App.vue specific] When App.vue is finish loading finish the progress bar
         this.$Progress.finish()
      },
      created() {
         //  [App.vue specific] When App.vue is first loaded start the progress bar
         this.$Progress.start()
         //  hook the progress bar to start before we move router-view
         this.$router.beforeEach((to, from, next) => {
            //  does the page we want to go to have a meta.progress object
            if (to.meta.progress !== undefined) {
               let meta = to.meta.progress
               // parse meta tags
               this.$Progress.parseMeta(meta)
            }
            //  start the progress bar
            this.$Progress.start()
            //  continue to next page
            next()
         })
         //  hook the progress bar to finish after we've finished moving router-view
         this.$router.afterEach((to, from) => {
            //  finish the progress bar
            this.$Progress.finish()
            
            let fn = to.meta._title || to.meta.title
            document.head.getElementsByTagName("title")[0].textContent = document.title = typeof fn == "function" ? fn(this.$route) : fn
         })

         this.$store.dispatch("fetchUser")
            .then(json => {
               if ( json.state.error || !json.data ) {
                  throw new Error( json.state.message )
               }
            })
            .catch(() => this.$router.push("/login"))
      }
   }
</script>
