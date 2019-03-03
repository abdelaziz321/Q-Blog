<template>
  <div class="chat text-secondary" :class="{ hide: isChatClosed }">
      <h4 class="header" @click="isChatClosed = !isChatClosed">
        <font-awesome-icon icon="globe" />
        chat with authors
      </h4>

      <ul ref="messages" class="messages">
        <li class="d-flex">
          <div class="messages_status">
            <span v-if="isChatEnded">no more messages</span>
            <button
              v-else
              :class="{'d-none': isMessagesLoading}"
              @click="loadPreviousMessages"
              type="button"
            >load more messages</button>
          </div>
        </li>

        <li
          v-for="message in messages"
          :key="message.id"
          class="d-flex"
          :class="message.user.slug == $auth.user().slug ? '' : 'flex-row-reverse'"
        >
          <img
            alt="avatar"
            :src="`${$baseURL}/storage/users/` +
              (
                typeof message.user === 'undefined' || message.user.avatar === null ?
                'avatar.svg' : message.user.avatar
              )"
          />
          <div class="details">
            <h6>{{ typeof message.user === 'undefined' ? 'someone' : message.user.name }}</h6>
            <p>{{ message.message }}</p>
          </div>
        </li>

      </ul>

      <form class="chat_form d-flex" @submit.prevent="sendMessage">
        <textarea class="form-control send_message" v-model="form.body" placeholder="send a message"></textarea>
        <button class="btn btn-primary send_btn" type="submit">
          <font-awesome-icon icon="paper-plane" />
        </button>
      </form>
    </div>
</template>

<script>

export default {
  name: 'chat',


  data: function () {
    return {
      form: {
        body: ''
      },

      isChatEnded: false,
      isChatClosed: false,
      isMessagesLoading: true
    };
  },


  computed: {
    messages: function () {
      return this.$store.getters['chat/messages'];
    },

		newMessage: function () {
			return this.$store.getters['chat/newMessage'];
		}
  },

	watch: {
    newMessage: function (val) {
			this.$nextTick(() => {
				this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
      });
     }
  },

  created() {
    this.$store.dispatch('chat/getMessages')
	    .then((noMoreMessages) => {
	      this.isChatEnded = noMoreMessages;
	      this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
	      this.$store.dispatch('chat/listenToNewMessages');
	      this.isMessagesLoading = false;
	    });
  },


  methods: {
    loadPreviousMessages() {
      this.isMessagesLoading = true;
      this.$store.dispatch('chat/getMessages')
	      .then((noMoreMessages) => {
	        this.isChatEnded = noMoreMessages;
	        this.isMessagesLoading = false;
	      });
    },

    sendMessage() {
      this.$store.dispatch('chat/send', this.form.body)
				.then(() => {
					this.form.body = '';
					this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
				});
    }
  }
}
</script>
