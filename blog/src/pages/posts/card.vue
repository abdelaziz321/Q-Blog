<template>
	<div class="col-lg-6 ">
		<div class="bg-white post q-mx-xs q-mb-sm">
			<div class="post_caption">
				<time>{{ post.published_at }}</time>
				<img :src="caption" alt="post caption">
			</div>

			<div class="post_header q-px-md q-py-sm">
				<h5 class="q-mt-none q-mb-sm q-pb-sm">
					<router-link class="text-secondary text-weight-medium" :to="'/posts/' + post.slug">
						{{ post.title }}
					</router-link>
				</h5>
				<span class="author text-primary" v-if="typeof post.author.name !== 'undefined'">
					By:
					<router-link class="text-weight-medium text-primary" :to="'/posts?author=' + post.author.slug">
						{{ post.author.name }}
					</router-link>
				</span>
				<span class="author text-primary float-right" v-if="typeof post.category.title !== 'undefined'">
					In:
					<router-link class="text-weight-medium text-primary" :to="'/posts?category=' + post.category.slug">
						{{ post.category.title }}
					</router-link>
				</span>
				<div style="clear: both"></div>
			</div>

			<div class="post_footer text-primary text-center q-px-md q-py-sm">
				<span class="recommendations float-left">
					<q-icon name="favorite" size="1.1rem" color="tertiary" />
					{{ post.total_recommendations }}
				</span>
				<span class="views">
					<q-icon name="visibility" size="1.1rem" color="tertiary" />
					{{ post.views }}
				</span>
				<span class="comments float-right">
					<q-icon name="comment" size="1.1rem" color="tertiary" />
					{{ post.total_comments }}
				</span>
				<div style="clear: both"></div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		name: 'card',


		props: [
			'post'
		],


		computed: {
			caption: function () {
				let postsPath = this.$baseURL + '/storage/posts/';
				if (this.post.caption == null) {
					return postsPath + 'default.jpg';
				} else {
					return postsPath + this.post.caption;
				}
			}
		}
	}
</script>
