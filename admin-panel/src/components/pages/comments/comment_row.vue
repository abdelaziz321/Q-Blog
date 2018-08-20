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
        <button v-if="$gate.allow('delete', 'comment', comment)" type="button" class="btn btn-danger" @click="deleteComment">Delete</button>
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

      this.$bus.$once('proceed', () => {
        this.$store.dispatch('comments/deleteComment', this.comment);
        this.$store.dispatch('message/close');
      });
    }
  }
}
</script>
