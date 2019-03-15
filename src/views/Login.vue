<template>
  <v-layout column>
    <div class="text-xs-center">
      <v-dialog v-model="dialog" lazy>
        <v-img
          slot="activator"
          :src="require('../assets/logo.png')"
          width="160"
        ></v-img>
        <v-card>
          <v-card-title class="headline">
            开发团队
            <v-spacer></v-spacer>
            <v-btn @click="dialog = false" icon>
              <v-icon>close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text class="text-xs-center">
            <div class="title">南昌大学家园工作室</div>
            <div class="subheading">2018 Hack Week 👑冠军</div>
            <v-tooltip v-for="(person, i) in group" bottom>
              <v-avatar slot="activator" class="ma-2" size="75">
                <v-img
                  :src="require(`../assets/avatars/${person.avatar}`)"
                ></v-img>
              </v-avatar>
              <span>{{ group[i].post }} - {{ group[i].name }}</span>
            </v-tooltip>
          </v-card-text>
        </v-card>
      </v-dialog>
      <div class="display-1">盒事</div>
    </div>
    <v-form v-model="form.valid" ref="form">
      <v-text-field
        v-model.trim="form.username"
        :rules="[v => !!v || '']"
        prepend-icon="person"
        label="用户名"
        autofocus
      ></v-text-field>
      <v-text-field
        v-model.trim="form.password"
        @click:append="form.visible = !form.visible"
        :rules="[v => !!v || '']"
        :type="form.visible ? 'text' : 'password'"
        prepend-icon="lock"
        :append-icon="form.visible ? 'visibility' : 'visibility_off'"
        label="密码"
      ></v-text-field>
      <v-layout>
        <v-btn to="/register" flat>注册</v-btn>
        <v-spacer></v-spacer>
        <v-btn @click="login" :disabled="!form.valid" color="primary" depressed
          >登录</v-btn
        >
      </v-layout>
    </v-form>
  </v-layout>
</template>

<script>
export default {
  data: () => ({
    dialog: false,
    group: [
      { post: '前端', name: 'Kid', avatar: 'front-end.png' },
      { post: '后端', name: '65wu', avatar: 'back-end-1.png' },
      { post: '后端', name: '西加贝贝', avatar: 'back-end-2.png' },
      { post: '设计', name: '兔玖', avatar: 'designer.png' },
      { post: '运营', name: 'deep', avatar: 'om.png' },
      { post: '行政', name: 'Weaver', avatar: 'admin.png' },
      { post: '产品', name: '想喝酸奶', avatar: 'pm.png' }
    ],
    form: {
      valid: false,
      username: null,
      password: null,
      visible: false
    }
  }),
  methods: {
    login() {
      this.$store.dispatch('login', {
        username: this.form.username,
        password: this.form.password,
        callback: () => this.$router.back()
      })
    }
  }
}
</script>
