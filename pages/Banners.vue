<template>
   <div class="main">
      <div class="title">
         <b-dropdown :text="category">
            <b-dropdown-item
               v-for="(item, index) in $options.optsCategory"
               :key="index"
               @click="category = item">
               {{ item }}
            </b-dropdown-item>
         </b-dropdown>
      </div>
      <div class="content">
         <ul class="list">
            <li class="item"
               v-for="(item, index) in banners">
               <div class="banner">
                  <b-form-input type="url" class="input" v-model="item.url" placeholder="Super Link"/>
                  
                  <image-input class="image" name="banners[]" v-model="item.banner" image-class="image-class"/>
               </div>
            </li>
            
            <li class="item add">
               <div class="banner">
                  <div class="image flex-center">
                     <h4> + </h4>
                  </div>
               </div>
            </li>
         </ul>
      </div>
   </div>
</template>

<style lang="scss" scoped>
   .main {
      padding: {
         left: 15px;
         right: 15px;
      };
      
      .flex-center {
         display: flex;
         justify-content: center;
         align-items: center;
      }
      
      .title {
         
      }
      .content {
         .list {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            .item {
               flex: 0 0 100%;
               .input {
                  
               }
               .image {
                  margin-top: 5px;
                  width: 330px;
                  height: 164px;
                  max-width: 100%;
                  .image-class {
                     object-fit: cover;
                     object-position: 50% 50%;
                  }
               }
               &.add {
                  .image {
                     background-color: rgba(200, 200, 200, .5);
                     color: #fff;
                  }
               }
            }
            
         }
      }
   }
   
</style>

<script>
   import ImageInput from "./../components/ImageInput.vue"
   
   export default {
      components: { ImageInput },
      optsCategory: [ "Feature", "Games", "Apps" ],
      data() {
         return {
            category: this.$options.optsCategory[0],
            
            banners: []
         }
      },
      methods: {
         fetchData() {
            
         }
      }
   }
</script>