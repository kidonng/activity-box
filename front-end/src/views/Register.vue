<template>
  <v-layout column>
    <v-form v-model="valid" ref="form">
      <v-text-field
        v-model.trim="user.username"
        :rules="[v => !!v || '']"
        prepend-icon="account_circle"
        label="用户名"
        maxlength="20"
        autofocus
      ></v-text-field>
      <v-text-field
        v-model.trim="password.original"
        :rules="[v => v && v.length >= 8 || '至少 8 位']"
        :type="password.visible ? 'text' : 'password'"
        prepend-icon="lock"
        :append-icon="password.visible ? 'visibility_off' : 'visibility'"
        @click:append="password.visible = !password.visible"
        label="密码"
        hint="至少 8 位"
        maxlength="20"
      ></v-text-field>
      <v-text-field
        v-model.trim="password.confirm"
        :rules="[
        v => !!v || '',
        v => v === password.original || '密码不一致']"
        :type="password.visible ? 'text' : 'password'"
        prepend-icon=" "
        label="确认密码"
      ></v-text-field>
      <v-text-field
        v-model.trim="user.nickname"
        :rules="[v => !!v || '']"
        prepend-icon="alternate_email"
        label="昵称"
        maxlength="20"
      ></v-text-field>
      <v-textarea
        v-model.trim="user.bio"
        prepend-icon="public"
        label="简介"
        hint="可选"
        persistent-hint
        rows="3"
        no-resize
        maxlength="100"
      ></v-textarea>
      <v-text-field
        v-model.trim="user.realname"
        prepend-icon="person"
        label="真实姓名"
        hint="可选，用于验证真实身份，不公开，设置后不能修改"
        persistent-hint
        maxlength="15"
      ></v-text-field>
      <v-text-field
        v-model.trim="user.groupname"
        prepend-icon="group"
        label="所属团体"
        hint="可选，用于验证真实身份，设置后不能修改"
        persistent-hint
        maxlength="15"
      ></v-text-field>
    </v-form>

    <v-layout>
      <v-spacer></v-spacer>
      <v-btn color="primary" @click="register" :disabled="!valid">注册</v-btn>
    </v-layout>
  </v-layout>
</template>

<script>
import jsSHA from 'jssha/src/sha1'

export default {
  data () {
    return {
      valid: null,
      user: {
        username: null,
        nickname: null,
        bio: null,
        realname: null,
        groupname: null
      },
      password: {
        original: null,
        confirm: null,
        visible: false
      }
    }
  },
  methods: {
    register () {
      const HEX = new jsSHA('SHA-1', 'TEXT')
      HEX.update(this.password.original)
      this.$ajax.post(this.$store.state.api.user, Object.assign(this.user, { password: HEX.getHash('HEX') }))
        .then(resp => {
          if (resp.data.success) {
            this.$store.dispatch('info', resp.data.token)
            this.$router.push({ name: 'index' })
            this.$store.commit('message', { type: 'done', message: '注册成功' })
          } else this.$store.commit('message', { type: 'error', message: resp.data.message })
        })
        .catch(() => this.$store.commit('message', { type: 'error', message: '注册失败' }))
    }
  }
}
</script>
