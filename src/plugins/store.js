import Vue from 'vue'
import Vuex from 'vuex'
import ky from 'ky'

Vue.use(Vuex)

const api = ky.extend({
  prefixUrl: '/rap2/app/mock/163080'
})
const store = new Vuex.Store({
  state: {
    login: false,
    user: {
      avatar: null
    },
    title: null,
    menu: false,
    message: { show: false }
  },
  mutations: {
    login: (state, v) => (state.login = v),
    user: ({ user }, v) => Object.assign(user, v),
    title: (state, v) => (state.title = v),
    menu: (state, show) => (state.menu = show),
    message({ message }, { show = true, type, text }) {
      message.show = show
      message.type = type
      message.text = text
    }
  },
  actions: {
    login({ commit, dispatch }, { username, password, callback }) {
      ;(async () => {
        const res = await api('user', {
          searchParams: {
            username,
            password
          }
        }).json()

        if (res.msg === 'OK') {
          localStorage.username = username
          localStorage.token = res.token
          dispatch('info')
          callback()
        } else commit('message', { type: 'error', text: res.msg })
      })()
    },
    info({ commit }) {
      commit('login', true)
      ;(async () => {
        const res = await api(`user/${localStorage.username}`, {
          headers: { Authorization: localStorage.token }
        }).json()

        res.msg === 'OK'
          ? commit('user', res)
          : commit('message', { type: 'error', text: res.msg })
      })()
    },
    upload({ commit }, { photo, callback }) {
      ;(async () => {
        const data = new FormData()
        data.append('smfile', photo, photo.name)

        const res = await ky
          .post('https://sm.ms/api/upload', { body: data })
          .json()

        if (res.code === 'success') callback(res.data.url)
        else commit('message', { type: 'error', text: res.msg })
      })()
    }
  }
})

export { api, store }
