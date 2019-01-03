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
        @search-change="searchUsers"
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
      isLoading: false
    };
  },


  computed: {
    users: function () {
      return this.$store.getters['users/usersSearch'];
    }
  },


  created: function () {
    if (this.action == 'update') {
      this.categoryForm = JSON.parse(JSON.stringify(this.formData));
    }
  },


  methods: {
    searchUsers(query) {
      this.isLoading = true;

      (window._.debounce(
        async function (vm, query) {
          if (query != '') {
            await vm.$store.dispatch('users/searchUsers', query);
          }
          vm.isLoading = false;
        }
      , 300))(this, query);
    },

    save () {
      if (this.action == 'create') {
        this.create(this.categoryForm);
      }
      else if (this.action == 'update') {
        this.update(this.categoryForm);
      }
    },

    create(category) {
      this.$store.dispatch('categories/createCategory', category)
      .then(() => {
        // send successful message
        this.$store.dispatch('message/update', {
          title: category.title,
          body: `${category.title} category has been added successfully`,
          class: 'success',
          confirm: false
        }, { root: true });

        window.$('#modal').modal('hide');
      })
      .catch((error) => {
        let response = error.response;

        // send error message
        this.$store.dispatch('message/update', {
          title: category.title,
          body: response.data.message,
          itemsErrors: response.data.errors,
          class: 'danger',
          confirm: false
        }, { root: true });
      });
    },

    update(category) {
      this.$store.dispatch('categories/updateCategory', category)
      .then(() => {
        // send successful message
        this.$store.dispatch('message/update', {
          title: category.title,
          body: `${category.title} category has been updated successfully`,
          class: 'success',
          confirm: false
        }, { root: true });

        window.$('#modal').modal('hide');
      })
      .catch((error) => {
        let response = error.response;

        // send error message
        this.$store.dispatch('message/update', {
          title: category.title,
          body: response.data.message,
          itemsErrors: response.data.errors,
          class: 'danger',
          confirm: false
        }, { root: true });
      });
    }
  }

}
</script>
