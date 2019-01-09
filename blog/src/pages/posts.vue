<template>
  <q-page class="overflow-hidden">

		<div class="header">
			<div class="container">
				<div>
					<h2 class="no-margin text-primary text-bold">Latest Posts</h2>
				</div>
			</div>
		</div>

		<div class="container overflow-hidden q-mb-xl">
			<div class="row q-mt-lg gutter-lg">
				<div class="col-lg-8">
					<!-- single-post|postslist -->
					<router-view :key="Date.now()"></router-view>
				</div>

				<div class="col-lg-4">
					<side-bar></side-bar>
				</div>
			</div>
		</div>
  </q-page>
</template>

<script>
import SideBar from './partials/sidebar';

export default {
	name: 'posts',

	components: {
		SideBar
	}
}
</script>