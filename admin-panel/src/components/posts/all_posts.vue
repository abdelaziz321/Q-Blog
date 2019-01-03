<template>
  <div>
    <router-link to="/posts/new-post" class="btn btn-primary mb-3 float-right">New Post</router-link>
    <post-table
      :posts="posts"
      :totalPages="totalPages"
      :page="page"
      @setPage="setPage"
    ></post-table>
  </div>
</template>

<script>
import helpers from '@/helpers.js';
import PostTable from './post_table';

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
    posts: function () {
      return this.$store.getters['posts/posts'];
    },
    totalPages: function () {
      return this.$store.getters['posts/totalPages'];
    }
  },


  created: function () {
    this.page = helpers.getPageQuery();
  },


  watch: {
    page: function (val) {
      this.$store.dispatch('posts/getAllPosts', val);
    }
  },


  methods: {
    setPage(page) {
      this.page = page;
      this.$router.push('posts?page=' + page);
    }
  }
}
</script>
