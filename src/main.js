import Vue from 'vue'
import router from './plugins/router'
import { api, store } from './plugins/store'
import './plugins/vuetify'
import App from './App'

Vue.config.productionTip = false
Vue.prototype.api = api

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
