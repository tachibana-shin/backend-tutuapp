<template>
   <div @click="open" >
      <span class="close" @click="empty" v-show="!!src" ref="Close"> &times; </span>
      <input type="file" @change="$emit('input', $event.target.files[0])" ref="Input" :name="name" hidden :accept="accept"
         :class="inputClass" :required="required">
      <img :src="src" v-bind="$attrs" :class="imageClass" v-show="!!src || !$slots.empty">
      <slot name="empty" v-if="$slots.empty && !src"></slot>
   </div>
</template>
<style lang="scss" scoped>
   div {
      position: relative;
      overflow: hidden;

      input[type="file"] ~ * {
         position: absolute;
         width: 100%;
         height: 100%;
         border-radius: inherit;

         object-fit: cover;
         object-position: center;
         display: block;

      }
      .close {
         all: initial;
         position: absolute;
         font-size: 1.2em;
         color: #fff;
         z-index: 2;
         text-shadow: 0 0 5px rgba(0, 0, 0, .5);
         top: 0;
         right: 0;
         display: block;
         margin: 0;
         padding: 0;
         line-height: 1;
      }
   }
</style>
<script>
   export default {
      inheritAttrs: false,
      props: {
         value: [String, File],
         name: String,
         accept: [String, Array],
         inputClass: [String, Array, Object],
         imageClass: [String, Array, Object],
         required: Boolean
      },
      data() {
         return {
            src: ""
         }
      },
      watch: {
         value: {
            handler(newVal) {
               if (typeof newVal == "string" || newVal == null) {
                  this.src = newVal
               } else {
                  this.src = URL.createObjectURL(newVal)
                  setTimeout(() => URL.revokeObjectURL(this.src), 70)
               }
            },
            immediate: true
         }
      },
      methods: {
         empty() {
            this.$emit("input", null)
            this.$refs.Input.value = null
            this.$emit("empty")
         },
         open({ target, srcElement }) {
            if ( [ target, srcElement ].indexOf(this.$refs.Close) == -1 ) {
               if ( this.$refs.Input )
                  this.$refs.Input.click()
            }
         }
      }
   }
</script>