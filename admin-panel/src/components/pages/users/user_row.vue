<template>
  <tr>
    <!-- avatar -->
    <td>
      <img v-if="!!user.avatar" :src="avatar" height="50px" :alt="user.username" />
      <img v-else :src="avatar" height="50px" alt="user_avatar" />
    </td>

    <!-- username -->
    <td>
      <router-link :to="'/users/' + user.slug + '/posts'">{{ user.username }}</router-link>
    </td>

    <!-- email -->
    <td>{{ user.email }}</td>

    <!-- role -->
    <td>
      <span v-if="user.role === 'admin'" class="badge badge-success">
        {{ user.role }}
      </span>
      <span v-else-if="user.role === 'moderator'" class="badge badge-primary">
        {{ user.role }}
      </span>
      <span v-else-if="user.role === 'author'" class="badge badge-warning">
        {{ user.role }}
      </span>
      <span v-else-if="user.role === 'banned'" class="badge badge-danger">
        {{ user.role }}
      </span>
      <span v-else class="badge badge-secondary">
        {{ user.role }}
      </span>
    </td>

    <!-- comments -->
    <td>{{ user.total_comments }}</td>

    <!-- recommendations -->
    <td>{{ user.total_recommendations }}</td>

    <!-- controls [assign role, ban|unban, delete] -->
    <td>
      <div class="btn-group btn-group-sm">
        <button 
          v-if="$gate.allow('assignRole', 'user')"
          @click="assignRoleForm()"
          type="button"
          class="btn btn-primary"
        >Role</button>

        <button
          v-if="$gate.allow('assignRole', 'user') && user.role === 'banned'"
          type="button"
          class="btn btn-success"
          @click="assignRole('regular')"
        >Unban</button>
        <button
          v-if="$gate.allow('assignRole', 'user') && user.role !== 'banned'"
          type="button"
          class="btn btn-warning"
          @click="assignRole('banned')"
        >Ban</button>

        <button
          v-if="$gate.allow('delete', 'user', user)"
          type="button"
          class="btn btn-danger"
          @click="deleteUser"
        >Delete</button>
      </div>
    </td>
  </tr>
</template>

<script>
import roleForm from './role_form';

export default {
  props: [
    'user'
  ],


  computed: {
    avatar: function () {
      let baseUrl = this.$baseURL + '/storage/users/';
      if (!!this.user.avatar) {
        return baseUrl + this.user.avatar;
      }
      return  baseUrl + 'avatar.svg';
    }
  },


  methods: {
    assignRoleForm() {
      this.$store.dispatch('modal/update', {
        action: 'update',
        formData: this.user,
        title: 'Assign Role to User ' + this.user.username,
        component: roleForm
      });
    },

    assignRole(role) {
      let user = this.user;
      this.$store.dispatch('users/assignRole', {
        user: user,
        role: role
      })
      .then(() => {
        this.$store.dispatch('message/update', {
          title: user.username,
          body: `${user.username} user has been updated successfully`,
          class: 'info',
          confirm: false
        }, { root: true });
      })
      .catch((error) => {
        let response = error.response;

        // send error message
        dispatch('message/update', {
          title: user.title,
          body: response.data.message,
          itemsErrors: response.data.errors,
          class: 'danger',
          confirm: false
        }, { root: true });
      });
    },

    deleteUser() {
      this.$store.dispatch('message/update', {
        title: this.user.username,
        body: 'Are you sure you want to delete this User?',
        class: 'danger',
        confirm: true
      });

      this.$bus.$off('proceed');
      this.$bus.$once('proceed', () => {
        this.delete(this.user);
        this.$store.dispatch('message/close');
      });
    },

    delete(user) {
      this.$store.dispatch('users/deleteUser', user)
      .then((response) => {
        // send successful message
        this.$store.dispatch('message/update', {
          title: user.username,
          body: response.data.message,
          class: 'success',
          confirm: false
        }, { root: true });
      })
      .catch((error) => {
        // send error message
        this.$store.dispatch('message/update', {
          title: user.username,
          class: 'danger',
          body: error.response.data.message,
          errors: error.response.data.errors,
          confirm: false
        }, { root: true });
      });
    }
  }
}
</script>
