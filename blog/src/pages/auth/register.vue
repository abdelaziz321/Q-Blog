<template>
	<div class="row register_pages justify-center items-center">
    <form class="bg-white text-primary q-px-md q-pt-lg q-pb-md">
      <h2 class="q-my-none text-center text-secondary">Register</h2>

      <q-input type="text" color="primary" float-label="Your username" v-model="username" />

			<q-input type="email"  class="q-mt-ms" color="primary" float-label="your email" v-model="email" />

      <q-input type="password" class="q-mt-ms" color="primary" float-label="your password" v-model="password" />

      <q-input type="password" class="q-mt-ms" color="primary" float-label="rewrite password" v-model="confirm_password" />

			<q-btn
				label="sign up"
				class="q-mt-md float-right"
				color="primary"
				@click="register"
			/>
    </form>
  </div>
</template>

<script>
import { Notify } from 'quasar';

export default {
  data: function () {
    return {
      username: '',
      email: '',
      password: '',
      confirm_password: '',
    }
  },


	mounted: function () {
		// REVIEW: what i am doing here is ridiculous..
		// there is no option for autocomplete=false
		// may be there is another way to achieve this.
		window.setTimeout(() => {
			this.username = '';
			this.email = '';
			this.password = '';
			this.confirm_password = '';
		}, 200);
	},


  methods: {
		register() {
			if (this.password !== this.confirm_password) {
				Notify.create({
					message: 'passwords missmatch',
					icon: 'error',
					position: 'top'
				});
				return;
			}

      let redirect = this.$auth.redirect();
      this.$auth.register({
        url: '/auth/register',
        redirect: {path: redirect ? redirect.from.path : '/posts'},
        data: {
					username: this.username,
          email: this.email,
          password: this.password,
          password_confirmation: this.confirm_password
        },
        success: (response) => {
          this.$auth.user(response.data.user);
          this.$auth.token('access_token', response.data.token);

					Notify.create({
					  message: 'you have been registered successfully!',
						type: 'positive',
  					color: 'positive',
						icon: 'error',
						position: 'top'
					});
        },
        error: (error) => {
					let res = error.response;

					Notify.create({
					  message: res.data.message,
						icon: 'error',
						position: 'top'
					});
        },
        fetchUser: false
      });
    }
  }
}
</script>
