<template>
  <v-form v-model="valid" ref="form">
    <v-text-field
      v-model.trim="user.username"
      :rules="[v => !!v || '']"
      label="用户名"
      hint="设置后不能修改"
      maxlength="20"
      autofocus
    ></v-text-field>
    <v-text-field
      v-model.trim="user.password"
      @click:append="visible = !visible"
      :rules="[v => (v && v.length >= 8) || '至少 8 位']"
      :type="visible ? 'text' : 'password'"
      :append-icon="visible ? 'visibility' : 'visibility_off'"
      label="密码"
      hint="至少 8 位"
    ></v-text-field>
    <v-text-field
      :rules="[v => v === user.password || '密码不一致']"
      :type="visible ? 'text' : 'password'"
      label="确认密码"
    ></v-text-field>
    <v-textarea
      v-model.trim="user.bio"
      label="简介"
      hint="可选"
      rows="4"
      maxlength="100"
    ></v-textarea>
    <v-text-field
      v-model.trim="user.realname"
      :rules="[v => !!v || '']"
      label="真实姓名"
      hint="设置后不能修改，不公开"
      maxlength="20"
    ></v-text-field>
    <v-text-field
      v-model.trim="user.groupname"
      label="所属组织"
      hint="可选"
      maxlength="20"
    ></v-text-field>
    <v-layout>
      <v-btn to="/login" flat>登录</v-btn>
      <v-spacer></v-spacer>
      <v-btn @click="register" :disabled="!valid" color="primary" depressed
        >注册</v-btn
      >
    </v-layout>
  </v-form>
</template>

<script>
export default {
  data: () => ({
    valid: false,
    user: {
      username: null,
      password: null,
      bio: null,
      realname: null,
      groupname: null
    },
    visible: false
  }),
  methods: {
    register() {
      ;(async () => {
        const res = await this.api.post('user', { json: this.user }).json()

        if (res.msg === 'OK')
          this.$store.dispatch('login', {
            username: this.user.username,
            password: this.user.password,
            callback: () => this.$router.push('/')
          })
        else this.$store.commit('message', { type: 'error', text: res.msg })
      })()
    }
  }
}
</script>
