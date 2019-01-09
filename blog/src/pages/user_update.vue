<template>
	<q-page class="overflow-hidden">
		<div class="row update_user">
			<div class="q-mt-xl q-py-lg container bg-white">
				<form class="q-px-lg">
					<h2 style="font-size:1.7em" class="q-my-lg center text-secondary">Update your profile</h2>

					<q-input inverted type="text" color="primary" float-label="Your username" v-model="userForm.username" />
					<div class="q-my-md bg-primary text-white q-pa-sm">
						<input type="file" ref="avatar" @change="handleAvatarUpload()" />
					</div>

					<q-input type="textarea" inverted class="q-my-md" color="primary" stack-label="About" rows="3" v-model="userForm.about" />

					<q-btn
					label="update"
					class="q-mt-md float-right"
					color="primary"
					@click="update"
					/>
				</form>
			</div>
		</div>
  </q-page>

</template>

<script>
import { Notify } from 'quasar';

export default {
	data: function () {
		return {
			userForm: {
				username: this.$auth.user().username,
				about: this.$auth.user().about,
				avatar: {}
			}
		}
	},


	methods: {
		handleAvatarUpload() {
      this.userForm.avatar = this.$refs.avatar.files[0];
    },


		update: function () {
			this.userForm.slug = this.$auth.user().slug;

			this.$store.dispatch('updateUser', this.userForm)
			.then(() => {
				Notify.create({
					message: 'your profile has been updated successfully!',
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
		}
	}
}

			
</script>
