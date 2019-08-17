<template>
  <v-toolbar v-if="search" :class="{ search: search }" app>
    <v-text-field
      v-model.trim="keyword"
      placeholder="搜索活动"
      prepend-inner-icon="arrow_back"
      @click:prepend-inner="back"
      solo
      clearable
    ></v-text-field>
  </v-toolbar>

  <v-toolbar v-else app>
    <v-toolbar-side-icon @click="$route.name === 'index' ? search = true : $router.back()">
      <v-icon v-if="$route.name === 'index'">search</v-icon>
      <v-icon v-else>arrow_back</v-icon>
    </v-toolbar-side-icon>

    <v-toolbar-title v-if="$route.name !== 'index'">{{ $store.state.title }}</v-toolbar-title>

    <v-spacer></v-spacer>

    <v-tooltip v-if="$route.params.name === $store.state.user.username && !$store.state.edit" bottom>
      <v-btn slot="activator" icon @click="$store.commit('edit', true)">
        <v-icon>edit</v-icon>
      </v-btn>
      <span>修改</span>
    </v-tooltip>

    <v-tooltip v-if="$store.state.login" bottom>
      <v-avatar
        slot="activator"
        class="btn-avatar"
        size="40"
        @click="$store.commit('menu', true)"
      >
        <v-img v-if="$store.state.user.avatar" :src="$store.state.user.avatar" alt="头像"></v-img>
        <v-icon v-else size="40">face</v-icon>
      </v-avatar>
      <span>菜单</span>
    </v-tooltip>

    <v-toolbar-items v-else-if="!$route.meta.account">
      <v-btn class="btn-login" flat :to="{ name: 'login' }">登录</v-btn>
    </v-toolbar-items>
  </v-toolbar>
</template>

<script>
export default {
  data () {
    return {
      search: false,
      keyword: null
    }
  },
  watch: {
    keyword (v) { if (v) this.$router.push({ name: 'search', query: { q: v }})},
    $route () {
     if (this.$route.name === 'search' && !this.search) {
        this.search = true
        this.keyword = this.$route.query.q
      }
    }
  },
  methods: {
    back () {
      this.search = false
      this.keyword = null
      this.$router.push({ name: 'index' })
    }
  }
}
</script>

<style lang="stylus">
.search .v-toolbar__content
  padding 0

  .v-input,
  .v-input__control,
  .v-input__slot
    height 100%

  .v-input__slot
    margin 0

.btn-avatar
  cursor pointer

.btn-login
  min-width 48px
</style>
