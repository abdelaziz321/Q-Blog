<template>
  <div class="chat text-secondary">
      <h4 class="header">
        <font-awesome-icon icon="globe" />
        chat with authors
      </h4>

      <ul class="messages">
        <li 
          v-for="message in messages" 
          :key="message.id"
          class="d-flex"
          :class="message.user_id != 5 ? 'flex-row-reverse' : ''"
        >
          <img :src="message.avatar" alt="user"/>
          <div class="details">
            <h6>{{ message.username }}</h6>
            <p>{{ message.body }}</p>
          </div>
        </li>
      </ul>

      <form class="chat_form d-flex" @submit.prevent="send">
        <textarea class="form-control send_message" v-model="message" placeholder="send a message"></textarea>
        <button class="btn btn-primary send_btn" type="submit">
          <font-awesome-icon icon="paper-plane" />
        </button>
      </form>
    </div>
</template>

<script>
// import { db } from '@/config/db.js';

export default {
  name: 'chat',

  data: function () {
    return {
      messages: [
        {
          id: 1,
          user_id: 3,
          avatar: this.$baseURL + '/storage/users/avatar.svg',
          username: 'name of the user1',
          body: 'first hello hello hello hello message hello first hello hello hello hello message hello '
        },
        {
          id: 2,
          user_id: 5,
          avatar: this.$baseURL + '/storage/users/avatar.svg',
          username: 'this is me',
          body: 'second message, msaa msaaaaa'
        },
        {
          id: 3,
          user_id: 3,
          avatar: this.$baseURL + '/storage/users/avatar.svg',
          username: 'name of the user1',
          body: '3rd message, hello world first hello hello hello hello message hello '
        },
        {
          id: 4,
          user_id: 3,
          avatar: this.$baseURL + '/storage/users/avatar.svg',
          username: 'name of the user1',
          body: '4th message, saaaaal7eeeeeer'
        },
        {
          id: 5,
          user_id: 4,
          avatar: this.$baseURL + '/storage/users/avatar.svg',
          username: 'name of the user2',
          body: '5th message, this is a message'
        },
        {
          id: 6,
          user_id: 1,
          avatar: this.$baseURL + '/storage/users/avatar.svg',
          username: 'name of the user3',
          body: '6th message, this is another message'
        },
        {
          id: 7,
          user_id: 5,
          avatar: this.$baseURL + '/storage/users/avatar.svg',
          username: 'this is me',
          body: '7th message, hello world first hello hello hello hello message hello '
        },
        {
          id: 8,
          user_id: 5,
          avatar: this.$baseURL + '/storage/users/avatar.svg',
          username: 'this is me',
          body: '8th message, hello world this is good'
        },
      ]
    };
  },


  // firestore: function () {
  //   return {
  //     messages: db.collection('messages')
  //   };
  // }
}
</script>
