<template>
  <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content bg-primary">

        <!-- modal header -->
        <div class="modal-header">
          <h4 class="modal-title">{{ modal.title }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
              <font-awesome-icon icon="times" />
            </span>
          </button>
        </div>

        <!-- modal body -->
        <div class="modal-body">
          <component
            v-if="modal.show"
            :is="modal.component"
            :action="modal.action"
            :formData="modal.formData"
            :key="modal.title"
          ></component>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    modal: function () {
      return this.$store.getters['modal/structure'];
    }
  }
}
</script>
