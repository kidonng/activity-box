<template>
  <v-toolbar app>
    <template v-if="keyword === undefined">
      <v-tooltip v-if="$route.path === '/'" bottom>
        <v-btn slot="activator" @click="keyword = null" icon>
          <v-icon>search</v-icon>
        </v-btn>
        <span>搜索</span>
      </v-tooltip>
      <template v-else>
        <v-btn @click="$router.push('/')" icon>
          <v-icon>arrow_back</v-icon>
        </v-btn>
        <v-toolbar-title>
          {{ $store.state.title }}
        </v-toolbar-title>
      </template>
      <v-spacer></v-spacer>
      <v-tooltip v-if="$store.state.login" bottom>
        <v-btn slot="activator" @click="$store.commit('menu', true)" icon>
          <v-avatar v-if="$store.state.user.avatar" size="36">
            <v-img :src="$store.state.user.avatar" alt="头像">
              <v-progress-circular
                slot="placeholder"
                indeterminate
              ></v-progress-circular>
            </v-img>
          </v-avatar>
          <v-icon v-else size="36">face</v-icon>
        </v-btn>
        <span>菜单</span>
      </v-tooltip>
      <v-tooltip
        v-else-if="$route.path !== '/login' && $route.path !== '/register'"
        bottom
      >
        <v-btn slot="activator" to="/login" icon>
          <v-icon>person</v-icon>
        </v-btn>
        <span>登录</span>
      </v-tooltip>
    </template>

    <template v-else>
      <v-tooltip bottom>
        <v-btn slot="activator" @click="back" icon>
          <v-icon>arrow_back</v-icon>
        </v-btn>
        <span>返回</span>
      </v-tooltip>
      <v-text-field
        v-model.trim="keyword"
        @input="!keyword || $router.push(`/search?q=${keyword}`)"
        placeholder="搜索"
        clearable
      ></v-text-field>
    </template>
  </v-toolbar>
</template>

<script>
export default {
  data: () => ({
    keyword: undefined
  }),
  created() {
    onload = () => (this.keyword = this.$route.query.q)
  },
  methods: {
    back() {
      this.keyword = undefined
      this.$router.push('/')
    }
  }
}
</script>
