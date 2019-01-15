<template>
  <v-layout column>
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
    <Post :dialog="dialog" @close="dialog = false"></Post>

    <v-flex v-for="(activity, i) in activities" :key="i" class="mb-3" xs12>
      <v-card>
        <v-layout>
          <v-flex xs5>
            <v-img :src="activity.images[0]"></v-img>
          </v-flex>
          <v-flex xs7>
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
          </v-flex>
        </v-layout>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
  import Post from '../components/Post'

  export default {
    components: { Post },
    data: () => ({
      dialog: false,
      activities: []
    }),
    created () {
      (async () => {
        const res = await this.$ajax.get(this.$store.state.api.activity).json()

        res === []
          ? this.$store.commit('message', { type: 'info', text: '暂无活动' })
          : this.activities = res
      })()
    }
  }
</script>
