<template>
   <div class="main">
      <form class="form-signin" :class="{
            'was-validated': wasValid
         }" @submit.prevent="login">
         <img class="mb-4" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
         <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
         <input type="text" name="username" class="form-control" placeholder="Email address" required autofocus>
         <input type="password" name="password" class="form-control" placeholder="Password" required>
         
         <button class="btn btn-lg btn-primary btn-block mt-3" type="submit">Sign in</button>
         <p class="mt-5 mb-3 text-muted">&copy; 2020</p>
      </form>
   </div>
</template>
<style lang="scss" scoped>
   .main {
      display: flex;
      align-items: center;
      justify-content: center;

      padding: {
         top: 40px;
         bottom: 40px;
      }

      ;
      text-align: center;
      background-color: #f5f5f5;

      .form-signin {
         width: 100%;
         max-width: 330px;
         padding: 15px;
         margin: 0 auto;

         .checkbox {
            font-weight: 400;
         }

         .form-control {
            position: relative;
            box-sizing: border-box;
            height: auto;
            padding: 10px;
            font-size: 16px;

            &:focus {
               z-index: 2;
            }
         }

         input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;


         }

         input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
         }
      }
   }
</style>
<script>
   import axios from "axios"
   export default {
      data() {
         return {
            wasValid: false
         }
      },
      methods: {
         login({ target }) {
            if (target.checkValidity()) {

               let loading = this.$loading.show({
                  width: 50,
                  height: 50,
                  container: null
               })

               this.$axios.post("http://localhost:8001/admin/api/login.php", new FormData(target))
                  .then(res => { if (typeof res.data == "object") return res.data; try { return JSON.parse(res.data) } catch (e) { return { error: 1, mess: res.data } } })
                  .then(json => {
                     
                     if (json.error == 1) {
                        throw new Error(json.mess)
                     }

                     this.$AppSuccess("Success", "Login success.")
                     this.$store.dispatch("currentUser")
                        .then(json => {
                           setTimeout(() => {
                              if (this.$route.params.url) {
                                 this.$router.push(this.$route.params.url)
                              } else {
                                 this.$router.push("/")
                              }
                           })
                        })
                  })
                  .catch(({ stack, message }) => {
                     
                     this.$AppError(message, stack)
                     this.wasValid = true
                  })
                  .finally(() => loading.hide())
            } else {
               this.wasValid = true
            }
         },
         checkLogined() {
            this.$store.dispatch("currentUser")
               .then(json => {
                  if (json.logined) {
                     this.$router.replace("/")
                  }
               })
         }
      },
      created() {
         this.checkLogined()
      }
   }
</script>