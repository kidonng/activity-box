<template>
  <v-snackbar
    v-model="snackbar"
    :color="$store.state.snackbar.type"
    :top="true"
    :right="true"
    :timeout="2000"
  >
    <v-icon color="white">{{ $store.state.snackbar.type }}</v-icon>
    {{ $store.state.snackbar.message }}
    <v-btn icon @click="snackbar = false">
      <v-icon>close</v-icon>
    </v-btn>
  </v-snackbar>
</template>

<script>
export default {
  computed: {
    snackbar: {
      get () { return this.$store.state.snackbar.show },
      set (v) { this.$store.commit('message', { show: v })}
    }
  }
}
</script>
