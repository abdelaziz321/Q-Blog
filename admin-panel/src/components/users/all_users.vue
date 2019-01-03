<template>
  <div>
    <user-table
      :users="users"
      :totalPages="totalPages"
      :page="page"
      @setPage="setPage"
    ></user-table>
  </div>
</template>

<script>
import helpers from '@/helpers.js';
import UserTable from './user_table';

export default {
  components: {
    UserTable
  },


  data: function () {
    return {
      page: 0
    }
  },


  computed: {
    users: function () {
      return this.$store.getters['users/users'];
    },
    totalPages: function () {
      return this.$store.getters['users/totalPages'];
    }
  },


  created: function () {
    this.page = helpers.getPageQuery();
  },


  watch: {
    page: function (val) {
      this.$store.dispatch('users/getAllUsers', val);
    }
  },


  methods: {
    setPage(page) {
      this.page = page;
      this.$router.push('users?page=' + page);
    }
  }
}
</script>
