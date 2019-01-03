<template>
  <div class="table-responsive">
    <table class="table table-striped table-light table-bordered table-hover text-center" id="dataTable" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th>Avatar</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Comments</th>
          <th title="recommendations">Recom.</th>
          <th>Controls</th>
        </tr>
      </thead>
      <tbody>
        <user-row
          v-for="user in users"
          :user="user"
          :key="user.slug"
        ></user-row>
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
import UserRow from './user_row';

export default {
  components: {
    UserRow
  },


  props: [
    'users', 'totalPages', 'page'
  ],


  computed: {
    currentPage: {
      get: function () {
        return  this.page;
      },
      set: function () {}
    }
  },
  

  methods: {
     setPage(page) {
       this.$emit('setPage', page);
     }
  }
}
</script>
