<template>
	<div class="sidebar q-mt-md">
		<!-- =========== categories =========== -->
		<div>
			<h6 class="q-ma-none q-mb-md q-pl-sm text-secondary">Categories:</h6>
			<ul class="categories q-mt-sm q-mb-xl">
				<li 
					v-for="category in categories"
					:key="category.slug"
					class="q-py-sm"
				>
					<router-link :to="'/posts?category=' + category.slug">
						{{ category.title }}
						<q-chip class="float-right" dense color="tertiary">{{ category.total_posts }}</q-chip>
					</router-link>
				</li>
			</ul>
		</div>

		<!-- =========== tags =========== -->
		<div>
			<h6 class="q-ma-none q-mb-md q-pl-sm text-secondary">Tags:</h6>
			<ul class="tags q-mt-sm q-mb-xl">
				<li 
					v-for="tag in tags"
					:key="tag.slug"
					class="q-py-sm"
				>
					<router-link :to="'/posts?tags[]=' + tag.slug">
						<q-chip class="q-mr-sm" tag small color="primary">
							{{ tag.name }}
						</q-chip>
					</router-link>
				</li>
			</ul>
		</div>

		<!-- =========== authors =========== -->
		<div>
			<h6 class="q-ma-none q-mb-md q-pl-sm text-secondary">Authors:</h6>
			<ul class="authors q-mt-sm q-mb-xl">
				<li 
					v-for="author in authors"
					:key="author.slug"
					class="q-py-sm"
				>
					<router-link :to="'/posts?author=' + author.slug">
						{{ author.username }}
						<q-chip class="float-right" dense color="tertiary">{{ author.total_posts }}</q-chip>
					</router-link>
				</li>
			</ul>
		</div>
	</div>
</template>

<script>
export default {
	name: 'side-bar',


	computed: {
		tags: function () {
			return this.$store.getters.popularTags;
		},
		authors: function () {
			return this.$store.getters.popularAuthors;
		},
		categories: function () {
			return this.$store.getters.popularCategories;
		}
  },


	created: function () {
		this.$store.dispatch('sidebar');
	}
}
</script>
