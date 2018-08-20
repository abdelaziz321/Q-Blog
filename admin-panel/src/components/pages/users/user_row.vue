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
      <span v-else-if="user.role === 'banned user'" class="badge badge-danger">
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

    <!-- controls -->
    <td>
      <div class="btn-group btn-group-sm">
        <button v-if="$gate.allow('assignRole', 'user') && user.role !== 'moderator'" type="button" class="btn btn-primary" @click="assignRole">Role</button>

        <button v-if="$gate.allow('assignRole', 'user') && user.role === 'banned user'" type="button" class="btn btn-success" @click="unbanUser">Unban</button>
        <button v-if="$gate.allow('assignRole', 'user') && user.role !== 'banned user'" type="button" class="btn btn-warning" @click="banUser">Ban</button>

        <button v-if="$gate.allow('delete', 'user', user)" type="button" class="btn btn-danger" @click="deleteUser">Delete</button>
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
      let baseUrl = window.Laravel.baseURL + '/storage/users/';
      if (!!this.user.avatar) {
        return baseUrl + this.user.avatar;
      }
      return  baseUrl + 'avatar.svg';
    }
  },


  methods: {
    banUser() {
      this.$store.dispatch('users/banUser', this.user);
    },

    unbanUser() {
      this.$store.dispatch('users/unbanUser', this.user);
    },

    assignRole() {
      this.$store.dispatch('modal/update', {
        action: 'update',
        formData: this.user,
        title: 'Assign Role to User ' + this.user.username,
        component: roleForm
      });
    },

    deleteUser() {
      this.$store.dispatch('message/update', {
        title: this.user.username,
        body: 'Are you sure you want to delete this User?',
        class: 'danger',
        confirm: true
      });

      this.$bus.$once('proceed', () => {
        this.$store.dispatch('users/deleteUser', this.user);
        this.$store.dispatch('message/close');
      });
    }
  }
}
</script>
