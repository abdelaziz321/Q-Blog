<template>
  <div class="row register_pages justify-center items-center">
    <form class="bg-white text-primary q-px-md q-pt-lg q-pb-md">
      <h2 class="q-my-none text-center text-secondary">Login</h2>

      <q-input type="email" color="primary" float-label="your email" v-model="email" />
      <q-input type="password" class="q-mt-ms" color="primary" float-label="your password" v-model="password" />

    	<q-checkbox
				v-model="remember"
				label="Remember me"
				color="teal"
				class="q-mt-lg checkbox"
				size="small"
			/>

			<q-btn
				label="Sign in"
				class="q-mt-md float-right"
				color="primary"
				@click="login"
			/>

			<div class="q-my-md q-py-xs" style="clear:both"></div>

      <hr style="color: rgba(0,0,0,0.1)" />

      <div class="password_reset text-center text-primary q-my-sm">
        Forgot your password?
        <router-link  class="text-weight-medium text-secondary" to="/register">
          Click here
        </router-link>
      </div>
    </form>
  </div>
</template>

<script>
import { Notify } from 'quasar';

export default {
  data: function () {
    return {
      email: '',
      password: '',
      remember: false
    }
  },


  methods: {
    login() {
      let redirect = this.$auth.redirect();
      this.$auth.login({
        url: '/auth/login',
        redirect: {path: redirect ? redirect.from.path : '/posts'},
        data: {
          email: this.email,
          password: this.password
        },
        rememberMe: this.remember,
        success: (response) => {
          this.$auth.user(response.data.user);
          this.$auth.token('access_token', response.data.token);

					Notify.create({
					  message: 'you have been logged in successfully!',
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
