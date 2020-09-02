<template>
   <b-navbar toggleable="lg" type="light" variant="light">
      <b-navbar-toggle class="border-0" :target="id" />
      <b-navbar-brand tag="span">
         Free iOS Admin
      </b-navbar-brand>
      <b-navbar-brand tag="span">
         <S-SDropdown toggle-class="no-caret" v-if="$store.state.currentUser">
            <template v-slot:button-content>
               <i class="fas fa-user" style="font-size: 1.5rem"></i>
            </template>
            <span class="dropdown-item font-bold"> {{ $store.state.currentUser.user }} </span>
            <router-link to="/myaccount" class="dropdown-item mt-1">
               Manager Account
            </router-link>
            <div class="dropdown-diliver"></div>
            <router-link to="/logout" class="dropdown-item text-danger">
               Log out
            </router-link>
         </S-SDropdown>
      </b-navbar-brand>
      <b-collapse :id="id" is-nav>
         <b-navbar-nav>
            <b-nav-item to="/" active-class="" exact-active-class="active">
               <i class="fab fa-app-store"></i>
               App Manager </b-nav-item>
            <b-nav-item to="/upload">
               <i class="far fa-arrow-alt-circle-up"></i>
               Upload App </b-nav-item>
            <b-nav-item to="/icloud">
               <i class="fab fa-cloudversify"></i>
               iCloud Manager </b-nav-item>
            <b-nav-item to="/banners">
               <i class="far fa-pennant"></i>
               Banners Manager </b-nav-item>
         </b-navbar-nav>
      </b-collapse>
   </b-navbar>
</template>
<style lang="scss" scoped>
   .navbar-toggler:hover {
      border: 0 !important;
      outline: none !important;
      box-shadow: none !important;
   }

   .collapse {
      .navbar-nav {
         margin-left: 1rem !important
      }
   }

   .avatar {
      width: 2.5rem !important;
      height: 2.5rem !important;
      border-radius: 50%;

      &::before {
         display: none !important;
      }

      &::after {
         display: none !important;
      }
   }
</style>
<script>
   import SSDropdown from "./SSDropdown.vue"
   export default {
      name: "HeaderApp",
      components: { SSDropdown },
      data() {
         return {
            id: "_" + Math.random().toString(36)
         }
      }
   }
</script>