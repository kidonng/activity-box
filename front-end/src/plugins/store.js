import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import jsSHA from 'jssha/src/sha1'

Vue.use(Vuex)

// 构造实例，便于重置用户信息
const user = () => {
  return {
    username: null,
    avatar: null,
    bio: null,
    nickname: null,
    realname: null,
    groupname: null,
    verified: false
  }
}
const hash = (password) => {
  const HEX = new jsSHA('SHA-1', 'TEXT')
  HEX.update(password)
  return HEX.getHash('HEX')
}

export default new Vuex.Store({
  state: {
    api: {
      activity: 'https://hackweekgroup6.cn/api/activity',
      user: 'https://hw.kidonng.me/api/user',
      image: 'https://sm.ms/api/upload'
    },
    login: false,
    user: user(),
    edit: false,
    title: null,
    menu: false,
    snackbar: {
      show: false,
      type: null,
      message: null
    }
  },
  mutations: {
    login (state, v) { state.login = v },
    user ({ user }, v) { Object.assign(user, v) },
    edit (state, v) { state.edit = v },
    title (state, v) { state.title = document.title = v },
    menu (state, show) { state.menu = show },
    message ({ snackbar }, { show = true, type, message }) {
      snackbar.show = show
      snackbar.type = type
      snackbar.message = message
    }
  },
  actions: {
    auth ({ state, commit }, { username, password, callback }) {
      axios.post(state.api.user, {
        username: username,
        password: hash(password)
      }).then(resp => {
        if (resp.data.success) callback(resp.data.token)
        else commit('message', { type: 'error', message: resp.data.message })
      })
        .catch(() => commit('message', { type: 'error', message: '验证失败' }))
    },
    info ({ state, commit }, token) {
      commit('login', true)
      localStorage.setItem('token', token)
      axios.get(state.api.user,{ headers: { Authorization: token }})
        .then(resp => {
          if (resp.data.success) commit('user', resp.data)
          else commit('message', { type: 'error', message: resp.data.message })
        })
        .catch(() => commit('message', { type: 'error', message: '获取登录信息失败' }))
    },
    logout ({ commit }) {
      commit('login', false)
      localStorage.removeItem('token')
      commit('user', user())
    },
    upload ({ state, commit }, { image, callback }) {
      const DATA = new FormData()
      DATA.append('smfile', image, image.name)
      axios.post(state.api.image, DATA, { headers: { 'Content-Type': 'multipart/form-data' }})
        .then(resp => {
          if (resp.data.code === 'success') {
            callback(resp.data.data.url)
            commit('message', { type: 'done', message: '上传成功' })
          } else commit('message', { type: 'error', message: resp.data.msg })
        })
        .catch(() => commit('message', { type: 'error', message: '上传失败' }))
    }
  }
})
