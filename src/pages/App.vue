<template>
   <div class="main">
      <div class="app-manager__header">
         <div class="search">
            <span class="icon">
               <i class="fas fa-search"></i>
            </span>
            <input class="input" placeholder="Search..." v-model="inputQuery" @keyup.enter="reloadInfinite" @keyup="$emit('input', $event.target.value)">
            <span class="close" v-show="!!inputQuery" @click="inputQuery = ''">
               <i class="fas fa-times"></i>
            </span>
         </div>
         <S-SDropdown :text="$options.optsCategory[category].text" class="category">
            <div class="dropdown-item" v-for="(item, index) in $options.optsCategory" :key="index" @click="category = index" :class="{ active: category == index }">
               {{ item.text }}
            </div>
         </S-SDropdown>
         <S-SDropdown class="sortby">
            <template v-slot:button-content>
               <i :class="$options.optsSortBy[ sortBy ].icon"></i>
            </template>
            <div class="dropdown-item" v-for="(item, index) in $options.optsSortBy" :key="index" @click="sortBy = index" :class="{ active: sortBy == index }">
               <i v-if="!!item.icon" :class="item.icon" class="mr-2"></i> {{ item.text || item }}
            </div>
         </S-SDropdown>
      </div>
      <div class="app-manager__body">
         <ul class="list">
            <li class="item" v-for="(item, index) in apps" :key="item.id">
               <router-link tag="div" :to="'/editor/' + item.id" class="app-manager__app">
                  <img class="icon" :src="item.icon">
                  <div class="content">
                     <div class="basic">
                        <div class="left">
                           <h1 class="title">
                              {{ item.name }}
                           </h1>
                           <h5 class="developer">
                              {{ item.developer }}
                           </h5>
                        </div>
                        <div class="menu_sprt">
                           <S-SDropdown toggle-class="no-caret" @click.native.stop>
                              <template v-slot:button-content>
                                 <i class="far fa-ellipsis-v"></i>
                              </template>
                              <router-link tag="div" class="dropdown-item" :to="'/editor/' + item.id">
                                 Edit App
                              </router-link>
                              <div class="dropdown-divider"></div>
                              <div class="dropdown-item text-danger" @click="remove(item)"> Delete </div>
                           </S-SDropdown>
                        </div>
                     </div>
                     <div class="infomation">
                        <b-badge pill variant="primary" class="bg-primary"> {{ item.category }} </b-badge>
                        <span>
                           <i class="fas fa-arrow-alt-down"></i>
                           <span> {{ item.download }} </span>
                        </span>
                        <span>
                           <i class="far fa-calendar-alt"></i>
                           <span> {{ item.updated }} </span>
                        </span>
                        <span>
                           <i class="fas fa-eye"></i>
                           <span> {{ item.view }} </span>
                        </span>
                     </div>
                  </div>
               </router-link>
            </li>
         </ul>
         <vue-infinite @infinite="infiniteHandler" ref="VueInfinite" />
      </div>
      <vue-fab size="big" fab-item-animate="alive" main-btn-color="#3eaf7c" @click.native="$router.push('/upload')" />
   </div>
</template>
<style lang="scss" scoped>
   .main {
      background-color: #fafafa;
      font-size: 15px;

      .app-manager__header {
         display: flex;
         align-items: center;
         justify-content: space-between;
         background-color: #f2f2f2;
         color: #a6a6a6;
         padding: 0 .5rem;

         .search {
            display: flex;
            align-items: center;
            flex-basis: 0;
            flex-grow: 1;

            .icon {
               text-align: center;
               margin: 0.5rem .75rem;
            }

            .input {
               appearance: none;
               border: 0;
               outline: none;
               background-color: #00000000;
               flex-basis: 0;
               flex-grow: 1;
               min-width: 0;
               width: auto;

               &::-webkit-input-placeholder {
                  color: #b6b6b6;
               }
            }

            .close {
               float: none;
               margin-right: 0.5rem;
               font-size: .8rem;
            }
         }

         .category {
            /*margin-left: auto;
            align-self: flex-end;*/
            margin-right: .5rem;
         }

         .sortby {}
      }

      .app-manager__body {
         .list {
            list-style: none;
            margin: 0;
            padding: .5rem 0;

            .item {
               border-bottom: 1px solid rgba(0, 0, 0, .1);

               .app-manager__app {
                  display: flex;
                  padding: .3rem 15px;

                  .icon {
                     width: 2.5rem;
                     height: 2.5rem;
                     border-radius: 10px;
                     margin-right: .5rem;
                  }

                  .content {
                     flex-basis: 0;
                     flex-grow: 1;

                     .basic {
                        height: 2.5rem;
                        display: flex;
                        justify-content: space-between;

                        .left {
                           flex-basis: 0;
                           flex-grow: 1;
                           display: flex;
                           flex-direction: column;
                           justify-content: space-around;

                           .title {
                              font: {
                                 weight: normal !important;
                                 size: 14px !important;
                              }

                              line-height: initial !important;
                              margin: 0 !important;
                              padding: 0 !important;
                              color: #111;
                           }

                           .developer {
                              font: {
                                 weight: normal !important;
                                 size: (14px * .8) !important;
                              }

                              line-height: initial !important;
                              margin: 0 !important;
                              padding: 0 !important;
                              color: #0078bd;

                           }
                        }

                        .menu_sprt {
                           text-align: center;
                        }
                     }

                     .infomation {
                        font-size: 12px;
                        display: flex;
                        align-items: center;
                        color: #6c757d;

                        &>* {
                           margin-right: .5rem;

                        }

                        i {
                           color: #7c858d;
                        }
                     }
                  }
               }
            }
         }
      }
   }
</style>
<script>
   import SSDropdown from "../components/SSDropdown.vue"
   import VueInfinite from "vue-infinite-loading"
   import axios from "axios"
   import Swal from "sweetalert2"

   export default {
      components: { SSDropdown, VueInfinite },
      optsCategory: [{
         text: "All",
         value: null,
         }, {
         text: "Apps",
         value: "Apps"
         }, {
         text: "Game",
         value: "Games"
         }, {
         text: "Jailbreaks",
         value: "jailbreaks"
         }, {
         text: "Support",
         value: "Support"
         }, {
         text: "Addons",
         value: "Addons"
      }],
      optsSortBy: [{
         icon: "fas fa-empty-set",
         text: "None",
         value: null
         }, {
         icon: "fas fa-sort-alpha-down",
         text: "Name A - Z",
         value: "a-z"
         }, {
         icon: "fas fa-sort-alpha-down-alt",
         text: "Name Z - A",
         value: "z-a"
         }, {
         icon: "fas fa-arrow-down",
         text: "Time new",
         value: "down"
         }, {
         icon: "fas fa-arrow-up",
         text: "Time last",
         value: "up"
      }],
      data: () => ({
         apps: [],

         category: 0,
         sortBy: 2,

         inputQuery: "",
         page: 0
      }),
      watch: {
         category: "reloadInfinite",
         sortBy: "reloadInfinite"
      },
      methods: {
         reloadInfinite() {
            this.page = 0
            this.apps = []
            this.$refs.VueInfinite.stateChanger.reset()
         },
         infiniteHandler($state) {
           this.$axios.get("/admin/api/All-app.php", {
                  params: {
                     "page": this.page++,
                     "category": this.$options.optsCategory[this.category].value,
                     "query": this.inputQuery,
                     "sort-by": this.$options.optsSortBy[this.sortBy].value
                  }
               })
               .then(res => res.data)
               .then(json => {
                  if ( json.state.error ) {
                     throw new Error(json.state.message)
                  } else {
                     if ( json.data.length == 0 ) {
                        $state.complete()
                     } else {
                        this.apps.push(...json.data)
                        $state.loaded()
                     }
                  }
                  
               })
               .catch(({ stack, message }) => {
                  this.$error(stack, message)
                  $state.complete()
               })
         },
         remove(app) {
            let { id, name } = app
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
                     .post("/admin/api/App.php", formData)
                     .then(res => res.data)
                     .then(response => {
                        if (response.state.error )
                           throw new Error(response.state.mess)
                        this.apps = this.apps.filter(e => e != app)
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
               } else {
                  this.$error("Delete failed", message)
               }
            })

         }
      }
   }
</script>