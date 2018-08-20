<template>
  <form class=" mt-2" id="form-modal">

    <!-- title input -->
    <div class="form-group">
      <label class="label-form">Title:</label>
      <input type="text" class="form-control" v-model="categoryForm.title" placeholder="the title of the category">
    </div>

    <!-- description input -->
    <div class="form-group">
      <label class="label-form">Describtion:</label>
      <textarea class="form-control" v-model="categoryForm.description" placeholder="the description of the category"></textarea>
    </div>

    <!-- moderator input -->
    <div class="form-group">
      <label class="label-form">Moderator:</label>
      <multiselect
        :options="users"
        :internal-search="false"
        :allow-empty="false"
        :options-limit="20"
        :max-height="130"
        :loading="isLoading"
        @search-change="onSearch"
        placeholder="select the moderator of the category"
        v-model="categoryForm.moderator"
        label="username"
        track-by="id"
      >
      </multiselect>

    </div>

    <!-- buttons -->
    <div class="form-footer text-right">
      <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary btn-sm ml-1" @click="save">Save</button>
    </div>
  </form>
</template>

<script>
import Multiselect from 'vue-multiselect';

export default {
  components: {
    Multiselect
  },


  props:[
    'action', 'formData'
  ],


  data: function () {
    return {
      categoryForm: {},

      users: [],
      isLoading: false
    };
  },


  created: function () {
    if (this.action == 'update') {
      this.categoryForm = JSON.parse(JSON.stringify(this.formData));
    }
  },


  methods: {
    onSearch(query) {
      this.isLoading = true;
      this.search(this, query)
    },

    search: _.debounce((vm, query) => {
      axios.get('/admin/users/search?q=' + query)
      .then((response) => {
        vm.users = response.data;
        vm.isLoading = false;
      })
      .catch((error) => {
        console.log(error);
        vm.isLoading = false;
      });
    }, 300),

    save () {
      if (this.action == 'create') {
        this.$store.dispatch('categories/createCategory', this.categoryForm);
      } else if (this.action == 'update') {
        this.$store.dispatch('categories/updateCategory', this.categoryForm);
      }
    }
  }

}
</script>
