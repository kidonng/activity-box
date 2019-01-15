<template>
  <v-snackbar
    v-model="message.show"
    :color="message.type"
    :top="true"
    :right="true"
    :timeout="2000"
  >
    <v-icon color="white">{{ message.type }}</v-icon>
    {{ message.text }}
    <v-btn icon @click="message = false">
      <v-icon>close</v-icon>
    </v-btn>
  </v-snackbar>
</template>

<script>
  export default {
    computed: {
      message: {
        get () { return this.$store.state.message },
        set (v) { this.$store.commit('message', { show: v })}
      }
    }
  }
</script>
