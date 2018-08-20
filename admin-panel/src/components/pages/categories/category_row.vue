<template>
  <tr>
    <!-- title -->
    <td>
      <router-link :to="'/categories/' + category.slug + '/posts'">
        {{ category.title }}
      </router-link>
    </td>

    <td>{{ description }}</td>
    <td>{{ moderator }}</td>
    <td>{{ category.total_posts }}</td>

    <!-- controls -->
    <td>
      <div class="btn-group btn-group-sm">
        <button v-if="$gate.allow('update', 'category', category)" type="button" class="btn btn-success" @click="updateCategory">Update</button>
        <button v-if="$gate.allow('delete', 'category')" type="button" class="btn btn-danger" @click="deleteCategory">Delete</button>
      </div>
    </td>
  </tr>
</template>

<script>
import categoryForm from './form';

export default {
  props: [
    'category'
  ],


  computed: {
    moderator: function () {
      if (typeof this.category.moderator === 'undefined') {
        return 'unknown';
      }
      return this.category.moderator.username;
    },
    description: function () {
      if (typeof this.category.description === 'undefined') {
        return '';
      }
      let desc = this.category.description;
      if (desc.length > 100) {
        return desc.slice(0, 97) + '...';
      }
      return desc;
    }
  },


  methods: {
    updateCategory() {
      this.$store.dispatch('modal/update', {
        action: 'update',
        formData: this.category,
        title: this.category.title,
        component: categoryForm
      });
    },

    deleteCategory() {
      this.$store.dispatch('message/update', {
        title: this.category.title,
        body: 'Are you sure you want to delete this Category?',
        class: 'danger',
        confirm: true
      });

      this.$bus.$once('proceed', () => {
        this.$store.dispatch('categories/deleteCategory', this.category);
        this.$store.dispatch('message/close');
      });
    }
  }
}
</script>
