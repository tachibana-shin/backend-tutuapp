<template>
   <div class="main">
      <div class="requestPassword" v-if="!renderPage">
         <form ref="FormDataConfirmPassword" class="form-group">
            <label> Enter Password </label>
            <div class="group-password">
               <input type="password" name="password" class="form-control" placeholder="Password">
            </div>
            <b-button variant="primary" block @click="confirmPassword" class="mt-3"> Continue </b-button>
            <small class="text-secondary"> Để tiếp tục bạn cần nhập mật khẩu. </small>
         </form>
      </div>
      <div class="privacy" v-if="renderPage">
         <!--<h1 class="title"> Quản lý tài khoản </h1>-->
         <div class="content">
            <div class="form-group">
               <label> Email </label>
               <input type="email" placeholder="Example: example@example.com" class="form-control">
               <b-button @click="saveEmail"> Save </b-button>
            </div>
            
            <form ref="FormDataChangePassword" class="form-group">
               <label> Change Password </label>
               <div class="group-password">
                  <input type="password" class="form-control" name="newPassword" placeholder="New Password">
               </div>
               <b-button @click="savePassword"> Save </b-button>
            </form>
         </div>
      </div>
   </div>
</template>
<style lang="scss" scoped>
   .main {
      padding: {
         left: 15px;
         right: 15px;
         top: 40px;
         bottom: 40px;
      }


      .content {
         .form-group {
            .group-password {
               position: relative;
               &>.eye {
                  position: absolute;
                  z-index: 2;
                  right: 1.2rem;
                  top: 0;
                  bottom: 0;
                  margin: auto 0 auto 0;
               }
            }
            margin-top: 1.5rem;
         }
      }
   }
</style>
<script>
   export default {
      data() {
         return {
            renderPage: false
         }
      },
      methods: {
         confirmPassword() {
            this.$axios.post("http://localhost:8001/admin/api/confirm-password.php", new FormData(this.$refs.FormDataConfirmPassword))
            .then(res => { if ( typeof res.data == "object" ) return res.data; try { return JSON.parse(res.data) } catch(e) { return { error: 1, mess: res.data } } })
            .then(json => {
               if ( json.error == 1 ) {
                  throw new Error(json.mess)
               }
               this.renderPage = true
            })
            .catch(({ stack, message }) => {
               this.$AppError(message, stack)
            })
         },
         saveEmail() {
            
         },
         savePassword() {
            this.$axios.post("http://localhost:8001/admin/api/change-password.php", new FormData(this.$refs.FormDataChangePassword))
            .then(res => { if ( typeof res.data == "object" ) return res.data; try { return JSON.parse(res.data) } catch(e) { return { error: 1, mess: res.data } } })
            .then(json => {
               if ( json.error == 1 ) {
                  throw new Error(json.mess)
               }
               
               this.$AppSuccess("Success", "Change password done.")
            })
            .catch(({ message, stack }) => {
               this.$AppError(message, stack)
            })
         }
      }
   }
</script>