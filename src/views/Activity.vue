<template>
  <v-layout column>
    <v-img v-if="activity.images" :src="activity.images[0]" alt="活动图片"></v-img>
    <div class="text-xs-center headline my-3">{{ activity.title }}</div>
    <div class="subheading mx-2">{{ activity.description }}</div>
    <v-list>
      <v-list-tile>
        <v-list-tile-avatar>
          <v-icon :color="verified ? 'teal' : ''">{{ verified ? 'verified_user' : 'person' }}</v-icon>
        </v-list-tile-avatar>

        <v-list-tile-content>
          <router-link v-if="activity.username" :to="{ name: 'user', params: { name: activity.username }}">{{ activity.username }}</router-link>
        </v-list-tile-content>
      </v-list-tile>

      <v-list-tile v-for="(item, i) in items" :key="i">
        <v-list-tile-avatar>
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-tile-avatar>

        <v-list-tile-content>{{ item.text }}</v-list-tile-content>
      </v-list-tile>
    </v-list>
  </v-layout>
</template>

<script>
  export default {
    data: () => ({
      activity: {},
      verified: false
    }),
    computed: {
      items () {
        return [
          { icon: 'label', text: this.activity.category },
          { icon: 'calendar_today', text: this.activity.time },
          { icon: 'place', text: this.activity.place }
        ]
      }
    },
    created () {
      (async () => {
        const activity = await this.$ajax.get(this.$store.state.api.activity, {
          searchParams: { id: this.$route.params.id }
        }).json()

        if (activity.success) {
          this.activity = activity
          this.$store.commit('title', activity.category)

          const poster = await this.$ajax.get(this.$store.state.api.user, {
            searchParams: { name: activity.username }
          }).json()

          poster.success
            ? this.verified = poster.verified
            : this.$store.commit('message', { type: 'error', text: poster.message })
        }
        else this.$store.commit('message', { type: 'error', text: activity.message })
      })()
    }
  }
</script>

<style lang="stylus" scoped>
  a
    text-decoration none

  .v-list
    background none
</style>
