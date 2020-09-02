<template>
   <div class="main">
      <div class="content">
         <div class="item">
            <h4 class="general"> Đổi mật khẩu </h4>
            
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

   }
 </style>
 
 <script>
    export default {
       
    }
 </script>