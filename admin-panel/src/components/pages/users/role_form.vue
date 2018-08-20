<template>
  <form class=" mt-1" id="form-modal">

    <!-- roles select -->
    <div class="form-group">
      <label class="label-form">Role:</label>
      <select class="form-control" v-model="userForm.role">
        <option value="admin">admin</option>
        <option value="author">author</option>
        <option value="regular user">regular user</option>
        <option value="banned user">banned user</option>
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
      userForm: {}
    };
  },


  created: function () {
    this.userForm = JSON.parse(JSON.stringify(this.formData));
  },


  methods: {
    save () {
      this.$store.dispatch('users/assignRole', this.userForm);
    }
  }

}
</script>
