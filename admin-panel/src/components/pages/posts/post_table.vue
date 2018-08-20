<template>
  <div class="table-responsive">
    <table class="table table-striped table-light table-bordered table-hover text-center" id="dataTable" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th>Title</th>
          <th>Views</th>
          <th>Comments</th>
          <th title="Recommendations">Recom.</th>
          <th>Published</th>
          <th v-if="typeof $route.params.user === 'undefined'">
            <router-link to="/users">Author</router-link>
          </th>
          <th v-if="typeof $route.params.category === 'undefined'">
            <router-link to="/categories">Category</router-link>
          </th>
          <th>Controls</th>
        </tr>
      </thead>
      <tbody>
        <post-row
          v-for="post in posts"
          v-if="post"
          :post="post"
          :key="post.id"
        ></post-row>
      </tbody>
    </table>

    <paginate
      v-model="currentPage"
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
</template>

<script>
import PostRow from './post_row';

export default {
  components: {
    PostRow
  },


  props: [
    'posts', 'totalPages', 'page'
  ],


  computed: {
    currentPage: {
      get: function () {
        return  this.page;
      },
      set: function (newValue) {}
    }
  },


  methods: {
    setPage(page) {
      this.$emit('setPage', page);
    }
  }
}
</script>
