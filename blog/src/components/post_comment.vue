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
					:text-color="comment.voted == 1 ? 'positive' : 'tertiary'"
					flat
					size="0.8rem"
					dense
					label="voteup"
					no-caps
					icon-right="arrow_drop_up"
					@click="voting('up')"
				/>
				<q-chip dense text-color="tertiary">
					{{ comment.votes }}
				</q-chip>
			  <q-btn
					:text-color="comment.voted == -1 ? 'negative' : 'tertiary'"
					flat
					size="0.8rem"
					dense
					label="votedown"
					no-caps
					icon="arrow_drop_down"
					@click="voting('down')"
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
		deleteComment() {
			let vm = this;

			Notify.create({
			  message: 'Are you sure you want to delete this comment?',
				position: 'bottom',
				actions: [
					{
					  label: 'yes',
					  handler: () => {
						  vm.deleteCommentHandler();
					  }
					}
			  ],
		  });
	  },

	  deleteCommentHandler() {
			this.$store.dispatch('comments/deleteComment', this.comment)
			.then(() => {
				Notify.create({
					message: 'your comment has beed deleted successfully!',
					type: 'positive',
					color: 'positive',
					icon: 'error',
					position: 'top'
				});
			})
			.catch((message) => {
				Notify.create({
					message: message,
					icon: 'error',
					position: 'top'
				});
			});			
		},

		voting(vote) {
			let action = vote;
			if(this.comment.voted === 1 && vote === 'up'
			|| this.comment.voted === -1 && vote === 'down') {
				action = 'del';
			}

			let payload = {
				action: action,		// now we have action up|down|del
				commentId: this.comment.id
			}

			this.$store.dispatch('comments/voting', payload)
			.then(() => {
				Notify.create({
					message: `the comment has been updated successfully`,
					type: 'positive',
					color: 'positive',
					icon: 'error',
					position: 'top'
				});
			});
		}
	}
}
</script>
