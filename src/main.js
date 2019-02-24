import Vue from 'vue'
import router from './plugins/router'
import store from './plugins/store'
import './plugins/vuetify'
import './plugins/jssha'
import 'whatwg-fetch'
import ky from 'ky'
import App from './App'

Vue.prototype.$ajax = ky
Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
