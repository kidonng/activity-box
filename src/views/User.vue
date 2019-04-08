<template>
  <v-layout column>
    <v-form v-if="edit" v-model="valid" ref="info">
      <div class="text-xs-center">
        <v-hover v-if="user.avatar">
          <v-avatar slot-scope="{ hover }" class="avatar" size="128">
            <v-img :src="user.avatar" alt="头像">
              <v-layout
                slot="placeholder"
                fill-height
                align-center
                justify-center
              >
                <v-progress-circular indeterminate></v-progress-circular>
              </v-layout>
              <v-fade-transition>
                <v-layout
                  v-if="hover"
                  class="hover"
                  fill-height
                  align-center
                  justify-center
                >
                  <v-menu attach=".avatar" min-width="144" offset-x>
                    <v-btn slot="activator" large icon>
                      <v-icon color="white">menu</v-icon>
                    </v-btn>
                    <v-list>
                      <v-list-tile @click="$refs.upload.click()">
                        <v-icon class="mr-3">edit</v-icon>
                        修改
                      </v-list-tile>
                      <v-list-tile @click="user.avatar = null">
                        <v-icon class="mr-3">close</v-icon>
                        恢复默认
                      </v-list-tile>
                    </v-list>
                  </v-menu>
                </v-layout>
              </v-fade-transition>
            </v-img>
          </v-avatar>
        </v-hover>
        <v-btn
          v-else
          @click="$refs.upload.click()"
          :loading="uploading"
          class="upload-avatar title"
          icon
        >
          <v-flex>
            <v-icon class="mb-3">add</v-icon>
            <div>上传头像</div>
          </v-flex>
        </v-btn>
        <input
          @input="upload"
          ref="upload"
          type="file"
          accept="image/*"
          hidden
        />
      </div>

      <v-text-field
        :value="currentUsername"
        label="用户名"
        disabled
      ></v-text-field>
      <v-textarea
        v-model.trim="user.bio"
        label="简介"
        hint="可选"
        rows="4"
        maxlength="100"
      ></v-textarea>
      <v-text-field
        :value="user.realname"
        label="真实姓名"
        disabled
      ></v-text-field>
      <v-text-field
        v-model.trim="user.groupname"
        label="所属组织"
        hint="可选"
        maxlength="20"
      ></v-text-field>
      <v-text-field
        v-model.trim="passwords.old"
        :rules="[v => !passwords.new || !!v || '请输入旧密码']"
        :type="passwords.visible ? 'text' : 'password'"
        :append-icon="passwords.visible ? 'visibility' : 'visibility_off'"
        @click:append="passwords.visible = !passwords.visible"
        label="旧密码"
        @input="$refs.info.validate()"
      ></v-text-field>
      <v-text-field
        v-model.trim="passwords.new"
        :rules="[
          v => !passwords.old || !!v || '请输入新密码',
          v => !v || v.length >= 8 || '至少 8 位',
          v => !v || v !== passwords.old || '新旧密码相同'
        ]"
        :type="passwords.visible ? 'text' : 'password'"
        label="新密码"
        @input="$refs.info.validate()"
      ></v-text-field>
      <v-text-field
        :rules="[v => !passwords.old || v === passwords.new || '密码不一致']"
        :type="passwords.visible ? 'text' : 'password'"
        label="确认新密码"
        @input="$refs.info.validate()"
      ></v-text-field>

      <v-dialog v-model="dialog.show">
        <v-card>
          <v-card-title class="headline">
            注销
            <v-spacer></v-spacer>
            <v-btn @click="dialog.show = false" icon>
              <v-icon>close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            注销将删除该账号产生的所有数据且无法恢复。
            <v-checkbox
              v-model="dialog.confirmed"
              label="我已了解注销的后果"
              color="primary"
            ></v-checkbox>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn @click="deactivate" :disabled="!dialog.confirmed" flat
              >注销</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-layout>
        <v-btn @click="dialog.show = true" flat>注销</v-btn>
        <v-spacer></v-spacer>
        <v-btn @click="cancel" depressed>取消</v-btn>
        <v-btn @click="update" :disabled="!valid" color="primary" depressed
          >保存</v-btn
        >
      </v-layout>
    </v-form>

    <div v-else class="text-xs-center">
      <v-avatar size="128">
        <v-img v-if="user.avatar" :src="user.avatar" alt="头像"></v-img>
        <v-icon v-else size="128">face</v-icon>
      </v-avatar>
      <div class="title mt-3 mb-2">
        {{ routeUsername }}
      </div>
      <div>
        <v-btn @click="edit = true">编辑</v-btn>
      </div>
    </div>
  </v-layout>
</template>

<script>
export default {
  data: () => ({
    edit: false,
    valid: true,
    uploading: false,
    user: {
      avatar: null
    },
    passwords: {
      old: null,
      new: null,
      visible: false
    },
    dialog: {
      show: false,
      confirmed: false
    }
  }),
  computed: {
    currentUsername: () => localStorage.username,
    routeUsername() {
      return this.$route.params.username
    }
  },
  created() {
    this.$store.commit('title', this.routeUsername)
    ;(async () => {
      const res = await this.api(`user/${this.routeUsername}`, {
        headers: { Authorization: localStorage.token }
      }).json()

      if (res.msg === 'OK') Object.assign(this.user, res)
      else this.$store.commit('message', { type: 'error', text: res.msg })
    })()
  },
  methods: {
    upload() {
      this.uploading = true
      this.user.avatar = null
      this.$store.dispatch('upload', {
        photo: this.$refs.upload.files[0],
        callback: url => {
          this.user.avatar = url
          this.uploading = false
          this.$refs.upload.value = null
        }
      })
    },
    cancel() {
      Object.assign(this.user, this.$store.state.user)
      this.edit = false
    },
    update() {
      ;(async () => {
        const res = await this.api
          .put(`user/${this.currentUsername}`, {
            headers: { Authorization: localStorage.token },
            json: Object.assign(
              {
                oldPassword: this.passwords.old,
                newPassword: this.passwords.new
              },
              this.user
            )
          })
          .json()

        if (res.msg === 'OK') {
          this.$store.commit('message', { type: 'done', text: '修改成功' })
          this.$store.commit('user', this.user)
          if (res.token) localStorage.token = res.token
          this.edit = false
        } else this.$store.commit('message', { type: 'error', text: res.msg })
      })()
    },
    deactivate() {
      ;(async () => {
        const res = await this.api
          .delete(`user/${this.currentUsername}`, {
            headers: { Authorization: localStorage.token }
          })
          .json()

        if (res.msg === 'OK') {
          this.$store.commit('message', { type: 'done', text: '注销成功' })
          this.$router.push('/logout')
        } else this.$store.commit('message', { type: 'error', text: res.msg })
      })()
    }
  }
}
</script>

<style lang="stylus">
.upload-avatar
  color rgba(0 0 0 0.54) !important
  height 128px
  width @height
</style>
