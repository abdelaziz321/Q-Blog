<template>
  <div class="message" :class="bgclass" v-show="message.showMessage">

    <!-- message header -->
    <div class="message-header">
      {{ message.title }}
      <button class="close" @click="close">
        <font-awesome-icon icon="times" />
      </button>
    </div>

    <!-- message body -->
    <div class="message-body d-flex">
      <p>
        <strong>{{ message.body }}</strong>
        <ul>
          <li v-for="(errors, item) in message.itemsErrors" :key="item">
            <u>{{ item }}</u> :
            <ul v-for="error in errors" :key="error">
              {{ error }}
            </ul>
          </li>
        </ul>
      </p>
      
      <button 
        v-if="message.confirm"
        @click="proceed"
        class="btn btn-sm"
        :class="[{'disabled': message.waiting}, btnclass]"
      >
        <template v-if="message.waiting">
          <font-awesome-icon icon="spinner" pulse />
        </template>
        <template v-else>
          proceed
        </template>
      </button>

      <span class="clearfix"></span>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    message: function () {
      return this.$store.getters['message/structure'];
    },
    bgclass: function () {
      return 'bg-' + this.message.class;
    },
    btnclass: function () {
      return 'btn-' + this.message.class;
    },
  },


  methods: {
    close: function () {
      this.$store.dispatch('message/close');
      this.$bus.$off('proceed');
    },

    proceed: function () {
      this.$bus.$emit('proceed');
      this.waiting = true;
    }
  }
}
</script>
