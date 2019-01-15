<template>
  <v-app>
    <Toolbar></Toolbar>
    <Message></Message>
    <Menu></Menu>

    <v-content>
      <v-container>
        <router-view></router-view>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
  import Toolbar from './components/Toolbar'
  import Message from './components/Message'
  import Menu from './components/Menu'

  export default {
    components: {
      Toolbar,
      Menu,
      Message
    },
    created () {
      if (localStorage.token) this.$store.dispatch('info', localStorage.token)

      this.$router.afterEach(to => {
        this.$store.commit('title', to.meta.title)
        if (typeof to.meta.login !== 'undefined' && this.$store.state.login !== to.meta.login) this.$router.push({ name: 'index' })
      })

      this.$store.commit('message', { type: 'info', text: '开发中，部分功能暂不可用' })
    }
  }
</script>

<style lang="stylus">
  .application
    font-family Roboto, PingFang SC, Hiragino Sans GB, Noto Sans CJK SC, Source Han Sans SC, Source Han Sans CN, Microsoft YaHei, Wenquanyi Micro Hei, WenQuanYi Zen Hei, ST Heiti, SimHei, WenQuanYi Zen Hei Sharp, sans-serif;
</style>
