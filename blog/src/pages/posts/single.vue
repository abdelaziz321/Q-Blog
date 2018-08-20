<template>
	<div class="single_post overflow-hidden q-mb-xl">

		<div class="post_caption blog-detail">
			<img :src="caption" alt="post caption">
		</div>

		<div class="description q-mr-xl q-mb-xl q-pt-lg text-grey-7 text-weight-light">
			<!-- meta -->
			<p class="meta no-margin">
				<!-- category -->
				<router-link
				  v-if="typeof post.category !== 'undefined'"
					class="text-secondary text-weight-regular"
					:to="'/posts?category=' + post.category.slug">
					{{ post.category.title }}
				</router-link>
				<!--  published at -->
				<time class="q-ml-md">{{ post.published_at }}</time>
				<span class="q-ml-md">
					By
					<router-link
						v-if="typeof post.author !== 'undefined'"
						class="text-secondary text-weight-regular"
						:to="'/posts?author=' + post.author.slug">
						{{ post.author.name }}
					</router-link>
				</span>
			</p>

			<h2 class="q-mt-md q-mb-sm text-secondary text-weight-bold">{{ post.title }}</h2>

			<!-- tags -->
			<ul
				v-if="typeof post.tags !== 'undefined' && post.tags.length !== 0"
				class="tags q-mt-none text-weight-regular"
			>
				<li class="q-py-sm">
					<router-link
						v-for="tag in post.tags"
						:key="tag.slug"
						:to="'/posts?tags[]=' + tag.slug"
					>
						<q-chip class="q-mr-sm" tag small color="primary">
							{{ tag.name }}
						</q-chip>
					</router-link>
				</li>
			</ul>


			<p class="q-mt-lg" v-html="post.body"></p>

			<div class="text-weight-regular">
					{{ post.total_recommendations }}
					<span v-if="$auth.check()">
						<q-btn
							v-if="post.recommended == 0"
							flat
							dense
							no-caps
							icon="favorite"
							label="Recommend"
							text-color="positive"
							@click="recommendPost(true)"
						></q-btn>

						<q-btn
							v-else
							flat
							dense
							no-caps
							icon="favorite"
							label="Unrecommend"
							text-color="negative"
							@click="recommendPost(false)"
						></q-btn>
					</span>

					<span v-else class="text-primary q-ml-md">
						<router-link class="text-secondary text-weight-medium" to="/login">
							Login
						</router-link>
						 first to recommend the post.
					</span>

					<span class="float-right">
						<q-icon class="on-right" name="visibility" />
						{{ post.views }} views
					</span>
			</div>
		</div>

		<hr style="color: rgba(0,0,0,0.1)" />


		<!-- Comments Section -->
		<div class="comments">
			<h2 class="q-mt-lg q-mb-lg text-secondary text-weight-bold">{{ comments.length }} Comments</h2>
			<comment
				v-for="comment in comments"
				:comment="comment"
				:key="comment.id"
				@deleteComment="deleteComment"
			/>
		</div>

		<div v-if="$auth.check()" class="comment_form">
			<h3 class="q-mt-lg q-mb-sm text-secondary text-weight-medium">Say something:</h3>
			<form>
				<q-input
				  v-model="commentInput"
					type="textarea"
					placeholder="Your time to talk  ;)"
					rows="3"
					inverted
					dark
				/>
				<q-btn
					label="Submit"
					color="primary"
					class="float-right q-mt-md"
					no-caps
					@click="makeComment"
				/>
			</form>
		</div>

		<div v-else class="text-center text-primary">
			<hr style="color: rgba(0,0,0,0.1)" />
			<router-link class="text-secondary text-weight-medium" to="/login">
				Login
			</router-link>
			 first to make a comment.
		</div>
	</div>
</template>

<script>
import Comment from './comment';
import { Notify } from 'quasar';

export default {
	components: {
		Comment
	},


	data: function () {
		return {
			post: {},
			comments: [],
			commentInput: ''
		};
	},


	computed: {
		caption: function () {
			let postsPath = this.$baseURL + '/storage/posts/';
			if (this.post.caption == null) {
				return postsPath + 'default.jpg';
			} else {
				return postsPath + this.post.caption;
			}
		}
	},


	created: function () {
		let post = this.$route.params.post;
		this.axios.get('/posts/' + post)
		.then((response) => {
			this.post = response.data.post;
			this.comments = response.data.comments;
		})
		.catch((errors) => {
			console.log(errors);
		})
	},


	methods: {
		makeComment() {
			this.axios.post('/comments', {
				body: this.commentInput,
				post_id: this.post.id,
				user_id: this.$auth.user().id
			})
			.then((response) => {
				this.comments.push(response.data.comment);

				Notify.create({
					message: 'your comment has been added successfully',
					type: 'positive',
					color: 'positive',
					icon: 'error',
					position: 'top'
				});

				this.commentInput = '';
			})
			.catch((error) => {
				console.log(error.response);
			});
		},

		// these 2 methods will be fired by the comment component.
		deleteComment(comment) {
			let index = this.comments.indexOf(comment);
			if (index > -1) {
				this.comments.splice(index, 1);
			}
		},

		updateComment(comment) {
			let commentIndex = this.comments.findIndex((item) => {
	      return item.id == comment.id;
	    });

	    Vue.set(this.comments, commentIndex, comment);
		},

		recommendPost(recommend) {
			let action = recommend ? 'recommend' : 'unrecommend';
			this.axios.post('posts/' + action + '/' + this.post.slug)
			.then((response) => {
				Notify.create({
					message: `the post has been ${action}ed successfully`,
					type: 'positive',
					color: 'positive',
					icon: 'error',
					position: 'top'
				});

				this.post.recommended = !this.post.recommended;
				this.post.total_recommendations += (this.post.recommended) ? 1 : -1;
			})
			.catch((error) => {
				console.log(error.response);
			});
		}
	}
}
</script>
