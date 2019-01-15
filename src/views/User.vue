<template>
  <v-layout v-if="$store.state.edit" column>
    <v-flex align-self-center>
      <v-avatar class="mb-2" size="125">
        <v-img v-if="user.avatar" :src="user.avatar" alt="头像"></v-img>
        <v-tooltip v-else bottom>
          <v-icon slot="activator" size="125" @click="$refs.upload.click()">add_photo_alternate</v-icon>
          <input
            type="file"
            accept="image/*"
            ref="upload"
            @input="$store.dispatch('upload', { image: $refs.upload.files[0], callback: (url) => { user.avatar = url }})"
            hidden>
          <span>上传头像</span>
        </v-tooltip>
      </v-avatar>
    </v-flex>

    <v-form v-model="user.valid" ref="user">
      <v-text-field
        v-model="user.username"
        prepend-icon="account_circle"
        label="用户名"
        disabled
      ></v-text-field>
      <v-text-field
        v-model.trim="user.nickname"
        :rules="[v => !!v || '']"
        prepend-icon="alternate_email"
        label="昵称"
        maxlength="20"
        autofocus
      ></v-text-field>
      <v-textarea
        v-model.trim="user.bio"
        prepend-icon="public"
        label="简介"
        rows="3"
        no-resize
        maxlength="100"
      ></v-textarea>
      <v-text-field
        v-model="user.realname"
        prepend-icon="person"
        label="真实姓名"
        :disabled="!!$store.state.user.realname"
      ></v-text-field>
      <v-text-field
        v-model="user.groupname"
        prepend-icon="group"
        label="所属团体"
        :disabled="!!$store.state.user.groupname"
      ></v-text-field>
      <v-text-field
        v-model.trim="password.old"
        :rules="[v => !password.new || !!v || '请输入旧密码']"
        :type="password.visible ? 'text' : 'password'"
        prepend-icon="lock"
        :append-icon="password.visible ? 'visibility_off' : 'visibility'"
        @click:append="password.visible = !password.visible"
        label="旧密码"
        hint="留空表示不修改"
        persistent-hint
        browser-autocomplete="current-password"
        @input="$refs.user.validate()"
      ></v-text-field>
      <v-text-field
        v-model.trim="password.new"
        :rules="[
        v => !v || v.length >= 8 || '至少 8 位',
        v => !password.old || !!v || '请输入新密码',
        v => !v || v !== password.old || '新旧密码相同']"
        :type="password.visible ? 'text' : 'password'"
        prepend-icon=" "
        label="新密码"
        browser-autocomplete="new-password"
        @input="$refs.user.validate()"
      ></v-text-field>
      <v-text-field
        v-model.trim="password.confirm"
        :rules="[v => !(password.new || v) || v === password.new || '密码不一致']"
        :type="password.visible ? 'text' : 'password'"
        prepend-icon=" "
        label="确认新密码"
        browser-autocomplete="new-password"
        @input="$refs.user.validate()"
      ></v-text-field>
      <v-layout>
        <v-dialog v-model="dialog.show">
          <v-btn slot="activator" flat :disabled="!password.old">删除账号</v-btn>
          <v-card>
            <v-card-title class="headline">
              删除账号
              <v-spacer></v-spacer>
              <v-btn icon @click="dialog.show = false">
                <v-icon>close</v-icon>
              </v-btn>
            </v-card-title>

            <v-card-text>
              <div class="subheading">
                <strong>删除账号后，您将失去所有与您相关的信息！</strong>为确保账号安全，请完成以下验证：
              </div>
              <v-form v-model="dialog.valid" ref="dialog">
                <v-text-field
                  v-model.trim="dialog.username"
                  :rules="[v => v === user.username || '']"
                  prepend-icon="account_circle"
                  browser-autocomplete="username"
                  autofocus
                >
                  <template slot="label">请输入<strong>用户名</strong></template>
                </v-text-field>
                <v-text-field v-model.trim="dialog.confirm" :rules="[v => v === '删除我的账号' || '']" prepend-icon="delete">
                  <template slot="label">请输入<strong>删除我的账号</strong></template>
                </v-text-field>
              </v-form>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="primary" @click="deactivate" :disabled="!dialog.valid">确认</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-spacer></v-spacer>
        <v-btn color="primary" @click="update" :disabled="!user.valid">保存</v-btn>
      </v-layout>
    </v-form>
  </v-layout>

  <v-layout v-else column align-center>
    <v-avatar :class="{ 'mb-2': user.avatar }" size="125">
      <v-img v-if="user.avatar" :src="user.avatar" alt="头像"></v-img>
      <v-icon v-else size="125">face</v-icon>
    </v-avatar>

    <div class="headline font-weight-medium" :class="{ 'my-1': user.avatar, 'mb-1': !user.avatar }">{{ user.nickname }}</div>

    <v-tooltip v-if="user.verified" class="mb-2" bottom>
      <v-chip slot="activator" color="teal" text-color="white">
        <v-avatar>
          <v-icon>verified_user</v-icon>
        </v-avatar>
        {{ user.groupname }}
      </v-chip>
      <span>已验证</span>
    </v-tooltip>
    <v-chip v-else-if="user.groupname" class="mb-2">{{ user.groupname }}</v-chip>

    <v-expansion-panel>
      <v-expansion-panel-content ripple>
        <div slot="header">简介</div>
        <v-card>
          <v-card-text>{{ user.bio || '这个人很懒，什么都没写' }}</v-card-text>
        </v-card>
      </v-expansion-panel-content>
    </v-expansion-panel>
  </v-layout>
</template>

<script>
  export default {
    data: () => ({
      user: {
        valid: true,
        nickname: null,
        avatar: null
      },
      password: {
        old: null,
        new: null,
        confirm: null,
        visible: false
      },
      dialog: {
        show: false,
        valid: false,
        username: null,
        confirm: null
      }
    }),
    created () {
      (async () => {
        const res = await this.$ajax.get(this.$store.state.api.user, {
          searchParams: { name: this.$route.params.name }
        }).json()

        if (res.success) {
          if (this.$route.name === 'user') this.$store.commit('title', res.nickname)
          Object.assign(this.user, res)
        } else this.$store.commit('message', { type: 'error', text: res.message })
      })()
    },
    watch: {
      '$store.state.edit' (v) {
        Object.assign(this.user, this.$store.state.user)
        if (!v) {
          this.password.old = this.password.new = this.password.confirm = null
          this.password.visible = false
        }
        this.$store.commit('title', v ? '编辑个人信息' : this.user.nickname)
      },
      'dialog.show' () { this.$refs.dialog.reset() }
    },
    beforeRouteLeave (to, from, next) {
      this.$store.commit('edit', false)
      next()
    },
    methods: {
      update () {
        const update = async () => {
          const res = await this.$ajax.put(this.$store.state.api.user, {
            headers: { Authorization: localStorage.getItem('token') },
            json: this.user
          }).json()

          if (res.success) {
            this.$store.commit('edit', false)
            this.$store.commit('user', this.user)
            if (res.token) localStorage.token = res.token
            this.$store.commit('message', { type: 'done', text: '修改成功' })
          } else this.$store.commit('message', { type: 'error', text: res.message })
        }

        if (this.password.new) this.$store.dispatch('auth', {
          username: this.user.username,
          password: this.password.old,
          callback: () => {
            Object.assign(this.user, { password: this.$hash(this.password.new) })
            update()
          }})
        else update()
      },
      deactivate () {
        this.$store.dispatch('auth', {
          username: this.user.username,
          password: this.password.old,
          callback: async () => {
            const res = await this.$ajax.delete(this.$store.state.api.user, {
              headers: { Authorization: localStorage.token },
              searchParams: { name: this.user.username }
            }).json()

            if (res.success) {
              this.$store.dispatch('logout')
              this.$router.push({ name: 'index' })
              this.$store.commit('message', { type: 'done', text: '删除账号成功' })
            } else this.$store.commit('message', { type: 'error', text: res.message })
          }
        })
      }
    }
  }
</script>
