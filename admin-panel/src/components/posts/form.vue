<template>
  <div class="new_post">
    <form class="forms-sample" :key="this.$route.params.post">
      <h5 class=" sub-title mb-3 text-primary">{{ formHeader }}</h5>

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
            v-model="postForm.category"
            :loading="isLoadingCategory"
            @search-change="searchCategories"
            label="title"
            track-by="id"
            :max-height="130"
            :options-limit="20"
            :allow-empty="false"
            :internal-search="false"
            placeholder="select the category of the post"
          >
          </multiselect>
        </div>

        <!-- select tags -->
        <div class="col-md-6">
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
      </div>

      <!-- body input -->
      <div class="form-group">
        <label class="label-form" for="describtion">Body:</label>
        <div class="text_editor">
          <jodit-vue v-model="postForm.body" :buttons="buttons" :config="config" />
        </div>
      </div>

      <div class="text-right">
        <button class="btn btn-primary" @click.prevent="save">Submit</button>
      </div>
    </form>
  </div>
</template>

<script>
import 'jodit/build/jodit.min.css';
import JoditVue from 'jodit-vue';
import Multiselect from 'vue-multiselect';

export default {
  components: {
    Multiselect,
    JoditVue
  },


  data: function () {
    return {
      formHeader: '',

      isLoadingTag: false,
      isLoadingCategory: false,

      postForm: {
        title: '',
        body: '',
        category: '',
        tags: []
      },

      buttons: [
        'bold', 'strikethrough', 'underline', 'italic', '|',
        'ul','ol', '|',
        'image','table','link','|',
        'outdent','indent', '|',
        'align','undo','redo', '|',
        'hr', 'symbol','fullsize'
      ],
      config: {
        "uploader": {
          "insertImageAsBase64URI": true
        }
      }
    };
  },


  computed: {
    categories: function () {
      return this.$store.getters['categories/search']
    },

    tags: function () {
      return this.$store.getters['tags/search'];
    }
  },


  created: function () {
    // new form
    if (typeof this.$route.params.post === 'undefined') {
      this.formHeader = 'New Post';
      this.postForm = {};
      return;
    }

    // update from
    this.formHeader = 'Update Post';
    this.$store.dispatch('posts/getPost', this.$route.params.post)
    .then(() => {
      let post = this.$store.getters['posts/post'];

      this.postForm.slug = post.slug;
      this.postForm.title = post.title;
      this.postForm.tags = post.tags;

      this.postForm.category = post.category;
      this.$store.commit(
        'categories/SET_CATEGORIES_SEARCH', [post.category]
      );

      let body = post.body.replace(/posts\/images\/.*?\.png/g, (imagePath) => {
        return this.$baseURL + '/storage/' +  imagePath;
      });

      // remove the div.body-wrapper 
      if (body.substr(0, 26) === '<div class="body-wrapper">') {
        body = body.substring(26, body.lastIndexOf('</div>'));
      }

      this.postForm.body = body;
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
  },

  methods: {
    // search for categories <multiselect>
    searchCategories(query) {
      this.isLoadingCategory = true;

      (window._.debounce(
        async function (vm, query) {
          if (query != '') {
            await vm.$store.dispatch('categories/searchCategories', query);
          }
          vm.isLoadingCategory = false;
        }
      , 300))(this, query);
    },

    // search for tags <multiselect>
    searchTags(query) {
      this.isLoadingTag = true;

      (window._.debounce(
        async function (vm, query) {
          if (query != '') {
            await vm.$store.dispatch('tags/searchTags', query);
          }
          vm.isLoadingTag = false;
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
      return `and ${count} other tags`;
    },

    // save form 
    handleCaptionUpload() {
      this.postForm.caption = this.$refs.caption.files[0];
    },

    save () {
      // add the div.body-wrapper when update of create new post
      if (this.postForm.body.substr(0, 26) !== '<div class="body-wrapper">') {
        this.postForm.body = '<div class="body-wrapper">' + this.postForm.body + '</div>';
      }

      // ==== create post
      if (typeof this.$route.params.post === 'undefined') {
        this.createPost(this.postForm);
        return;
      }
      
      // ==== update post
      // send the relative path of the <img>s' src to the server
      // here each img tag src will be changed
      // from: <img src="http://127.0.0.1:8000/storage/posts/images/15465151870.png">
      // to:   <img src="posts/images/15465151870.png">
      let pattern = new RegExp(
        this.$baseURL.replace(/\//g, '\\/') + '\/storage\/posts\/images\/.*?\.png', 'g'
      );
      this.postForm.body = this.postForm.body.replace(pattern, (absolutePath) => {
        let index = absolutePath.indexOf('posts/images/');
        return absolutePath.substr(index);
      });
      
      this.updatePost(this.postForm);
    },

    // create new post
    createPost(post) {
      this.$store.dispatch('posts/createPost', post)
      .then((postSlug) => {
        // send successful message
        this.$store.dispatch('message/update', {
          title: post.title,
          body: `${post.title} post has been added successfully`,
          class: 'success',
          confirm: false
        }, { root: true });

        this.$router.push('/posts/' + postSlug);
      })
      .catch((error) => {
        let response = error.response;

        // send error message
        this.$store.dispatch('message/update', {
          title: post.title,
          body: response.data.message,
          itemsErrors: response.data.errors,
          class: 'danger',
          confirm: false
        }, { root: true });
      });
    },

    // update the given post
    updatePost(post) {     
      this.$store.dispatch('posts/updatePost', post)
      .then((postSlug) => {
        // send successful message
        this.$store.dispatch('message/update', {
          title: post.title,
          body: `${post.title} post has been updated successfully`,
          class: 'success',
          confirm: false
        }, { root: true });
        
        this.$router.push('/posts/' + postSlug);
      })
      .catch((error) => {
        let response = error.response;

        // send error message
        this.$store.dispatch('message/update', {
          title: post.title,
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
