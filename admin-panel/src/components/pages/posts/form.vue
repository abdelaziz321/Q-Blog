<template>
  <div class="new_post">
    <form class="forms-sample" :key="this.$route.params.post">
      <h5 class=" sub-title mb-3 text-primary text-center">{{ formHeader }}</h5>

      <div class="form-group row">
        <!-- title input -->
        <div class="col-md-6">
          <label class="label-form" for="title">Title:</label>
          <input type="text" class="form-control" id="title" v-model="postForm.title" placeholder="the title of the post">
        </div>

        <!-- caption input -->
        <div class="col-md-6">
          <label class="label-form" for="title">Caption:</label>
          <div class="form-control" style="padding:6px">
            <input type="file" ref="caption" @change="handleCaptionUpload()">
          </div>
        </div>
      </div>

      <div class="form-group row">
        <!-- select category -->
        <div class="col-md-6">
          <label class="label-form">Category:</label>
          <multiselect
            :options="categories"
            :internal-search="false"
            :allow-empty="false"
            :options-limit="20"
            :max-height="130"
            :loading="isLoadingCategory"
            @search-change="onSearchCategories"
            placeholder="select the category of the post"
            v-model="postForm.category"
            label="title"
            track-by="id"
          >
          </multiselect>
        </div>

        <!-- select tags -->
        <div class="col-md-6">
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
      </div>

      <!-- body input -->
      <div class="form-group">
        <label class="label-form" for="describtion">Body:</label>
        <wysiwyg v-model="postForm.body" />
      </div>

      <div class="text-right">
        <button class="btn btn-primary" @click.prevent="save">Submit</button>
      </div>
    </form>
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect';

export default {
  components: {
    Multiselect
  },


  data: function () {
    return {
      formHeader: '',

      categories: [],
      isLoadingCategory: false,

      tags: [],
      isLoadingTag: false,

      postForm: {}
    };
  },

  created: function () {
    if (typeof this.$route.params.post !== 'undefined') { // update form
      this.formHeader = 'Update Post';
      this.$store.dispatch('posts/getPost', this.$route.params.post)
      .then(() => {
        this.postForm = this.$store.getters['posts/post'];
      })
      .catch(() => {
        this.$store.dispatch('message/update', {
          title: this.$route.params.post,
          body: `can't find post '${this.$route.params.post}'!`,
          class: 'info',
          confirm: false
        }, { root: true });

        this.formHeader = 'New Post';
        this.$router.push('/posts/new-post');
      });
    } else {                                              // new form
      this.formHeader = 'New Post';
    }
  },


  methods: {
    // search for categories <multiselect>
    onSearchCategories(query) {
      this.isLoadingCategory = true;
      this.searchCategories(this, query)
    },

    searchCategories: _.debounce((vm, query) => {
      axios.get('/categories/search?q=' + query)
      .then((response) => {
        vm.categories = response.data;
        vm.isLoadingCategory = false;
      })
      .catch((error) => {
        console.log(error);
        vm.isLoadingCategory = false;
      });
    }, 300),

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
      return `and ${count} other tags`;
    },

    handleCaptionUpload() {
      this.postForm.caption = this.$refs.caption.files[0];
    },

    save () {
      if (typeof this.$route.params.post !== 'undefined') {
        // i think what I'm doing here is wrong.
        // but it works :D.
        this.$store.dispatch('posts/updatePost', this.postForm)
        .then(postSlug => {
            this.$router.push('/posts/' + postSlug);
        });
      } else {
        this.$store.dispatch('posts/createPost', this.postForm);
      }
    }
  }

}
</script>
