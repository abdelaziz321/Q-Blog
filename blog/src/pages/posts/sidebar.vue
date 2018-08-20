<template>
	<div class="sidebar">
		<div>
			<h6 class="q-ma-none q-mb-md q-pl-sm text-secondary">Categories:</h6>
			<ul class="categories q-mt-sm q-mb-xl">
				<li v-for="category in categories"
						class="q-py-sm"
				>
					<router-link :to="'/posts?category=' + category.slug">
						{{ category.title }}
						<q-chip class="float-right" dense color="tertiary">{{ category.total_posts }}</q-chip>
					</router-link>
				</li>
			</ul>
		</div>

		<div>
			<h6 class="q-ma-none q-mb-md q-pl-sm text-secondary">Tags:</h6>
			<ul class="tags q-mt-sm q-mb-xl">
				<li v-for="tag in tags"
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

		<div>
			<h6 class="q-ma-none q-mb-md q-pl-sm text-secondary">Authors:</h6>
			<ul class="authors q-mt-sm q-mb-xl">
				<li v-for="author in authors"
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


	data: function () {
		return {
			tags: [],
			authors: [],
			categories: []
		};
	},


	created: function () {
		this.axios.get('/sidebar')
		.then((response) => {
			this.tags = response.data.tags;
			this.authors = response.data.authors;
			this.categories = response.data.categories;
		})
		.catch((error) => {
			console.log(error.response);
		});
	}
}
</script>
