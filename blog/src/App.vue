<template>
  <div id="q-app">
		<div v-if="$auth.ready()">
    	<router-view />
		</div>

		<div
			v-if="!$auth.ready()"
			class="row justify-center items-center text-primary text-weight-medium"
			style="height: 100vh"
		>
				<q-spinner-bars color="primary" :size="70" class="on-left" /> Loading...
		</div>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
</style>
