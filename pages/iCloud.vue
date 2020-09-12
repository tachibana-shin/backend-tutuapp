<template>
   <div class="main">
      <form class="my-2 was-validated" ref="formData">
         <label> Các tài khoản </label>
         <table class="table-responsive">
            <tr v-for="(item, index) in icloud" :key="index">
               <td> <input class="no-input form-control" v-model="item.username" name="username[]" required placeholder="Username" type="email"> </td>
               <td> <input class="no-input form-control" v-model="item.password" name="password[]" required placeholder="Password"> </td>
               <td class="icon" @click="remove(index)">
                  <i class="fad fa-trash"></i>
               </td>
            </tr>
         </table>
         <div v-if="icloud.length == 0" class="text-center text-secondary py-3">
            Không có tài khoản nào
         </div>
      </form>
      <vue-fab icon="toc" main-btn-color="#999" size="big" fab-item-animate="alive">
         <fab-item icon="save" color="#c7d23b" @clickItem="save" :idx="0" />
         <fab-item icon="add" @clickItem="icloud.push({ username: '', password: '' })" :idx="1" />
      </vue-fab>
   </div>
</template>
<style lang="scss" scoped>
   .main {
      form {
         padding: {
            left: 15px;
            right: 15px;
         }


         table {
            margin: 0 auto;

            tr {
               border-top: 1px solid #ccc;
               background-color: #fff;

               th {
                  text-align: inherit;
                  border: 1px solid #ddd;
                  padding: 6px 13px;
               }

               td {
                  border: 1px solid #ddd;

                  &.icon {
                     padding: {
                        left: .5rem;
                        right: .5rem;
                     }
                  }
               }
            }
         }

         .no-input {
            -webkit-appearance: none;
            display: inline-block;
            width: 100%;
            border: 0;
         }
      }
   }
</style>
<script>
   import axios from "axios"
   export default {
      data() {
         return {
            icloud: []
         }
      },
      methods: {
         save() {
            let loading = this.$loading.show({
               width: 50,
               height: 50,
               container: null
            })

            let formData = new FormData(this.$refs.formData)

            this.$axios.post("/admin/api/iCloud.php", formData)
               .then(res => res.data)
               .then(json => {
                  console.log(json)

                  if (json.state.error) {
                     throw new Error(json.state.message)
                  } else {
                     this.$success("Success", "Update success.")
                  }
               })
               .catch(e => {

                  this.$error(e.message, e.stack)
               })
               .finally(() => loading.hide())
         },
         remove(index) {

            this.icloud.splice(index, 1)
         }
      },
      created() {
         let loading = this.$loading.show({
            width: 50,
            height: 50,
            container: null
         })
         this.$axios.get("/admin/api/iCloud.php")
            .then(res => res.data)
            .then(json => {
               if (json.state.error) {
                  throw new Error(json.state.message)
               } else {
                  this.icloud = json.data
               }
            })
            .catch(e => {
               this.$error(e.message, e.stack)
            })
            .finally(() => loading.hide())
      }
   }
</script>