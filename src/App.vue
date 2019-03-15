<template>
  <v-app>
    <Toolbar></Toolbar>
    <Menu></Menu>
    <Message></Message>
    <v-content>
      <v-container>
        <router-view></router-view>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
import Toolbar from './components/App/Toolbar'
import Menu from './components/App/Menu'
import Message from './components/App/Message'

export default {
  components: {
    Toolbar,
    Menu,
    Message
  },
  created() {
    if (localStorage.token) this.$store.dispatch('info')
  }
}
</script>
