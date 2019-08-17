import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

const router = new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      name: 'index',
      path: '/',
      component: () => import(/* webpackChunkName: 'index' */ '../views/Index'),
      meta: { title: '盒事' }
    },
    {
      name: 'search',
      path: '/search',
      component: () => import(/* webpackChunkName: 'search' */ '../views/Search'),
      meta: { title: '搜索' }
    },
    {
      name: 'activity',
      path: '/activity/:id',
      component: () => import(/* webpackChunkName: 'activity' */ '../views/Activity'),
      meta: { title: '活动' }
    },
    {
      name: 'favorite',
      path: '/favorite',
      component: () => import(/* webpackChunkName: 'favorite' */ '../views/Favorite'),
      meta: { title: '收藏', login: true }
    },
    {
      name: 'manage',
      path: '/manage',
      component: () => import(/* webpackChunkName: 'manage' */ '../views/Manage'),
      meta: { title: '管理', login: true }
    },
    {
      name: 'login',
      path: '/login',
      component: () => import(/* webpackChunkName: 'login' */ '../views/Login'),
      meta: { title: '登录', account: true }
    },
    {
      name: 'register',
      path: '/register',
      component: () => import(/* webpackChunkName: 'register' */ '../views/Register'),
      meta: { title: '注册', account: true }
    },
    {
      name: 'user',
      path: '/user/:name',
      component: () => import(/* webpackChunkName: 'user' */ '../views/User'),
      meta: { title: '用户' }
    },
    {
      path: '*',
      redirect: { name: 'index' }
    }
  ]
})

router.afterEach((to) => {
  if (router.app.$store.state.login) {
     if (to.meta.account) router.back()
  } else {
    // 自动登录
    if (localStorage.getItem('token'))
      router.app.$store.dispatch('info', localStorage.getItem('token'))
    // 登录验证
    if (to.meta.login) router.back()
  }

  router.app.$store.commit('title', to.meta.title)
})

export default router
