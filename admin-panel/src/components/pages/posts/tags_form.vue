<template>
  <form class=" mt-1" id="form-modal">

    <!-- tags input -->
    <div class="form-group">
      <label class="label-form">Tags:</label>
      <multiselect
        :options="tags"
        :internal-search="false"
        :options-limit="20"
        :max-height="130"
        :close-on-select="false"
        :limit="3"
        :multiple="true"
        :limit-text="limitTextTags"
        :loading="isLoadingTag"
        :max="7"
        @search-change="onSearchTags"
        placeholder="select the tags of the posts"
        v-model="postForm.tags"
        label="name"
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
      postForm: {},

      tags: [],
      isLoadingTag: false
    };
  },


  created: function () {
    this.postForm = JSON.parse(JSON.stringify(this.formData));
  },


  methods: {
    save () {
      this.$store.dispatch('posts/assignTags', this.postForm);
    },

    // search for tags <multiselect>
    onSearchTags(query) {
      this.isLoadingTag = true;
      this.searchTags(this, query)
    },

    searchTags: _.debounce((vm, query) => {
      axios.get('/tags/search?q=' + query)
      .then((response) => {
        vm.tags = response.data;
        vm.isLoadingTag = false;
      })
      .catch((error) => {
        console.log(error);
        vm.isLoadingTag = false;
      });
    }, 300),

    limitTextTags(count) {
      return `and ${count} other tags`
    },
  }
}
</script>
