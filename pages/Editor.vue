<template>
   <div class="main">
      <form ref="FormData" :class="{
            'was-validated': WasValidate
         }">
         <div class="basic item">
            <div class="left">
               <image-input name="icon" v-model="app.icon" accept=".jpg, .jpeg, .gif" class="avatar" :required="$route.meta.upload" image-class="insperator" input-class="form-control">
                  <template v-slot:empty>
                     <div class="flex-center insperator">
                        <h4> + </h4>
                     </div>
                  </template>
               </image-input>
            </div>
            <div class="right">
               <input class="no-input app-name form-control" placeholder="App name" v-model="app.name" name="name" required>
               <input class="no-input app-developer form-control" placeholder="Developer" v-model="app.developer" name="developer" required>
            </div>
         </div>
         <div class="infomation item">
            <ul class="form-group infomation-basic">
               <li class="item">
                  <label> Size </label>
                  <input class="form-control" placeholder="Không bắt buộc" v-model="app.size" name="size">
               </li>
               <li class="item">
                  <label> Compatibility </label>
                  <textarea class="form-control" placeholder="Không bắt buộc" v-model="app.compatibility" name="compatibility"></textarea>
               </li>
               <li class="item">
                  <label> Languages </label>
                  <vue-select v-model="app.languages"
                     :filterable="false"
                     @search="searchLanguage"
                     :options="optLanguages"
                     :class="{
                        'is-valid': WasValidate
                     }" multiple taggable
                     :get-option-label="e => e.name ? (e.name + ' (' + e.nativeName + ')') : e.label || e"
                     :get-option-key="() => Math.random()"/>
               </li>
               <li class="item">
                  <label> Category </label>
                  <vue-select class="w-100" v-model="app.category" required taggable :options="$options.optsCategory" placeholder="Một tùy chọn phải được có"
                    :class="{
                        'is-valid': !!app.category && WasValidate,
                        'is-invalid': !app.category && WasValidate
                     }"/>
               </li>
            </ul>
         </div>
         <div class="screenshot item">
            <label> Screenshot </label>
            <div class="form-group" :class="{
                  'is-valid': app.screenshot.length > 0 && WasValidate,
                  'is-invalid': app.screenshot.length == 0 && WasValidate
               }">
               <ul class="scroll-x">
                  <li class="item" v-for="(src, index) in app.screenshot" :key="index">
                     <image-input v-model="app.screenshot[index]" class="child" accept="image/*" @empty="app.screenshot.splice(index, 1)"/>
                  </li>
                  <li class="item" @click="inputScreenshot">
                     <h4 class="child flex-center">
                        <span> + </span>
                     </h4>
                  </li>
               </ul>
            </div>
         </div>
         <div class="active item">
            <div class="form-group">
               <label> Tải khoản kích hoạt </label>
               <input class="form-control" placeholder="example@icloud.com" v-model="app.account" name="account" type="email" required>
               <small class="text-secondary"> Trường này bắt buộc </small>
            </div>
         </div>
         <div class="advanced item">
            <div class="form-group">
               <label class="d-flex justify-content-between">
                  <span> Version </span>
                  <span @click="app.version.push({ name: '', value: '' })"> + </span>
               </label>
               <div class="content">
                  <ul class="version-list" :class="{
                        'is-invalid': !app.version || !app.version.length,
                        'is-valid': app.version && app.version.length
                     }">
                     <li>
                        <div class="input-group my-2" v-for="(item, index) in app.version">
                           <input class="form-control" placeholder="Verions" name="name-vers[]" required v-model="item.name">
                           <input class="form-control" placeholder="URL" name="url-vers[]" required v-model="item.value" type="url">
                           <div class="input-group-append">
                              <span class="input-group-text bg-0" @click="app.version.splice(index, 1)"> &times; </span>
                           </div>
                        </div>
                     </li>
                  </ul>
                  <div v-show="!app.version || !app.version.length">
                     <div class="text-center py-3 text-secondary border border-top-0" @click="app.version.push({ name: '', value: '' })">
                        Tap to +
                     </div>
                  </div>
               </div>
            </div>
            <div class="form-group">
               <label> Description </label>
               <textarea class="form-control custom-input" v-model="app.description" name="description" required rows="5" placeholder="Description for application"></textarea>
            </div>
         </div>
      </form>
      <vue-fab size="big" fab-item-animate="alive" :scroll-auto-hide="false" v-if="!$route.meta.upload" icon="toc" main-btn-color="#999">
         <fab-item icon="save" title="Save changes" :idx="0" color="#C7D23B" @clickItem="Save" />
         <fab-item icon="restore" title="fetchData" :idx="1" color="#ff9900" @clickItem="fetchData" />
         <fab-item icon="delete" title="Delete" :idx="2" color="#dc3545" @clickItem="Delete" />
      </vue-fab>
      <vue-fab size="big" icon="save" v-else @click.native="Save" />
   </div>
</template>
<script>
   import axios from "axios"
   import EasyMDE from "easymde"
   import Swal from "sweetalert2"
   import VueSelect from "vue-select"
   import ImageInput from "./../components/ImageInput.vue"
   
   function extend(...args) {
      let object = {}
      args.forEach(item => {
         for ( let key in item ) {
            object[ key ] = item[ key ]
         }
      })
      return object
   }

   export default {
      optsCategory: ["Games", "Apps", "Jailbreaks", "Support", "Addons"],
      components: { VueSelect, ImageInput },
      data() {
         return {
            app: {
               name: "",
               icon: "",
               size: "",
               description: "",
               screenshot: [],
               developer: "",
               category: "",
               compatibility: "",
               languages: [],
               version: [],
               account: "",

               id: null
            },
            
            /* @cache */
            appScreenshotSource: [],
            /* @/cache */

            optLanguages: [],
            WasValidate: false
         }
      },
      methods: {
         Delete() {
            let { id, name } = this.app
            Swal.fire({
               title: "Are you sure?",
               html: `You have delete "${name}"? <br>`,
               icon: "warning",
               showCancelButton: true,
               showConfirmButton: true,
               confirmButtonText: "Delete",
               confirmButtonColor: "#dc3545",
               showLoaderOnConfirm: true,
               preConfirm: () => {
                  let formData = new FormData
                  formData.append("id", id)
                  formData.append("action", "delete")
                  return axios
                     .post("http://localhost:8001/admin/api/App.php", formData)
                     .then(res => res.data)
                     .then(response => {

                        if (response.state.error)
                           throw new Error(response.state.message)
                        return response
                     })
                     .catch(error => {
                        Swal.showValidationMessage(
                           `${error}`
                        )
                     })
               },

               allowOutsideClick: () => !Swal.isLoading()
            }).then(({ value }) => {
               let { error, message } = value.state
               if (!error) {
                  this.$success("Delete success", "You delete app.")
                  setTimeout(() => this.$router.go(-1))
               } else {
                  this.$error("Delete failed", message)
               }
            })

         },
         CheckValidate() {
            if (this.$refs.FormData.checkValidity() && this.app.version.length > 0 && this.app.screenshot .length > 0) {
               return true
            } else {
               return false
            }
         },
         Save() {
            if (!this.CheckValidate()) {
               this.WasValidate = true
               return
            }

            let formData = new FormData(this.$refs.FormData)

            formData.append("id", this.app.id)
            formData.append("action", this.$route.meta.upload ? "post" : "put")
            formData.append("category", this.app.category)
            
            if ( Array.isArray(this.app.languages) ) {
               this.app.languages.forEach(item => formData.append("languages[]", item.nativeName || item.label || item))
            }
            
            if ( Array.isArray(this.app.screenshot) ) {
               this.app.screenshot.forEach(item => {
                  if ( item.constructor == File ) {
                     formData.append("screenshot[]", item)
                     formData.append("screenshot-map[]", 1)
                  } else {
                     formData.append("screenshot-path[]", item)
                     formData.append("screenshot-map[]", 0)
                  }
               })
            }

            let loading = this.$loading.show({
               width: 50,
               height: 50,
               container: null
            })
            
            this.$axios.post("http://localhost:8001/admin/api/App.php", formData)
               .then(res => res.data)
               .then(json => {
                  console.log( json )

                  if (json.state.error) {
                     throw new Error(json.state.message)
                  } else {
                     this.$success("Successfully", "Upload to server success.")
                     if ( this.$route.meta.upload ) {
                        // if this = page upload
                        this.$router.push(`/editor/${json.data.id}`)
                     }
                  }
               })
               .catch(e => {
                  this.$error(e.message, e.stack)
               })
               .finally(() => loading.hide())
         },
         fetchData() {
            let loading = this.$loading.show({
               width: 50,
               height: 50,
               container: null
            })
            
            this.$axios.get("http://localhost:8001/admin/api/App.php", {
                  params: {
                     id: this.$route.params.id
                  },
                  headers: {
                     "Content-Type": "application/json",
                     "Accept": "application/json"
                  }
               })
               .then(res => res.data)
               .then(json => {
                  
                  if (json.state.error) {
                     throw new Error(json.state.mess)
                  } else {
                     this.app = extend(this.app, json.data)
                  }
               })
               .catch(e => {
                  this.$error(e.message, e.stack)
               })
               .finally(() => loading.hide())
         },
         inputScreenshot() {
            //this.app.screenshot.push(null)

            if (document.querySelector("input[data-type=addons]")) {
               return 0x0;
            }
            let input = document.createElement("input")
            input.type = "file"
            input.dataset.type = "addons"
            input.hidden = true
            input.accept = ".jpg, .jpeg, .gif"
            input.multiple = true
            document.body.appendChild(input)
            input.click()
            input.addEventListener("blur", () => input.remove())
            input.addEventListener("change", () => {
               this.app.screenshot.push(...input.files)
               input.remove()
            })
         },
         searchLanguage(query, loading) {
            loading(true)
            axios.get("http://localhost:8001/admin/api/Languages-search.php", {
               params: {
                  query
               }
            })
            .then(res => res.data)
            .then(json => {
               this.optLanguages = json.data
               loading(false)
            })
         }
      },
      created() {
         if (!this.$route.meta.upload) {
            this.fetchData()
         }
      }
   }
</script>
<style lang="scss" scoped>
   @import "https://unpkg.com/easymde/dist/easymde.min.css";
   @import "https://unpkg.com/vue-select@3.0.0/dist/vue-select.css";

   .main {
      .no-input {
         appearance: none;
         -webkit-appearance: none;
         border: none;
         outline: none;
         background-color: #00000000;
      }

      .flex-center {
         display: flex !important;
         align-items: center;
         justify-content: center;
      }

      form {
         &>.item {
            padding: 1.5rem (10rem / 16);
            background-color: #fff;
            margin: 15px;
            box-shadow: 0 2px 2px rgba(0, 0, 0, .2);
            border: 1px solid #ced4da;
            border-radius: .25rem;

            .form-group {
               margin-top: 1rem;

               &:first-child {
                  margin-top: 0;
               }
            }
         }

         .basic {
            display: flex;
            margin: 0;

            .left {
               margin-right: 10px;
               position: relative;
               width: 96px;
               height: 96px;

               .avatar {
                  width: 100%;
                  height: 100%;
                  border-radius: 10px;
                  border: 1px solid rgba(0, 0, 0, .1);
                  background-color: rgba(200, 200, 200, .5);
                  position: absolute;
               }
            }

            .right {
               display: flex;

               flex: {
                  direction: column;
                  grow: 1;
                  basis: 0;
               }


               justify-content: space-around;

               .app-name {
                  font-size: (36rem / 16) !important;
                  padding: 0 !important;
                  margin: 0 !important;
                  padding-bottom: .5rem !important;
                  font-weight: 500 !important;

                  @media (max-width: 768px) {
                     font-size: (26rem / 16) !important;
                  }

                  width: 100% !important;
                  min-width: 0 !important;
               }

               .app-developer {
                  font-size: (24rem / 16) !important;
                  color: rgba(0, 0, 0, .8) !important;
                  padding: 0 !important;
                  margin: 0 !important;

                  @media (max-width: 768px) {
                     font-size: (18rem / 16) !important;
                  }

                  width: 100% !important;
                  min-width: 0 !important;
               }

            }
         }

         .infomation {
            .form-group.infomation-basic {
               flex-direction: column;
               font-size: 14px;
               font-weight: 500;
               position: relative;
               width: 100%;
               box-sizing: border-box;
               margin: 0;
               padding: 0;
               list-style: none;

               .item {
                  display: flex;
                  justify-content: space-between;
                  flex-wrap: nowrap;
                  align-items: center;

                  padding: {
                     top: .5rem;
                     bottom: .5rem;
                  }

                  ;

                  &>* {
                     flex: 0 0 50%;
                     min-width: 0;
                  }
               }
            }
         }

         .screenshot {
            .form-group {
               padding: 0;

               .scroll-x {
                  width: 100%;
                  height: 237px;
                  position: relative;
                  overflow: scroll hidden;
                  word-wrap: nowrap;
                  white-space: nowrap;
                  margin: 0;
                  padding: 0;

                  li {
                     height: 237px;
                     display: inline-block;
                     margin-right: 15.36px;
                     width: 134px;
                     position: relative;

                     .child {
                        border-radius: 7.67px;
                        border: 1px solid rgba(0, 0, 0, .1);
                        position: absolute;
                        width: 100%;
                        height: 100%;
                        overflow: hidden;
                     }
                  }
               }
            }
         }

         .advanced {
            .form-group {
               .content {
                  .version-list {
                     list-style: none;
                     margin: 0;
                     padding: 0;
                  }
               }
            }
         }

      }

   }
</style>