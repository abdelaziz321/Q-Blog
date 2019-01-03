<template>
  <form class=" mt-1" id="form-modal">

    <!-- name input -->
    <div class="form-group">
      <label class="label-form">Name:</label>
      <input type="text" class="form-control" v-model="tagForm.name" placeholder="the name of the tag">
    </div>

    <!-- buttons -->
    <div class="form-footer text-right">
      <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary btn-sm ml-1" @click="save">Save</button>
    </div>
  </form>
</template>

<script>
export default {
  props:[
    'action', 'formData'
  ],


  data: function () {
    return {
      tagForm: {}
    };
  },


  created: function () {
    this.tagForm = JSON.parse(JSON.stringify(this.formData));
  },


  methods: {
    save () {
      this.$store.dispatch('tags/updateTag', this.tagForm)
      .then((response) => {
        // send successful message
        this.$store.dispatch('message/update', {
          title: response.data.tag.name,
          body: `${response.data.tag.name} tag has been updated successfully`,
          class: 'success',
          confirm: false
        }, { root: true });

        window.$('#modal').modal('hide');
      })
      .catch((error) => {
        let response = error.response;
        // send error message
        this.$store.dispatch('message/update', {
          title: this.tagForm.name,
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
