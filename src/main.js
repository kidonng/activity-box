import Vue from 'vue'
import router from './plugins/router'
import store from './plugins/store'
import './plugins/vuetify'
import 'whatwg-fetch'
import ky from 'ky'
import './plugins/jssha'
import App from './App'

Vue.prototype.$ajax = ky
// https://cn.vuejs.org/v2/api/index.html#productionTip
Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
