<template>
  <div class="login_page">
    <h2 class="text-center">Q-Blog <span>Admin Panel</span></h2>
    <form class="form-signin">
      <h4 class="h4 mb-4 text-center">Login</h4>

      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="email" v-model="email" class="form-control mb-3" placeholder="Email address" required autofocus>

      <label class="sr-only">Password</label>
      <input type="password" v-model="password" class="form-control mb-3" placeholder="Password" required>

      <div class="checkbox mb-2">
        <label style="font-size:0.9em">
          <input type="checkbox" style="vertical-align: text-top" v-model="remember"> Remember me
        </label>
      </div>

      <button class="btn btn-primary btn-lg btn-block" type="submit" @click.prevent="login">Sign in</button>

      <hr />

      <div style="font-size:0.9em" class="text-center mt-1 text-secondary">
        Forgot your password?
        <router-link  class="" to="/register">
          Click here
        </router-link>
      </div>
    </form>

  </div>
</template>

<script>
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
        redirect: {path: redirect ? redirect.from.path : '/dashboard'},
        data: {
          email: this.email,
          password: this.password
        },
        rememberMe: this.remember,
        success: (response) => {
          this.$auth.user(response.data.user);
          this.$auth.token('access_token', response.data.token);
        },
        error: (error) => {
          console.log(error);
          console.log(error.data);
        },
        fetchUser: false
      });
    },
  }
}
</script>
