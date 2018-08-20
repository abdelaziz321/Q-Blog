<template>
	<div class="comment row q-mb-lg">
		<img class="col-2 self-start q-mt-sm" :src="avatar" alt="user avatar" />
		<div class="desc col q-pl-lg text-grey-8">
			<h4 class="no-margin text-primary text-weight-regular">
				<span
					v-if="typeof comment.user !== 'undefined'"
					class="text-weight-medium"
				>
					{{ comment.user.name }}
				</span>
				<time class="q-ml-lg">{{ comment.created_at }}</time>
			</h4>
			<p class="text-weight-light q-mb-sm">{{ comment.body }}</p>
			<p class="votes">
			  <q-btn
					:disable="comment.voted == 1"
					:text-color="comment.voted == 1 ? 'positive' : 'tertiary'"
					flat
					size="0.8rem"
					dense
					label="voteup"
					no-caps
					icon-right="arrow_drop_up"
					@click="vote(true)"
				/>
				<q-chip dense text-color="tertiary">
					{{ comment.votes }}
				</q-chip>
			  <q-btn
					:disable="comment.voted == -1"
					:text-color="comment.voted == -1 ? 'negative' : 'tertiary'"
					flat
					size="0.8rem"
					dense
					label="votedown"
					no-caps
					icon="arrow_drop_down"
					@click="vote(false)"
				 />

				<q-btn-group v-if="$auth.user().slug === comment.user.slug" flat class="q-ml-lg">
					<q-btn text-color="tertiary" dense icon="delete" @click="deleteComment"/>
					<!-- TODO : <q-btn class="q-ml-md" text-color="tertiary" dense icon="edit" @click="editComment"/> -->
				</q-btn-group>
			</p>
		</div>
	</div>
</template>

<script>
import { Notify } from 'quasar'

export default {
	props: [
		'comment'
	],


	computed: {
		avatar: function () {
			let usersPath = this.$baseURL + '/storage/users/';
			if (this.comment.user.avatar == null) {
				return usersPath + 'avatar.svg';
			} else {
				return usersPath + this.comment.user.avatar;
			}
		}
	},


	methods: {
		editComment() {
			// TODO:
		},

		deleteComment() {
			let app = this;

			Notify.create({
			  message: 'Are you sure you want to delete this comment?',
				position: 'bottom',
				actions: [
					{
					  label: 'yes',
					  handler: () => {
						  app.deleteCommentHandler();
					  }
					}
			  ],
		  });
	  },

	  deleteCommentHandler() {
			this.axios.post('comments/' + this.comment.id, {
				'_method': 'DELETE'
			})
			.then((response) => {
				this.$emit('deleteComment', this.comment);

				Notify.create({
					message: 'your comment has beed delete successfully!',
					type: 'positive',
					color: 'positive',
					icon: 'error',
					position: 'top'
				});
			})
			.catch((error) => {
				Notify.create({
					message: error.response.data.message,
					icon: 'error',
					position: 'top'
				});
			});
		},

		vote(vote) {
			let action = vote ? 'vote' : 'unvote';
			this.axios.post('comments/' + action + '/' + this.comment.id)
			.then((response) => {
				Notify.create({
					message: `the comment has been ${action}ed successfully`,
					type: 'positive',
					color: 'positive',
					icon: 'error',
					position: 'top'
				});

				// reset the votes so the user doesn't vote up or down
				if (this.comment.voted == -1) {
					this.comment.votes = parseInt(this.comment.votes) + 1;
				} else if (this.comment.voted == 1) {
					this.comment.votes = parseInt(this.comment.votes) - 1;
				}

				// set the user vote => up or down
				if (action === 'vote') {
					this.comment.voted = 1;
				} else {
					this.comment.voted = -1;
				}

				// update the total votes
				this.comment.votes = parseInt(this.comment.votes) + parseInt(this.comment.voted);
			})
			.catch((error) => {
				console.log(error.response);
			});;
		}
	}
}
</script>
