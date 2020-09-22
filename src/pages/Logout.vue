<template>
   <div class="main" v-if="$store.state.currentUser">
      <div class="logout">
         <div class="header">
            <img class="avatar" :src="$store.state.currentUser.photoURL">
            <h5 class="name"> {{ $store.state.displayName }} </h5>
         </div>
         <div class="loading mt-3">
            <b-spinner type="border" class="mr-3"/> Logout...
         </div>
      </div>
   </div>
</template>
<style lang="scss" scoped>
   .main {
      padding: {
         top: 40px;
         bottom: 40px;
      }

      ;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;

      .logout {
         .header {
            .avatar {
               width: 2.5rem;
               height: 2.5rem;
               border: 1px solid rgba(0, 0, 0, .1);
               border-radius: 50%;
            }

            .name {
               font-weight: 500;
            }
         }

         .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            margin-top: 3rem;
         }
      }
   }
</style>
<script>
   export default {
      created() {
         this.$store.dispatch("Logout")
            .then(json => {
               if (json.state.error) {
                  throw new Error(json.state.message)
               } else {
                  this.$success("Success", "Logout!")
                  this.$router.push("/login")
               }
            })
            .catch(({ message, stack }) => {
               this.$error(message, stack)
            })
      }
   }
</script>