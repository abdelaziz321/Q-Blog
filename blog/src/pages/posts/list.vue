<template>
	<div class="blog_list overflow-hidden">

		<search-form @search="search"></search-form>

		<div class="posts gutter-sm row">
			<card v-for="post in posts" :post="post" :key="post.id" />

			<div
				v-if="posts.length === 0"
				class="text-primary text-weight-bold col text-center"
			>
				there is no result for your search!
			</div>
		</div>

		<q-pagination
			v-model="page"
			color="primary"
			class="q-mb-xl q-mt-lg float-right"
			boundary-links
			:max="max"
			:max-pages="6"
			ellipses
			@input="setPage"
		/>
	</div>
</template>
<script>
import Card from './card';
import SearchForm from './search_form';

export default {
	components: {
		SearchForm,
		Card
	},


	data: function () {
		return {
			page: 0,
			max: 0,
			posts: [],

			queryString: ''
		};
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

			this.axios.get('/posts?' + query)
			.then((response) => {
				let res = response.data;
				this.posts = res.data;
				this.max = Math.ceil(res.total / res.per_page);

				this.$router.push('posts?' + query);
			})
			.catch((error) => {
				console.log(error);
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
