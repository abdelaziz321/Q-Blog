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
            <button v-else @click="loadMessages" type="button" >load more messages</button>
          </div>
        </li>

        <li
          v-for="message in messages" 
          :key="message.id"
          class="d-flex"
          :class="message.user_id == $auth.user().id ? '' : 'flex-row-reverse'"
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
            <p>{{ message.body }}</p>
          </div>
        </li>

      </ul>

      <form class="chat_form d-flex" @submit.prevent="send">
        <textarea class="form-control send_message" v-model="form.message" placeholder="send a message"></textarea>
        <button class="btn btn-primary send_btn" type="submit">
          <font-awesome-icon icon="paper-plane" />
        </button>
      </form>
    </div>
</template>

<script>
import { db } from '@/config/db.js';

export default {
  name: 'chat',


  data: function () {
    return {
      form: {
        message: ''
      },

      isChatEnded: false,
      isChatClosed: false,

      lastMessage: null,
      messages: [],
    };
  },


  created() {
    db.collection('messages')
      .orderBy("created", "desc")
      .limit(5)
      .get()
    .then((messages) => {
      this.addMessages(messages)
      .then(() => {
        this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
      });
    });
  },


  methods: {
    /**
     * load more messages when the user scroll up
     */
    loadMessages() {
      db.collection('messages')
        .orderBy("created", "desc")
        .startAfter(this.lastMessage)
        .limit(5)
        .get()
      .then((messages) => {
        this.addMessages(messages);
      });
    },

    /**
     * add the retrieved messages to the chat
     */
    addMessages(messages) {
      const messagesIds = [];
      const usersIds = new Set();

      messages.forEach(message => {
        this.messages.unshift({
          id: message.id,
          body: message.data().body,
          user_id: message.data().user_id
        });

        messagesIds.push(message.id);
        usersIds.add(message.data().user_id);
      });

      if (messages.docs.length < 5) {
        this.isChatEnded = true;
      }

      this.lastMessage = messages.docs[messages.docs.length - 1];
      return this.setChatUsers(messagesIds, usersIds);  
    },

    /**
     * get the chat users from the API if not exist in our $store
     * then add them to the chat messages
     * 
     * @param array messagesIds
     * @param Set   usersIds
     */
    setChatUsers(messagesIds, usersIds) {
      return this.$store.dispatch('chat/getUsers', usersIds)
      .then(() => {
        let users = this.$store.getters['chat/users'];
        
        this.messages.forEach((message, index) => {
          message.user = users.get(message.user_id);
          Vue.set(this.messages, index, message);
        });
      });
    }
  }
}
</script>