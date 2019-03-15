<template>
  <v-snackbar
    v-model="$store.state.message.show"
    :color="$store.state.message.type"
    :top="true"
    :timeout="3000"
    app
  >
    <v-icon color="white">{{ $store.state.message.type }}</v-icon>
    {{ $store.state.message.text }}
    <v-btn @click="$store.commit('message', { show: false })" icon>
      <v-icon>close</v-icon>
    </v-btn>
  </v-snackbar>
</template>

<style lang="stylus">
.v-snack__content .v-btn
  margin 0 !important
</style>
