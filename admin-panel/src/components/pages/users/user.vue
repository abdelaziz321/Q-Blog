<template>
  <div class="user_page">
    <div class="media">
      <img class="d-flex mr-3 avatar" :src="avatar" height="300px" alt="user_avatar" />
      <div class="media-body">
        <table class="table table-information mb-0" width="100%" cellspacing="0">
          <tr>
            <td><strong>Username</strong></td>
            <td>{{ user.username }}</td>
          </tr>
          <tr>
            <td><strong>Email</strong></td>
            <td>{{ user.email }}</td>
          </tr>
          <tr>
            <td><strong>About</strong></td>
            <td>{{ user.about }}</td>
          </tr>
          <tr>
            <td><strong>Joined</strong></td>
            <td>{{ user.joined_at }}</td>
          </tr>
          <tr>
            <td><strong>Role</strong></td>
            <td>{{ user.role }}</td>
          </tr>
          <tr v-if="typeof user.role !== 'undefined'  && user.role.indexOf('user') === -1">
            <td><strong>Total Posts</strong></td>
            <td>{{ user.total_posts }}</td>
          </tr>
          <tr>
            <td><strong>Recommendations</strong></td>
            <td>{{ user.total_recommendations }}</td>
          </tr>
          <tr>
            <td><strong>Comments</strong></td>
            <td>{{ user.total_comments }}</td>
          </tr>
          <tr>
            <td><strong>Votes</strong></td>
            <td>{{ user.total_votes }}</td>
          </tr>

        </table>
      </div>
    </div>

    <h4 class="mt-5">{{ user.username }},s Posts:</h4>
    <hr />
    <post-table
      :posts="posts"
      :totalPages="totalPages"
      :page="page"
      @setPage="setPage"
    ></post-table>
  </div>
</template>

<script>
import helpers from '../../../helpers.js';
import PostTable from '../posts/post_table';

export default {
  components: {
    PostTable
  },


  data: function () {
    return {
      page: 0
    }
  },


  computed: {
    user: function () {
      return this.$store.getters['users/user'];
    },
    posts: function () {
      return this.$store.getters['posts/posts'];
    },
    totalPages: function () {
      return this.$store.getters['posts/totalPages'];
    },
    avatar: function () {
      let baseUrl = window.Laravel.baseURL + '/storage/users/';
      if (!!this.user.avatar) {
        return baseUrl + this.user.avatar;
      }
      return  baseUrl + 'avatar.svg';
    }
  },


  watch: {
    page: function (val) {
      this.$store.dispatch('posts/getUserPosts', {
        page: val,
        user: this.$route.params.user
      });
    }
  },


  created: function () {
    this.page = helpers.getPageQuery();
    this.$store.dispatch('users/getUser', this.$route.params.user);
  },


  methods: {
    setPage(page) {
      this.page = page;
      router.push('/users/' + this.$route.params.user + '/posts?page=' + page);
    }
  }
}
</script>
