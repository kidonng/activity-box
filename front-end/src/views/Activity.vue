<template>
  <v-card>
    <v-responsive>
      <v-img v-if="activity.images[0]" :src="activity.images[0]"></v-img>
    </v-responsive>
    <v-layout justify-center>
      <v-card-title class="headline" primary-title>{{ activity.title }}</v-card-title>
    </v-layout>
    <v-card-text>
      <div class="subheading">{{ activity.description }}</div>
      <v-list>
        <v-list-tile avatar>
          <v-list-tile-avatar>
            <v-icon>{{ verified ? 'verified_user' : 'person' }}</v-icon>
          </v-list-tile-avatar>

          <v-list-tile-content>
            <router-link :to="{ name: 'user', params: { name: activity.username || ' ' }}">{{ activity.username }}</router-link>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile avatar>
          <v-list-tile-avatar>
            <v-icon>label</v-icon>
          </v-list-tile-avatar>

          <v-list-tile-content>{{ activity.category }}</v-list-tile-content>
        </v-list-tile>

        <v-list-tile avatar>
          <v-list-tile-avatar>
            <v-icon>calendar_today</v-icon>
          </v-list-tile-avatar>

          <v-list-tile-content>{{ activity.time }}</v-list-tile-content>
        </v-list-tile>

        <v-list-tile avatar>
          <v-list-tile-avatar>
            <v-icon>place</v-icon>
          </v-list-tile-avatar>

          <v-list-tile-content>{{ activity.place }}</v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  data () {
    return {
      activity: {
        id: this.$route.params.id,
        images: []
      },
      verified: false
    }
  },
  mounted () {
    this.$ajax.get(this.$store.state.api.activity, { params: { id: this.activity.id }})
      .then(resp => {
        if (resp.data.success) {
          this.activity = resp.data
          this.$ajax.get(this.$store.state.api.user, { params: { name: resp.data.username }})
            .then(resp => {
              if (resp.data.success) this.verified = resp.data.verified
              else this.$store.commit('message', { type: 'error', message: resp.data.message })
            })
            .catch(() => this.$store.commit('message', { type: 'error', message: '获取发布者信息失败' }))
        } else this.$store.commit('message', { type: 'error', message: resp.data.message })
      })
      .catch(() => this.$store.commit('message', { type: 'error', message: '获取活动详情失败' }))
  }
}
</script>

<style lang="stylus">
a
  text-decoration none
</style>
