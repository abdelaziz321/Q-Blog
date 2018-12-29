<template>
  <form class=" mt-1" id="form-modal">

    <!-- roles select -->
    <div class="form-group">
      <label class="label-form">Role:</label>
      <select class="form-control" v-model="role">
        <option value="admin">admin</option>
        <option value="author">author</option>
        <option value="regular">regular user</option>
        <option value="banned">banned user</option>
      </select>
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
      userForm: {},
      role: 'regular'
    };
  },


  created: function () {
    this.userForm = JSON.parse(JSON.stringify(this.formData));
  },


  methods: {
    save () {
      let user = this.userForm;
      this.$store.dispatch('users/assignRole', {
        user: user,
        role: this.role
      })
      .then(() => {
        this.$store.dispatch('message/update', {
          title: user.username,
          body: `${user.username} user has been updated successfully`,
          class: 'info',
          confirm: false
        }, { root: true });
      })
      .catch((error) => {
        let response = error.response;

        // send error message
        this.$store.dispatch('message/update', {
          title: user.title,
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
