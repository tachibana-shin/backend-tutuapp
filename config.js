SystemJS.config({
  baseURL:'https://unpkg.com/',
  defaultExtension: true,
  meta: {
      "*.scss": { "loader": "sass" },
      "*.sass": { "loader": "sass" },
    
    '*.vue': {
       'loader': 'vue-loader'
    },
  },
  map: {
        'plugin-babel': 'systemjs-plugin-babel@latest/plugin-babel.js',
    'systemjs-babel-build': 'systemjs-plugin-babel@latest/systemjs-babel-browser.js',
        
        'vue-loader': 'systemjs-vue-loader@latest',
        'vue-template-compiler': 'vue-template-compiler@latest',
        'vue-template-es2015-compiler': 'vue-template-es2015-compiler@latest',
        'sass.js': 'sass.js@latest',
        'less': 'less@latest',
        'acorn': 'acorn@latest',
        'bootstrap-vue': 'bootstrap-vue',
        'vue-infinite-loading': 'vue-infinite-loading',
        'vue-router': 'vue-router',
        'axios': 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js',
        'vue-unsaved-changes-dialog': 'vue-unsaved-changes-dialog',
        'sweetalert2': 'sweetalert2',
        'vue-sweetalert2': 'vue-sweetalert2',
        'vue-float-action-button': 'vue-float-action-button',
        'vue-loading-overlay': 'vue-loading-overlay',
        'animejs': 'animejs',
        'vue-progressbar': 'vue-progressbar'
  },
  transpiler: 'plugin-babel',
  separateCSS: false,
  buildCSS: true,
  sassPluginOptions: {
     "autoprefixer": true
  },
  packages: {
        vue: {
            main: 'dist/vue.js'
        },
        'vue-template-es2015-compiler': {
            main: 'index.js'
        },
        easymde: {
           main: 'dist/easymde.min.js'
        }
    }
});

SystemJS.import('./main.js')
  .catch(console.error.bind(console));