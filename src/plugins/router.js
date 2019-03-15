import Vue from 'vue'
import Router from 'vue-router'
import { store } from './store'
import Index from '../views/Index'

Vue.use(Router)

const router = new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      component: Index
    },
    {
      path: '/login',
      component: () => import(/* webpackChunkName: 'login' */ '../views/Login'),
      meta: {
        title: '登录',
        noLoggedIn: true
      }
    },
    {
      path: '/register',
      component: () =>
        import(/* webpackChunkName: 'register' */ '../views/Register'),
      meta: {
        title: '注册',
        noLoggedIn: true
      }
    },
    {
      path: '/user/:username',
      component: () => import(/* webpackChunkName: 'user' */ '../views/User')
    },
    { path: '/personal' },
    { path: '/logout' },
    {
      path: '*',
      redirect: '/'
    }
  ],
  scrollBehavior: () => ({
    x: 0,
    y: 0
  })
})
router.afterEach(to => {
  if (to.meta.noLoggedIn && store.state.login) router.push('/')
  else if (to.path === '/personal')
    router.push(`/user/${localStorage.username}`)
  else if (to.path === '/logout') {
    store.commit('login', false)
    delete localStorage.username
    delete localStorage.token
    router.push('/')
  }
  if (to.meta.title) store.commit('title', to.meta.title)
  if (store.state.menu) store.commit('menu', false)
})

export default router
