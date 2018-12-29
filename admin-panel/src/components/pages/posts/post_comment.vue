<template>
  <div :id="'comment_' + comment.id" class="media mb-4">
    <img v-if="comment.user.avatar === null" class="d-flex mr-3 rounded-circle" :src="usersPath + 'avatar.svg'" width="50px" alt="user">
    <img v-else class="d-flex mr-3 rounded-circle" :src="usersPath + comment.user.avatar" width="50px" alt="user">
    <div class="media-body">
      <h5 class="mt-0">
        <router-link :to="'/users/' + comment.user.slug + '/posts'">{{ comment.user.name }}</router-link>
        <small class="ml-2">{{ comment.created_at }}</small>
        <button v-if="$gate.allow('delete', 'comment', comment)" type="button" class="float-right" @click="deleteComment">
          <font-awesome-icon icon="times" />
        </button>
       </h5>
      {{ comment.body }}
    </div>
  </div>
</template>

<script>
export default {
  props: [
    'comment'
  ],


  data: function () {
    return {
      usersPath: window.Laravel.baseURL + '/storage/users/'
    };
  },


  methods: {
    deleteComment() {
      this.$store.dispatch('message/update', {
        body: 'Are you sure you want to delete this Comment?',
        class: 'danger',
        confirm: true
      });

      this.$bus.$off('proceed');
      this.$bus.$once('proceed', () => {
        this.$store.dispatch('comments/deleteComment', this.comment);
        this.$store.dispatch('message/close');
      });
    }
  }
}
</script>
