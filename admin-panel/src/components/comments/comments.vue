<template>
  <div>
    <h1>Comments</h1>
    <hr>
    <breadcrumbs/>

    <div class="table-responsive">
      <table class="table table-striped table-light table-bordered table-hover text-center" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>Comment</th>
            <th>Post</th>
            <th>User</th>
            <th>Votes</th>
            <th>Controls</th>
          </tr>
        </thead>
        <tbody>
          <comment-row
            v-for="comment in comments"
            :comment="comment"
            :key="comment.id"
          ></comment-row>
        </tbody>
      </table>

      <paginate
        v-model="page"
        :page-count="totalPages"
        :click-handler="setPage"
        :prev-text="'<'"
        :next-text="'>'"
        :first-button-text="'<<'"
        :last-button-text="'>>'"
        :first-last-button="true"
        :container-class="'pagination pagination-sm'"
        :page-class="'page-item'"
        :prev-class="'page-item prev'"
        :next-class="'page-item next'"
        :page-link-class="'page-link'"
        :prev-link-class="'page-link'"
        :next-link-class="'page-link'"
      ></paginate>
    </div>

  </div>
</template>

<script>
import helpers from '@/helpers.js';
import CommentRow from './comment_row';

export default {
  components: {
    CommentRow
  },


  data: function () {
    return {
      page: 0
    }
  },


  computed: {
    comments: function () {
      return this.$store.getters['comments/comments'];
    },
    totalPages: function () {
      return this.$store.getters['comments/totalPages'];
    }
  },


  created: function () {
    this.page = helpers.getPageQuery();
  },


  watch: {
    page: function (val) {
      this.$store.dispatch('comments/getAllComments', val);
    }
  },


  methods: {
    setPage(page) {
      this.page = page;
      this.$router.push('comments?page=' + page);
    }
  }
}
</script>
