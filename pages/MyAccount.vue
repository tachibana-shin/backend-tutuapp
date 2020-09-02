<template>
   <div class="main">
      <div class="requestPassword" v-if="!renderPage">
         <form ref="FormDataConfirmPassword" class="form-group">
            <label> Enter Password </label>
            <div class="group-password">
               <input type="password" name="password-confirm" class="form-control" placeholder="Password">
            </div>
            <b-button variant="primary" block @click="confirmPassword" class="mt-3"> Continue </b-button>
            <small class="text-secondary"> Để tiếp tục bạn cần nhập mật khẩu. </small>
         </form>
      </div>
      <div class="privacy" v-if="renderPage">
         <h2 class="title"> Thông tin cá nhân </h2>
         <div class="content">
            
            <div class="item">
               
               <h4 class="general"> Chung </h4>
               
               <ul class="content">
                  <li is="router-link" tag="li" to="/myaccount/change/name" class="item">
                     <h5 class="title-item"> Tên </h5>
                     <p class="content"> Nguyễn Thành </p>
                  </li>
                  <li is="router-link" tag="li" to="/myaccount/change/email" class="item">
                     <h5 class="title-item"> Email </h5>
                     <p class="content"> thanhnguyennguyen1995@gmail.com </p>
                  </li>
               </ul>
            </div>
            <div class="item">
               
               <h4 class="general"> Bảo mật </h4>
               
               <ul class="content">
                  <li is="router-link" tag="li" to="/myaccount/change/password" class="item">
                     <h5 class="title-item"> Đổi mật khẩu </h5>
                  </li>
               </ul>
            </div>
            
         </div>
      </div>
   </div>
</template>
<style lang="scss" scoped>
   .main {
      @for $i from 1 to 7 {
         h#{$i} {
            margin: 0;
            padding: 0;
            font-weight: 500;
         }
      }
      padding: {
         left: 15px;
         right: 15px;
      }


      .content {
         .form-group {
            margin-top: 1.5rem;
         }
      }
      .privacy {
         .title {
            
         }
         .content {
            .item {
                     
               .general {
                  margin-top: 1.5rem;
               }
               .content {
                  list-style: none;
                  margin: 0;
                  padding: 0;
                  margin-top: .3rem;
                  .item {
                     position: relative;
                     padding: .5rem;
                     border-bottom: 1px solid rgba(0, 0, 0, .1);
                     display: flex;
                     justify-content: space-between;
                     flex-direction: column;
                     padding-right: 1rem;
                     .title-item {
                        font-size: 1rem !important;
                     }
                     .content {
                        color: var(--secondary);
                        font-size: 14px;
                     }
                     &:before {
                        content: "";
                        position: absolute;
                        top: 50%;
                        right: .3rem;
                        right: .5rem;
                        border: solid black;
                        border-width: 0 3px 3px 0;
                        display: inline-block;
                        padding: 3px;
                        transform: rotate(-45deg) translateY(-50%);
                     }
                  }
               }
            }
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
            .then(res => res.data)
            .then(json => {
               if ( json.state.error ) {
                  throw new Error(json.state.message)
               } else {
                  this.renderPage = json.data
                  if ( !json.data ) {
                     this.$error("Confirm failed", "Wrong cofirm password")
                  }
               }
            })
            .catch(({ stack, message }) => {
               this.$error(message, stack)
            })
         }
      }
   }
</script>