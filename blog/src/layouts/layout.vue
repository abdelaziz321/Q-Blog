<template>
  <q-layout view="hhh lpr fff">
    <q-layout-header class="bg-primary q-py-sm">
      <q-toolbar
        color="primary"
				class="container"
      >
			<img src="statics/logo.png" height="30px"/>
        <q-toolbar-title>
					<router-link class="text-white" to="/posts">Q-Blog</router-link>
          <div slot="subtitle">Running on Quasar v{{ $q.version }}</div>
        </q-toolbar-title>

				<q-btn flat size="0.8rem" to="/about" label="About" />
				<q-btn flat size="0.8rem" to="/contact" label="Contact" />

				<q-btn-group
					v-if="!$auth.check()"
					class="q-ml-lg bg-tertiary"
					flat
				>
					<q-btn class="q-pr-sm" flat size="0.8rem" icon-right="lock" label="login" to="/login" />
					<q-btn class="q-pl-sm" flat size="0.8rem" icon-right="lock_open" label="register" to="/register" />
				</q-btn-group>

				<q-btn-dropdown
				  v-else
					:label="$auth.user().username"
					size="0.8rem"
					class="q-ml-lg bg-tertiary"
					no-caps
				  flat
				>
				  <q-list link>
						<q-item to="/users/update-profile">
							<q-item-side>
								<q-item-tile avatar>
									<img :src="avatar" alt="avatar" width="20px">
								</q-item-tile>
							</q-item-side>
							<q-item-main>
				        <q-item-tile color="primary" label>Update Profile</q-item-tile>
				      </q-item-main>
						</q-item>

				    <q-item @click.native="logout">
							<q-item-side class="text-center" color="primary" icon="lock" />
				      <q-item-main>
				        <q-item-tile color="primary" label>Logout</q-item-tile>
				      </q-item-main>
				    </q-item>
				  </q-list>
				</q-btn-dropdown>


      </q-toolbar>
    </q-layout-header>

    <q-page-container>
      <router-view />
    </q-page-container>

		<q-layout-footer style="font-size:0.9em" class="text-center text-white bg-primary q-py-sm">
			Developed with
			<q-icon  class="q-mx-xs" name="favorite" size="1.2rem" color="negative" />
			By <a href="https://www.twitter.com/" class="text-white text-bold">Abdeleaziz Sliem</a>
	  </q-layout-footer>
  </q-layout>
</template>

<script>
import { Notify } from 'quasar';

export default {
	computed: {
		avatar: function () {
			let usersPath = this.$baseURL + '/storage/users/';
			if (this.$auth.user().avatar == null) {
				return usersPath + 'avatar.svg';
			} else {
				return usersPath + this.$auth.user().avatar;
			}
		}
	},


	methods: {
		logout() {
			this.$auth.logout({
			  makeRequest: true,
			  success: (res) => {
					Notify.create({
					  message: 'you have been Logged out successfully!',
						type: 'positive',
  					color: 'positive',
						icon: 'error',
						position: 'top'
					});
				},
			  error: (errors) => {
					console.log(errors);
				},
			  redirect: '/posts'
			});
		}
	}
}
</script>

<style>
</style>
