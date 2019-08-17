import Vue from 'vue'
import router from './plugins/router'
import store from './plugins/store'
import './plugins/vuetify'
import axios from 'axios'

Vue.config.productionTip = false
Vue.prototype.$ajax = axios

import App from './App'
new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
