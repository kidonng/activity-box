<template>
  <v-layout column>
    <v-flex align-self-center>
      <v-dialog v-model="dialog.show" lazy>
        <v-img
          slot="activator"
          class="logo"
          :src="require('../assets/logo.png')"
          width="200"
        ></v-img>
        <v-card>
          <v-card-title class="headline">
            制作团队
            <v-spacer></v-spacer>
            <v-btn icon @click="dialog.show = false">
              <v-icon>close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <div class="subheading text-xs-center mb-3">
              <div><strong>{{ dialog.name || '南昌大学家园工作室' }}</strong></div>
              <div>{{ dialog.bio || '2018 Hack Week 👑冠军' }}</div>
            </div>
            <v-layout wrap justify-center>
              <v-avatar class="ma-1" v-for="(person, i) in dialog.group" :key="i" size="75">
                <v-img
                  :src="require('../assets/avatars/' + person.avatar)"
                  @mouseover="dialog.name = person.name; dialog.bio = person.bio"
                  @mouseout="dialog.name = dialog.bio = null"
                ></v-img>
              </v-avatar>
            </v-layout>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-flex>
    <div class="display-1 text-xs-center mb-2">盒事</div>
    <v-form v-model="form.valid" ref="form">
      <v-text-field
        v-model.trim="form.username"
        :rules="[v => !!v || '']"
        prepend-icon="account_circle"
        label="用户名"
        browser-autocomplete="username"
        autofocus
      ></v-text-field>
      <v-text-field
        v-model.trim="form.password"
        :rules="[v => !!v || '']"
        :type="form.visible ? 'text' : 'password'"
        prepend-icon="lock"
        :append-icon="form.visible ? 'visibility_off' : 'visibility'"
        @click:append="form.visible = !form.visible"
        label="密码"
        browser-autocomplete="current-password"
      ></v-text-field>
    </v-form>

    <v-layout justify-space-between>
      <v-btn flat :to="{ name: 'register' }">注册</v-btn>
      <v-btn color="primary" @click="login" :disabled="!form.valid">登录</v-btn>
    </v-layout>
  </v-layout>
</template>

<script>
  export default {
    data: () => ({
      form: {
        valid: false,
        username: null,
        password: null,
        visible: false
      },
      dialog: {
        show: false,
        name: null,
        bio: null,
        group: [
          { avatar: 'designer.png', name: '设计', bio: '兔玖' },
          { avatar: 'om.png', name: '运营', bio: 'deep' },
          { avatar: 'admin.png', name: '行政', bio: 'Weaver' },
          { avatar: 'front-end.png', name: '前端', bio: 'Kid' },
          { avatar: 'back-end-1.png', name: '后端', bio: '65wu' },
          { avatar: 'back-end-2.png', name: '后端', bio: '西加贝贝' },
          { avatar: 'pm.png', name: '产品', bio: '想喝酸奶' }
        ]
      }
    }),
    methods: {
      login () {
        this.$store.dispatch('auth', {
          username: this.form.username,
          password: this.form.password,
          callback: (token) => {
            this.$store.dispatch('info', token)
            this.$router.back()
            this.$store.commit('message', { type: 'done', text: '登录成功' })
          }
        })
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .logo
    margin-top -30px
    margin-bottom -20px
</style>
