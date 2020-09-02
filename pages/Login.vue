<template>
   <div class="main">
      <form class="form-signin" :class="{
            'was-validated': wasValid
         }" @submit.prevent="login">
         <img class="mb-4" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
         <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
         <input type="email" name="email" class="form-control" placeholder="Email address" required>
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

               this.$store.dispatch("Login", new FormData(target))
                  .then(json => {
                     if (this.$route.params.url) {
                        this.$router.push(this.$route.params.url)
                     } else {
                        this.$router.push("/")
                     }
                  })
                  .catch(({ stack, message }) => {
                     this.$error(message, stack)
                     this.wasValid = true
                  })
                  .finally(() => loading.hide())
            } else {
               this.wasValid = true
            }
         }
      },
      created() {
         if ( this.$store.state.currentUser ) {
            this.$router.$push("/")
         }
      }
   }
</script>