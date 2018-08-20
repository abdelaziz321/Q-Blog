<template>
  <div>
    <table class="table table-information" width="100%" cellspacing="0">
      <tr>
        <td><strong>Tag</strong></td>
        <td>{{ tag.name }}</td>
      </tr>
      <tr>
        <td><strong>Total Posts</strong></td>
        <td>{{ tag.total_posts }}</td>
      </tr>
    </table>

    <h4 class="mt-5">{{ tag.name }},s Posts:</h4>
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
    tag: function () {
      return this.$store.getters['tags/tags'];
    },
    posts: function () {
      return this.$store.getters['posts/posts'];
    },
    totalPages: function () {
      return this.$store.getters['posts/totalPages'];
    }
  },


  watch: {
    page: function (val) {
      this.$store.dispatch('posts/getTagPosts', {
        page: val,
        tag: this.$route.params.tag
      });
    }
  },


  created: function () {
    this.page = helpers.getPageQuery();
    this.$store.dispatch('tags/getTag', this.$route.params.tag);
  },


  methods: {
    setPage(page) {
      this.page = page;
      router.push('/tags/' + this.$route.params.tag + '/posts?page=' + page);
    }
  }
}
</script>
