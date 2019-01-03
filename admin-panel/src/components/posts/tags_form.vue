<template>
  <form class=" mt-1" id="form-modal">

    <!-- tags input -->
    <div class="form-group">
      <label class="label-form">Tags:</label>
      <multiselect
        :options="tags"
        v-model="postForm.tags"
        :loading="isLoadingTag"
        @search-change="searchTags"
        label="name"
        track-by="id"
        @tag="addTag"
        :taggable="true"
        :max="7"
        :limit="3"
        :multiple="true"
        :max-height="130"
        :options-limit="20"
        :internal-search="false"
        :close-on-select="false"
        :limit-text="limitTextTags"
        placeholder="select the tags of the posts"
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
      isLoadingTag: false
    };
  },

  computed: {
    tags: function () {
      return this.$store.getters['tags/search'];
    }
  },


  created: function () {
    this.postForm = JSON.parse(JSON.stringify(this.formData));
  },


  methods: {
    searchTags(query) {
      this.isLoading = true;

      (window._.debounce(
        async function (vm, query) {
          if (query != '') {
            await vm.$store.dispatch('tags/searchTags', query);
          }
          vm.isLoading = false;
        }
      , 300))(this, query);
    },

    addTag(newTag) {
      const tag = {
        name: newTag,
        id: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
      }
      this.postForm.tags.push(tag);
    },

    limitTextTags(count) {
      return `and ${count} other tags`
    },

    save () {
      this.$store.dispatch('posts/assignTags', this.postForm)
      .then(() => {
        this.$store.dispatch('message/update', {
          title: this.postForm.title,
          body: 'post\'s tags have been updated successfully',
          class: 'success',
          confirm: false
        }, { root: true });
      });
    }
  }
}
</script>
