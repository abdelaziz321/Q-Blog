<template>
  <div>
    <button 
      v-if="$gate.allow('create', 'category')" 
      @click="createCategory"
      type="button" 
      class="btn btn-primary mb-3 float-right" 
    >Add Category</button>
    
    <div class="table-responsive">
      <table class="table table-striped table-light table-bordered table-hover text-center" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Moderator</th>
            <th>Posts</th>
            <th>Controls</th>
          </tr>
        </thead>
        <tbody>
          <!-- link moderator -->
          <category-row
            v-for="category in categories"
            :category="category"
            :key="category.id"
          ></category-row>
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
import categoryForm from './form';
import CategoryRow from './category_row';

export default {
  components: {
    CategoryRow
  },


  data: function () {
    return {
      page: 0
    }
  },


  computed: {
    categories: function () {
      return this.$store.getters['categories/categories'];
    },
    totalPages: function () {
      return this.$store.getters['categories/totalPages'];
    }
  },


  created: function () {
    this.page = helpers.getPageQuery();
  },


  watch: {
    page: function (val) {
      this.$store.dispatch('categories/getAllCategories', val);
    }
  },


  methods: {
    setPage(page) {
      this.page = page;
      this.$router.push('categories?page=' + page);
    },

    createCategory() {
      this.$store.dispatch('modal/update', {
        action: 'create',
        title: 'Add Category',
        component: categoryForm
      });
    }
  }
}
</script>
