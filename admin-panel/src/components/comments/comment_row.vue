<template>
  <tr>
    <td>
      <router-link :to="'/posts/' + comment.post.slug + '#comment_' + comment.id">
        {{ (comment.body).slice(0, 50) }}
      </router-link>
    </td>
    <td>{{ comment.post.title }}</td>
    <td>{{ comment.user.name }}</td>
    <td>{{ comment.votes }}</td>

    <td>
      <div class="btn-group btn-group-sm">
        <button 
          v-if="$gate.allow('delete', 'comment', comment)"
          @click="deleteComment"
          type="button" 
          class="btn btn-danger"
        >Delete</button>
      </div>
    </td>
  </tr>
</template>

<script>
export default {
  props: [
    'comment'
  ],

  methods: {
    deleteComment() {
      this.$store.dispatch('message/update', {
        body: 'Are you sure you want to delete this Comment?',
        class: 'danger',
        confirm: true
      });

      this.$bus.$off('proceed');
      this.$bus.$once('proceed', () => {
        this.delete(this.comment);
        this.$store.dispatch('message/close');
      });
    },

    delete(comment) {
      this.$store.dispatch('comments/deleteComment', comment).
      then((response) => {
        // send successful message
        this.$store.dispatch('message/update', {
          body: response.data.message,
          class: 'success',
          confirm: false
        }, { root: true });
      })
      .catch((error) => {
        // send error message
        this.$store.dispatch('message/update', {
          class: 'danger',
          body: error.response.data.message,
          errors: error.response.data.errors,
          confirm: false
        }, { root: true });
      });
    }
  }
}
</script>
