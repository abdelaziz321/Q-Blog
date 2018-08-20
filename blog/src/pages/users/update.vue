<template>
	<q-page class="overflow-hidden">
		<div class="row update_user">
			<div class="q-mt-xl q-py-lg container bg-white">
				<form class="q-px-lg">
					<h2 style="font-size:1.7em" class="q-my-lg center text-secondary">Update your profile</h2>

					<p class="text-primary">Email: {{ email }}</p>

					<q-input inverted type="text" color="primary" float-label="Your username" v-model="username" />

					<q-input inverted type="password" class="q-my-md" color="primary" float-label="your password" v-model="password" />

					<q-input inverted type="password" class="q-my-md" color="primary" float-label="rewrite password" v-model="confirm_password" />

					<div class="q-my-md bg-primary text-white q-pa-sm">
						<input type="file" ref="avatar" @change="handleAvatarUpload()" />
					</div>

					<q-input type="textarea" inverted class="q-my-md" color="primary" stack-label="About" rows="3" v-model="about" />

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
			username: this.$auth.user().username,
			about: this.$auth.user().about,
			email: this.$auth.user().email,
			password: '',
			confirm_password: '',
			avatar: {}
		}
	},


	methods: {
		handleAvatarUpload() {
      this.avatar = this.$refs.avatar.files[0];
    },


		update: function () {
			if (this.password !== this.confirm_password) {
				Notify.create({
					message: 'passwords missmatch',
					icon: 'error',
					position: 'top'
				});

				return;
			}

			let formData = new FormData();
      formData.append('_method', 'PUT');
      formData.append('username', this.username);
      formData.append('password', this.password);
      formData.append('description', this.about);
      formData.append('avatar', this.avatar);

			this.axios.post('/users/' + this.$auth.user().slug, formData, {
				headers: {
          'Content-Type': 'multipart/form-data'
        }
			})
			.then((response) => {
				Notify.create({
					message: 'you have been updated your profile successfully!',
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
			})
		}
	}
}
</script>
