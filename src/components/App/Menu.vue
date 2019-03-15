<template>
  <v-bottom-sheet v-model="$store.state.menu" inset app>
    <v-list>
      <v-list-tile v-for="item in items" :to="item.to">
        <v-list-tile-action>
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-tile-action>
        <v-list-tile-content>
          <v-list-tile-title>{{ item.title }}</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
    </v-list>
  </v-bottom-sheet>
</template>

<script>
export default {
  data: () => ({
    items: [
      { to: '', icon: 'favorite', title: '收藏' },
      { to: '', icon: 'dashboard', title: '管理' },
      { to: '/personal', icon: 'account_circle', title: '个人' },
      { to: '/logout', icon: 'exit_to_app', title: '退出' }
    ]
  })
}
</script>
