<template>
   <div class="main">
      <div class="title">
         <b-dropdown :text="category"
            variant="outline-dark"
            toggle-tag="span"
            size="sm">
            <b-dropdown-item
               v-for="(item, index) in $options.optsCategory"
               :key="index"
               @click="category = item"
               :class="{
                  active: item == category
               }">
               {{ item }}
            </b-dropdown-item>
         </b-dropdown>
      </div>
      <div class="content">
         <ul class="list">
            <li class="item"
               v-for="(item, index) in banners">
               <div class="banner">
                  <b-form-input type="url" class="input" v-model="item.url" placeholder="Super Link" size="sm"/>
                  
                  <image-input class="image" v-model="item.path" image-class="image-class" @empty="banners.splice(index, 1)"/>
               </div>
            </li>
            
            <li class="item add">
               <div class="banner">
                  <div class="image flex-center" @click="addImage">
                     <h4> + </h4>
                  </div>
               </div>
            </li>
         </ul>
      </div>
      
      <vue-fab size="big" icon="save" @click.native="saveData" />
   </div>
</template>

<style lang="scss" scoped>
   .main {
      padding: {
         left: 15px;
         right: 15px;
         box-sizing: border-box;
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
            margin: 0;
            padding: 0;
            .item {
               margin-top: .8rem;
               flex: 0 0 100%;
               .input {
                  
               }
               .image {
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
      watch: {
         category: {
            handler: "fetchData",
            immediate: true
         }
      },
      methods: {
         fetchData() {
            let loading = this.$loading.show({
               width: 50,
               height: 50,
               container: null
            })
            this.$axios.get("/admin/api/Banners.php", {
               params: {
                  category: this.category
               }
            })
            .then(res => res.data)
            .then(json => {
               if ( json.state.error ) {
                  throw new Error(json.state.message)
               } else {
                  this.banners = json.data
               }
            })
            .catch(({ stack, message }) => {
               this.$error(message, stack)
            })
            .finally(() => loading.hide())
         },
         saveData() {
            let formData = new FormData
            
            formData.append("category", this.category)
            
            this.banners.forEach(({ path, url }) => {
               if ( path.constructor == File ) {
                  formData.append("banners[]", path)
                  formData.append("banners-map[]", 1)
               } else {
                  formData.append("banners-path[]", path)
                  formData.append("banners-map[]", 0)
               }
               
               formData.append("banners-url[]", url)
            })
            
            let loading = this.$loading.show({
               width: 50,
               height: 50,
               container: null
            })
            
            this.$axios.post("/admin/api/Banners.php", formData)
            .then(res => res.data)
            .then(json => {
               if ( json.state.error ) {
                  throw new Error( json.state.message )
               } else {
                  this.$success("Success", "Save Success.")
               }
            })
            .catch(({ stack, message }) => this.$error(message, stack))
            .finally(() => loading.hide())
         },
         addImage() {
            let input = document.createElement("input")
            input.type = "file"
            input.accept = "image/*"
            input.multiple = true
            input.addEventListener("change", () => {
               this.banners.push(...[...input.files].map(e => {
                  return {
                     url: "",
                     path: e
                  }
               }))
               input.remove()
            })
            input.addEventListener("blur", () => input.remove())
               
            input.hidden = true
            input.click()
            document.body.appendChild(input)
         }
      }
   }
</script>