<template>
  <v-toolbar v-if="search" class="search" app>
    <v-text-field
      v-model.trim="keyword"
      placeholder="搜索活动"
      prepend-inner-icon="arrow_back"
      @click:prepend-inner="search = false"
      @input="!keyword || $router.push({ name: 'search', query: { q: keyword }})"
      solo
      clearable
    ></v-text-field>
  </v-toolbar>

  <v-toolbar v-else app>
    <v-toolbar-side-icon @click="$route.name === 'index' ? search = true : $router.back()">
      <v-icon>{{ $route.name === 'index' ? 'search' : 'arrow_back'}}</v-icon>
    </v-toolbar-side-icon>

    <v-toolbar-title v-if="$route.name !== 'index'">{{ $store.state.title }}</v-toolbar-title>

    <v-spacer></v-spacer>

    <v-tooltip v-if="$route.name === 'user' && $route.params.name === $store.state.user.username" bottom>
      <v-btn slot="activator" icon @click="$store.commit('edit', !edit)">
        <v-icon>{{ edit ? 'cancel' : 'edit' }}</v-icon>
      </v-btn>
      <span>{{ edit ? '取消' : '修改' }}</span>
    </v-tooltip>

    <v-tooltip v-if="$store.state.login" bottom>
      <v-avatar
        slot="activator"
        class="avatar"
        size="40"
        @click="$store.commit('menu', true)"
      >
        <v-img v-if="$store.state.user.avatar" :src="$store.state.user.avatar" alt="头像"></v-img>
        <v-icon v-else size="40">face</v-icon>
      </v-avatar>
      <span>菜单</span>
    </v-tooltip>

    <v-btn
      v-else-if="$route.name !== 'login'"
      class="login subheading"
      color="primary"
      :to="{ name: 'login' }"
    >
      登录
    </v-btn>
  </v-toolbar>
</template>

<script>
  export default {
    data: () => ({
      search: false,
      keyword: null
    }),
    computed: { edit () { return this.$store.state.edit }},
    watch: {
      search (v) {
        if (!v) {
          this.keyword = null
          this.$router.push({ name: 'index' })
        }
      },
      $route (v) { if (v.name !== 'search') this.search = false }
    },
    created () {
      onload = () => {
        this.keyword = this.$route.query.q
        this.search = !!this.keyword
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
</style>

<style lang="stylus" scoped>
  .avatar
    cursor pointer

  .login
    min-width 64px
</style>
