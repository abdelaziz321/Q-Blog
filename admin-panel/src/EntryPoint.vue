<template>
  <div>
    <div v-if="$auth.ready()">
      <router-view></router-view>
    </div>

    <div v-if="!$auth.ready()" class="loading">
      <font-awesome-icon icon="spinner" spin class="mr-3" /> loading...
    </div>
  </div>
</template>
