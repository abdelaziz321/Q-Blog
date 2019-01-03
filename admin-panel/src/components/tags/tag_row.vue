<template>
  <tr>
    <!-- name -->
    <td>
      <router-link :to="'/tags/' + tag.slug + '/posts'">
        {{ tag.name }}
      </router-link>
    </td>

    <!-- posts -->
    <td>{{ tag.total_posts }}</td>

    <!-- controls -->
    <td>
      <div class="btn-group btn-group-sm">
        <button v-if="$gate.allow('update', 'tag')" type="button" class="btn btn-success" @click="updateTag">Update</button>
        <button v-if="$gate.allow('delete', 'tag')" type="button" class="btn btn-danger" @click="deleteTag">Delete</button>
      </div>
    </td>
  </tr>
</template>

<script>
import tagForm from './form';

export default {
  props: [
    'tag'
  ],


  methods: {
    updateTag() {
      this.$store.dispatch('modal/update', {
        action: 'update',
        formData: this.tag,
        title: this.tag.name,
        component: tagForm
      });
    },

    deleteTag() {
      this.$store.dispatch('message/update', {
        title: this.tag.name,
        body: 'Are you sure you want to delete this Tag?',
        class: 'danger',
        confirm: true
      });

      this.$bus.$off('proceed');
      this.$bus.$once('proceed', () => {
        this.delete(this.tag);
        this.$store.dispatch('message/close');
      });
    },

    delete(tag) {
      this.$store.dispatch('tags/deleteTag', tag)
      .then((response) => {
        // send successful message
        this.$store.dispatch('message/update', {
          title: tag.title,
          body: response.data.message,
          class: 'success',
          confirm: false
        }, { root: true });
      })
      .catch((error) => {
        // send error message
        this.$store.dispatch('message/update', {
          title: tag.name,
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
