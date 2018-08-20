<template>
  <tr>
    <!-- title -->
    <td>
      <router-link :to="'/posts/' + post.slug">{{ post.title }}</router-link>
    </td>

    <!-- views -->
    <td>{{ post.views }}</td>

    <!-- comments -->
    <td>{{ post.total_comments }}</td>

    <!-- recommendations -->
    <td>{{ post.total_recommendations }}</td>

    <!-- published -->
    <td v-if="post.published == 1">
      <span class="badge badge-info">yes</span>
    </td>
    <td v-else>
      <span class="badge badge-warning">no</span>
    </td>

    <!-- author -->
    <td v-if="typeof $route.params.user === 'undefined'">
      <router-link v-if="typeof post.author !== 'undefined'"
      :to="'/users/' + post.author.slug + '/posts'"
      >{{ author }}</router-link>
    </td>

    <!-- category -->
    <td  v-if="typeof $route.params.category === 'undefined'">
      <router-link v-if="typeof post.category !== 'undefined'"
      :to="'/categories/' + post.category.slug + '/posts'"
      >{{ post.category.title }}</router-link>
    </td>

    <!-- controls -->
    <td>
      <div v-if="typeof post.author !== 'undefined'" class="btn-group btn-group-sm">
        <router-link v-if="$gate.allow('update', 'post', post)" class="btn btn-success" :to="'/posts/update-post/' + this.post.slug">Update</router-link>
        <button v-if="$gate.allow('delete', 'post', post)" type="button" class="btn btn-danger" @click="deletePost">Delete</button>
      </div>
    </td>
  </tr>
</template>

<script>
import postForm from './form';

export default {
  props: [
    'post'
  ],


  computed: {
    author: function () {
      if (typeof this.post.author === 'undefined') {
        return 'unknown';
      }
      return this.post.author.name;
    }
  },


  methods: {
    deletePost() {
      this.$store.dispatch('message/update', {
        title: this.post.title,
        body: 'Are you sure you want to delete this Post?',
        class: 'danger',
        confirm: true
      });

      this.$bus.$once('proceed', () => {
        this.$store.dispatch('posts/deletePost', this.post);
        this.$store.dispatch('message/close');
      });
    }
  }
}
</script>
