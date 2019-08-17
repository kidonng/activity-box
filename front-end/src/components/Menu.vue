<template>
  <v-bottom-sheet v-model="menu" inset max-width="500">
    <v-list>
      <v-list-tile :to="{ name: 'favorite' }" @click="menu = false">
        <v-list-tile-action>
          <v-icon>favorite</v-icon>
        </v-list-tile-action>

        <v-list-tile-content>
          <v-list-tile-title>收藏</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>

      <v-list-tile :to="{ name: 'manage' }" @click="menu = false">
        <v-list-tile-action>
          <v-icon>dashboard</v-icon>
        </v-list-tile-action>

        <v-list-tile-content>
          <v-list-tile-title>管理</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>

      <v-list-tile :to="{ name: 'user', params: { name: $store.state.user.username || ' ' }}" @click="menu = false">
        <v-list-tile-action>
          <v-icon>account_circle</v-icon>
        </v-list-tile-action>

        <v-list-tile-content>
          <v-list-tile-title>个人</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>

      <v-list-tile @click="logout">
        <v-list-tile-action>
          <v-icon>exit_to_app</v-icon>
        </v-list-tile-action>

        <v-list-tile-content>
          <v-list-tile-title>注销</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
    </v-list>
  </v-bottom-sheet>
</template>

<script>
export default {
  computed: {
    menu: {
      get () { return this.$store.state.menu },
      set (v) { this.$store.commit('menu', v) }
    }
  },
  methods: {
    logout () {
      this.menu = false
      this.$store.dispatch('logout')
      this.$router.push({ name: 'index' })
      this.$store.commit('message', { type: 'done', message: '已注销' })
    }
  }
}
</script>
