<template>
  <v-app>
    <toolbar></toolbar>
    <Menu></Menu>
    <message></message>

    <v-content>
      <v-container fluid>
        <router-view></router-view>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
import Toolbar from './components/Toolbar'
import Menu from './components/Menu'
import Message from './components/Message'

export default {
  components: {
    Toolbar,
    Menu,
    Message
  }
}
</script>

<style lang="stylus">
.application
  font-family Roboto, -apple-system, 'Noto Sans', 'PingFang SC', 'Hiragino Sans GB', 'Noto Sans CJK SC', 'Source Han Sans SC', 'Source Han Sans CN', 'Microsoft YaHei', 'Wenquanyi Micro Hei', 'WenQuanYi Zen Hei', 'ST Heiti', SimHei, 'WenQuanYi Zen Hei Sharp', sans-serif
</style>
