<template>
  <div class="table-responsive">
    <table class="table table-striped table-light table-bordered table-hover text-center" id="dataTable" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th>Tag</th>
          <th>Posts</th>
          <th>Controls</th>
        </tr>
      </thead>
      <tbody>
        <tag-row
          v-for="tag in tags"
          :tag="tag"
          :key="tag.slug"
        ></tag-row>
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
</template>

<script>
import helpers from '@/helpers.js';
import TagRow from './tag_row';

export default {
  components: {
    TagRow
  },


  data: function () {
    return {
      page: 0
    }
  },


  computed: {
    tags: function () {
      return this.$store.getters['tags/tags'];
    },
    totalPages: function () {
      return this.$store.getters['tags/totalPages'];
    }
  },


  created: function () {
    this.page = helpers.getPageQuery();
  },


  watch: {
    page: function (val) {
      this.$store.dispatch('tags/getAllTags', val);
    }
  },


  methods: {
    setPage(page) {
      this.page = page;
      this.$router.push('tags?page=' + page);
    }
  }
}
</script>
