<script>
   export default {
      props: {
         show: Boolean,
         text: String,
         toggleClass: [ String, Array, Object ]
      },
      data() {
         return {
            showState: this.show || false
         }
      },
      watch: {
         showState(newVal) {
            this.$emit("input", newVal)
         },
         show(newVal) {
            this.showState = newVal
         }
      },
      model: {
         prop: "show"
      },
      render(h) {
         return h("div", {
            staticClass: "dropdown dropdown-static",
            class: {
               show: this.showState
            }
         }, [
            h("span", {
               staticClass: "dropdown-toggle",
               class: this.toggleClass,
               on: {
                  click: () => {
                     this.showState = !this.showState
                  }
               },
               ref: "toggle"
            }, this.$slots["button-content"] || this.text),
            h("div", {
               staticClass: "dropdown-menu",
               class: {
                  show: this.showState
               },
               style: {
                  right: 0,
                  left: "auto"
               }
            }, this.$slots.default)
         ])
      },
      methods: {
         hide(e) {
            if ( this.$refs.toggle && [e.target, e.srcElement, e.targetElement].indexOf(this.$refs.toggle) == -1 )
               this.showState = false
         }
      },
      created() {
         document.addEventListener("click", this.hide)
      },
      destroyed() {
         document.removeEventListener("click", this.hide)
      }
   }
</script>