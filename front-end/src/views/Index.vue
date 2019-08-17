<template>
  <v-layout class="mt-2" column>
    <v-tooltip v-if="$store.state.login && !dialog" top>
      <v-btn
        slot="activator"
        fixed bottom right
        fab
        color="primary"
        @click="dialog = true">
        <v-icon>add</v-icon>
      </v-btn>
      <span>发布活动</span>
    </v-tooltip>
    <create :dialog="dialog" @close="dialog = false"></create>

    <v-card v-for="(activity, i) in activities" :key="i" class="mb-3">
      <v-layout>
        <v-img v-if="activity.images[0]" :src="activity.images[0]"></v-img>
        <v-card-title primary-title>
          <div>
            <div class="headline">
              <router-link :to="{ name: 'activity', params: { id: activity.id }}">{{ activity.title }}</router-link>
            </div>
            <div><v-icon class="mr-1">person</v-icon>{{ activity.username }}</div>
            <div><v-icon class="mr-1">calendar_today</v-icon>{{ activity.time }}</div>
            <div><v-icon class="mr-1">place</v-icon>{{ activity.place }}</div>
          </div>
        </v-card-title>
      </v-layout>
    </v-card>
  </v-layout>
</template>

<script>
import Create from '../components/Create'

export default {
  components: { Create },
  data () {
    return {
      dialog: false,
      activities: []
    }
  },
  mounted () {
    this.$ajax.get(this.$store.state.api.activity)
      .then(resp => resp.data !== []
        ? this.activities = resp.data
        : this.$store.commit('message', { type: 'error', message: resp.data.message }))
      .catch(() => this.$store.commit('message', { type: 'error', message: '获取活动失败' }))
  }
}
</script>
