import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    login: false,
    user: {},
    edit: false,
    title: null,
    menu: false,
    message: { show: false },
    api: {
      activity: 'https://easy-mock.com/mock/5c31d16c67fe190a45936aa3/activity',
      user: 'https://easy-mock.com/mock/5c31d16c67fe190a45936aa3/user',
      image: 'https://sm.ms/api/upload'
    }
  },
  mutations: {
    login (state, v) { state.login = v },
    user ({ user }, v) { Object.assign(user, v) },
    edit (state, v) { state.edit = v },
    title (state, v) { state.title = document.title = v },
    menu (state, show) { state.menu = show },
    message ({ message }, { show = true, type, text }) {
      message.show = show
      message.type = type
      message.text = text
    }
  },
  actions: {
    auth ({ state, commit }, { username, password, callback }) {
      (async () => {
        const res = await this._vm.$ajax.post(state.api.user, {
          json: {
            username,
            password: this._vm.$hash(password)
          }
        }).json()

        res.success
          ? callback(res.token)
          : commit('message', { type: 'error', text: res.message })
      })()
    },
    info ({ state, commit }, token) {
      (async () => {
        commit('login', true)
        localStorage.token = token

        const res = await this._vm.$ajax.get(state.api.user, { headers: { Authorization: token }}).json()

        res.success
          ? commit('user', res)
          : commit('message', { type: 'error', text: res.message })
      })()
    },
    logout ({ commit }) {
      delete localStorage.token
      commit('login', false)
    },
    upload ({ state, commit }, { image, callback }) {
      (async () => {
        const data = new FormData()
        data.append('smfile', image, image.name)

        const res = await this._vm.$ajax.post(state.api.image, { body: data }).json()

        if (res.code === 'success') {
          callback(res.data.url)
          commit('message', { type: 'done', text: '上传成功' })
        } else commit('message', { type: 'error', text: res.msg })
      })()
    }
  }
})
