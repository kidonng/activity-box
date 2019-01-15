<template>
  <v-dialog v-model="dialog" fullscreen>
    <v-card>
      <v-toolbar>
        <v-btn icon @click="$emit('close')">
          <v-icon>close</v-icon>
        </v-btn>
        <v-toolbar-title>发布活动</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-tooltip bottom>
          <v-btn
            slot="activator"
            icon
            @click="publish"
            :disabled="!valid">
            <v-icon>send</v-icon>
          </v-btn>
          <span>发布</span>
        </v-tooltip>
      </v-toolbar>

      <v-card-text>
        <v-layout :class="{ 'mt-2 mb-3': activity.images[0], 'mb-2': !activity.images[0] }" column align-center>
          <v-img v-if="activity.images[0]" :src="activity.images[0]" width="100%"></v-img>
          <v-tooltip v-else bottom>
            <v-icon slot="activator" size="100" @click="$refs.upload.click()">add_photo_alternate</v-icon>
            <input
              type="file"
              accept="image/*"
              ref="upload"
              @input="$store.dispatch('upload', { image: $refs.upload.files[0], callback: (url) => { activity.images.push(url) }})"
              hidden>
            <span>上传图片</span>
          </v-tooltip>
        </v-layout>
        <v-form v-model="valid" ref="form">
          <v-text-field
            v-model.trim="activity.title"
            :rules="[v => !!v || '']"
            prepend-icon="subject"
            label="标题"
            maxlength="40"
            autofocus
          ></v-text-field>
          <v-textarea
            v-model.trim="activity.description"
            :rules="[v => !!v || '']"
            prepend-icon="description"
            label="内容"
            rows="3"
            auto-grow
          ></v-textarea>
          <v-select
            v-model="activity.category"
            :rules="[v => !!v || '']"
            :items="['展演', '讲座', '赛事', '招聘', '影音', '其他']"
            no-data-text
            prepend-icon="label"
            label="分类"
          ></v-select>
          <v-layout>
            <v-menu v-model="pickers.date" class="mr-4" offset-y :close-on-content-click="false">
              <v-text-field
                slot="activator"
                v-model="activity.date"
                :rules="[v => !!v || '']"
                prepend-icon="calendar_today"
                label="日期"
                readonly
              ></v-text-field>
              <v-date-picker
                v-model="activity.date"
                locale="zh-Hans"
                @input="pickers.date = false"
                no-title
                scrollable
              ></v-date-picker>
            </v-menu>
            <v-menu v-model="pickers.time" offset-y :close-on-content-click="false">
              <v-text-field
                slot="activator"
                v-model="activity.time"
                :rules="[v => !!v || '']"
                prepend-icon="access_time"
                label="时间"
                readonly
              ></v-text-field>
              <v-time-picker
                v-model="activity.time"
                format="24hr"
                @change="pickers.time = false"
                scrollable
              >
              </v-time-picker>
            </v-menu>
          </v-layout>
          <v-combobox
            v-model.trim="activity.place"
            :rules="[v => !!v || '']"
            :items="['天健', '休闲', '医学院']"
            no-data-text
            prepend-icon="place"
            label="地点"
          ></v-combobox>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    props: {
      dialog: {
        type: Boolean,
        required: true
      }
    },
    data: () => ({
      valid: false,
      activity: {
        title: null,
        description: null,
        category: null,
        date: null,
        time: null,
        place: null,
        images: []
      },
      pickers: {
        date: false,
        time: false
      }
    }),
    watch: {
      dialog (v) {
        this.$refs.form.reset()
        this.activity.date = new Date().toLocaleDateString().replace(/\//g, '-')
        this.activity.time = new Date().toTimeString().slice(0, 5)
        this.activity.images = []

        this.$store.commit('title', v ? '发布活动' : this.$route.meta.title)
      }
    },
    methods: {
      publish () {
        (async () => {
          const res = await this.$ajax.post(this.$store.state.api.activity, {
            headers: { Authorization: localStorage.token },
            json: this.activity
          }).json()

          if (res.success) {
            this.$emit('close')
            this.$router.push({ name: 'activity', params: { id: res.id }})
            this.$store.commit('message', { type: 'done', text: '发布成功' })
          } else this.$store.commit('message', { type: 'error', text: res.message })
        })()
      }
    }
  }
</script>
