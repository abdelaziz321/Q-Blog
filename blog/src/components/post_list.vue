<template>
	<div class="blog_list overflow-hidden">
		<!-- search posts form -->
		<search-form @search="search"></search-form>

		<!-- posts list -->
		<div class="posts gutter-sm row">
			<card v-for="post in posts" :post="post" :key="post.id" />
			<div
				v-if="posts.length === 0"
				class="text-primary text-weight-bold col text-center"
			>
				there is no result for your search!
			</div>
		</div>

		<!-- pagination -->
		<q-pagination
			v-model="page"
			:max="totalPosts"
			@input="setPage"
			:max-pages="6"
			ellipses
			color="primary"
			class="q-mb-xl q-mt-lg float-right"
			boundary-links
		/>
	</div>
</template>

<script>
import Card from './post_card';
import SearchForm from './posts_search';

export default {
	components: {
		SearchForm,
		Card
	},


	data: function () {
		return {
			page: 0,
			queryString: ''
		};
	},


	computed: {
		posts: function() {
			return this.$store.getters.posts;
		},
		totalPosts: function() {
			return this.$store.getters.totalPosts;
		}
	},


	created: function () {
		this.queryString = this.getQueryString();
		this.page = this.getPageQuery();
		this.getPosts();
	},


	methods: {
		setPage: function (page) {
			this.queryString = this.getQueryString();
			this.page = page;
			this.getPosts();
		},

		search(query) {
			this.queryString = query;
			this.page = 1;
			this.getPosts();
		},

		getPosts: function () {
			let query = this.queryString;
			if (query.charAt(query.length - 1) !== '&' && query.length !== 0) {
				query += '&';
			}
			query += ('page=' + this.page);

			this.$store.dispatch('getPosts', query)
			.then(() => {
				this.$router.push('posts?' + query);
			});
		},

		getPageQuery() {
			let href = window.location.href;

			let pageQuery = /page=[0-9]*/.exec(href);

			let page = 1;
			if (pageQuery !== null) {
				page = parseInt(pageQuery[0].slice(5, pageQuery[0].length));
			}

			return isNaN(page) ? 1 : page;
		},

		getQueryString() {
			let href = window.location.href;
			let indx = href.indexOf('?');

			if (indx === -1) {
				return '';
			}

			let queryString = href.slice(indx + 1);
			return queryString.replace(/&?page=[0-9]*/g, '');
		}
	}
}
</script>
