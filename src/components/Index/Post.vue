<template>
  <v-dialog v-if="$store.state.login" v-model="dialog" fullscreen>
    <v-tooltip slot="activator" top>
      <v-btn slot="activator" fixed bottom right fab color="primary">
        <v-icon>add</v-icon>
      </v-btn>
      <span>发布</span>
    </v-tooltip>

    <v-card>
      <v-toolbar>
        <v-btn @click="close" icon>
          <v-icon>close</v-icon>
        </v-btn>
        <v-toolbar-title>发布</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-tooltip bottom>
          <v-btn slot="activator" @click="publish" :disabled="!valid" icon
            ><v-icon>send</v-icon>
          </v-btn>
          <span>发布</span>
        </v-tooltip>
      </v-toolbar>

      <v-card-text>
        <v-form v-model="valid" ref="form">
          <v-hover v-if="activity.photo">
            <v-img
              slot-scope="{ hover }"
              :src="activity.photo"
              aspect-ratio="1.7778"
              class="mb-3"
              alt="配图"
            >
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
                  <v-menu attach=".hover" offset-x>
                    <v-btn slot="activator" large icon>
                      <v-icon color="white">menu</v-icon>
                    </v-btn>
                    <v-list>
                      <v-list-tile @click="$refs.upload.click()">
                        <v-icon class="mr-3">edit</v-icon>
                        修改
                      </v-list-tile>
                      <v-list-tile @click="activity.photo = null">
                        <v-icon class="mr-3">close</v-icon>
                        删除
                      </v-list-tile>
                    </v-list>
                  </v-menu>
                </v-layout>
              </v-fade-transition>
            </v-img>
          </v-hover>
          <v-btn
            v-else
            @click="$refs.upload.click()"
            :loading="uploading"
            class="add-photo title"
            block
            flat
          >
            <v-icon class="mr-2">add</v-icon>
            添加图片
          </v-btn>
          <input
            @input="upload"
            ref="upload"
            type="file"
            accept="image/*"
            hidden
          />
          <v-text-field v-model="activity.photo" class="d-none"></v-text-field>

          <v-text-field
            v-model.trim="activity.title"
            :rules="[v => !!v || '']"
            label="标题"
            maxlength="30"
            autofocus
          ></v-text-field>
          <v-textarea
            v-model.trim="activity.content"
            :rules="[v => !!v || '']"
            label="详情"
            maxlength="1000"
            rows="3"
            auto-grow
          ></v-textarea>
          <v-select
            v-model="activity.category"
            :items="['展演', '讲座', '赛事', '招聘', '影音', '其他']"
            :rules="[v => !!v || '']"
            no-data-text
            label="分类"
          ></v-select>
          <v-layout>
            <v-dialog
              v-model="dialogs.date"
              :return-value.sync="activity.date"
              ref="date"
              class="mr-5"
              persistent
              width="290px"
            >
              <v-text-field
                slot="activator"
                v-model="activity.date"
                :rules="[v => !!v || '']"
                label="日期"
                readonly
              ></v-text-field>
              <v-date-picker
                v-model="activity.date"
                :min="min"
                locale="zh-Hans"
                scrollable
              >
                <v-spacer></v-spacer>
                <v-btn @click="dialogs.date = false" flat>取消</v-btn>
                <v-btn @click="$refs.date.save(activity.date)" flat>确定</v-btn>
              </v-date-picker>
            </v-dialog>
            <v-dialog
              v-model="dialogs.time"
              :return-value.sync="activity.time"
              ref="time"
              persistent
              width="290px"
            >
              <v-text-field
                slot="activator"
                v-model="activity.time"
                :rules="[v => !!v || '']"
                label="时间"
                readonly
              ></v-text-field>
              <v-time-picker v-model="activity.time" format="24hr">
                <v-spacer></v-spacer>
                <v-btn @click="dialogs.time = false" flat>取消</v-btn>
                <v-btn @click="$refs.time.save(activity.time)" flat>确定</v-btn>
              </v-time-picker>
            </v-dialog>
          </v-layout>
          <v-combobox
            v-model.trim="activity.location"
            :items="['天健', '休闲', '商业街', '医学院']"
            :rules="[v => !!v || '']"
            label="地点"
            maxlength="50"
          ></v-combobox>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data: () => ({
    dialog: false,
    valid: false,
    activity: {
      title: null,
      content: null,
      category: null,
      date: null,
      time: null,
      location: null,
      photo: null
    },
    dialogs: {
      date: false,
      time: false
    },
    uploading: false
  }),
  computed: {
    min() {
      const now = new Date()
      const month = (now.getMonth() + 1).toString()
      const date = now.getDate().toString()
      return `${now.getFullYear()}-${
        month.length === 2 ? month : `0${month}`
      }-${date.length === 2 ? date : `0${date}`}`
    }
  },
  watch: {
    dialog(v) {
      if (v) {
        const now = new Date()
        this.activity.date = `${now.getFullYear()}-${now.getMonth() +
          1}-${now.getDate()}`
        this.activity.time = now.toTimeString().substring(0, 5)
      }
    }
  },
  methods: {
    close() {
      this.$refs.form.reset()
      this.dialog = false
    },
    upload() {
      this.uploading = true
      this.activity.photo = null
      this.$store.dispatch('upload', {
        photo: this.$refs.upload.files[0],
        callback: url => {
          this.activity.photo = url
          this.uploading = false
          this.$refs.upload.value = null
        }
      })
    },
    publish() {
      ;(async () => {
        const res = await this.api
          .post('activity', {
            headers: { Authorization: localStorage.token },
            json: this.activity
          })
          .json()

        if (res.success) {
          this.$store.commit('message', { type: 'done', text: '发布成功' })
          dialog = false
        } else
          this.$store.commit('message', { type: 'error', text: res.message })
      })()
    }
  }
}
</script>

<style lang="stylus">
.v-image__placeholder + .v-responsive__content
  display none

.hover
  background rgba(0 0 0 .5)

.add-photo
  color rgba(0 0 0 .54) !important
  height 54px !important
</style>
