<template>
  <v-layout column>
    <v-flex align-self-center>
      <v-img class="logo" width="200" src="/logo.png"></v-img>
    </v-flex>
    <div class="display-1 text-xs-center mb-2">盒事</div>
    <v-form v-model="valid" ref="form">
      <v-text-field
        v-model.trim="username"
        :rules="[v => !!v || '']"
        prepend-icon="account_circle"
        label="用户名"
        browser-autocomplete="username"
        autofocus
      ></v-text-field>
      <v-text-field
        v-model.trim="password"
        :rules="[v => !!v || '']"
        :type="visible ? 'text' : 'password'"
        prepend-icon="lock"
        :append-icon="visible ? 'visibility_off' : 'visibility'"
        @click:append="visible = !visible"
        label="密码"
        browser-autocomplete="current-password"
      ></v-text-field>
    </v-form>

    <v-layout justify-space-between>
      <v-btn flat :to="{ name: 'register' }">注册</v-btn>
      <v-btn color="primary" @click="login" :disabled="!valid">登录</v-btn>
    </v-layout>
  </v-layout>
</template>

<script>
export default {
  data () {
    return {
      valid: false,
      username: null,
      password: null,
      visible: false
    }
  },
  methods: {
    login () {
      this.$store.dispatch('auth', {
        username: this.username,
        password: this.password,
        callback: (token) => {
          this.$store.dispatch('info', token)
          this.$router.back()
          this.$store.commit('message', { type: 'done', message: '登录成功' })
        }
      })
    }
  }
}
</script>

<style lang="stylus">
.logo
  margin-top -30px
  margin-bottom -20px
</style>
