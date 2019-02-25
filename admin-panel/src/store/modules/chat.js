import axios from 'axios';
import { db } from '@/config/db.js';
import firebase from 'firebase/app';

const LIMIT = 5;

// initial state
const state = {
  messages: [],
  firstMessage: null,  // refers to the message at the top of our chat
  lastMessage: null,   // refers to the message at the bottom of our chat
  
  users: new Map()
}

// getters
const getters = {
  messages: (state) => {
    return state.messages;
  }
}

// mutations
const mutations = {
  ADD_USERS(state, users) {
    users.forEach((user) => {
      state.users.set(user.id, {
        name: user.username,
        avatar: user.avatar
      });
    });    
  },
  
  ADD_MESSAGES(state, messages) {
    messages.forEach((message) => {   // (doc)
      state.messages.unshift({
        id: message.id,
        body: message.data().body,
        user_id: message.data().user_id
      });
    });
  },

  ADD_MESSAGE(state, message) {
    state.messages.push({
      id: message.id,
      body: message.data().body,
      user_id: message.data().user_id
    })
  },

  // TODO: only add the user for the retrieved messages
  UPDATE_MESSAGES_USERS(state, messages) {
    state.messages.forEach((message, index) => {
      if (typeof message.user !== 'undefined') {
        return;
      }

      message.user = state.users.get(message.user_id);
      Vue.set(state.messages, index, message);
    });
  },

  UPDATE_FIRST_MESSAGE(state, messages) {
    state.firstMessage = messages.docs[messages.docs.length - 1];
  },
  
  UPDATE_LAST_MESSAGE(state, messages) {
    state.lastMessage = messages.docs[0];
  }
}

// actions
const actions = {
  getMessages({ state, commit, dispatch }) {
    let query = db.collection('messages')
      .orderBy("created", "desc");

    if (state.firstMessage !== null) {
      query = query.startAfter(state.firstMessage);
    }

    return query.limit(LIMIT).get()
      .then((messages) => {     // querySnapshot
        commit('ADD_MESSAGES', messages);

        commit('UPDATE_LAST_MESSAGE', messages);
        commit('UPDATE_FIRST_MESSAGE', messages);

        dispatch('getUsers', messages);
        return messages.docs.length < LIMIT;
      });
  },

  // TODO: react to every new message and notify somehow to our user
  getNewMessages({ commit, dispatch }) {
    db.collection("messages")
      .orderBy("created", "asc")
      .startAfter(state.lastMessage)
      .onSnapshot(function (snapshot) {
        snapshot.docChanges().forEach(function (change) {
          if (change.type === "added") {
            let message = change.doc;
            if (message.exists) {
              commit('ADD_MESSAGE', message);
              dispatch('getUsers', [message]);
            }
          }
        });
      });
  },

  getUsers({ state, commit }, messages) {
    // get the users ids from the retrieved messages
    let usersIds = new Set();
    messages.forEach((message) => {
      usersIds.add(message.data().user_id);
    });

    // get the users ids that we dont have them in the current state
    let missidUsers = [];
    usersIds.forEach((id) => {
      if (!state.users.has(id)) {
        missidUsers.push(id);
      }
    });

    // there is no need to get users from API,
    // just update messages users from our Map
    if (missidUsers.length === 0) {
      commit('UPDATE_MESSAGES_USERS', messages);
      return;
    }

    // build the query to get the missed users from our API
    let query = '';
    missidUsers.forEach((id) => {
      query += `ids[]=${id}&`;
    });
    query = query.substr(0, query.length - 1);
    
    return axios.get('/admin/chat/users?' + query)
    .then((response) => {
      let res = response;
      commit('ADD_USERS', res.data);
      commit('UPDATE_MESSAGES_USERS', messages);
    });
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
