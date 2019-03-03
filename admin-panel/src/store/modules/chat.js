import axios from 'axios';
import Echo from 'laravel-echo';

const Pusher = require('pusher-js');

const echo = new Echo({
  broadcaster: 'pusher',
  key: '468adb0d5808c1',
  wsHost: '127.0.0.1',
  httpHost: '127.0.0.1',
  wsPort: 6001,
  disableStats: true,
  encrypted: false,
  auth: {
    headers: {
      'Authorization': 'Bearer ' + localStorage.getItem('access_token')
    }
  },
  enabledTransports: ['ws', 'wss']
});

echo.connector.pusher.config.authEndpoint = `http://127.0.0.1:8000/broadcasting/auth`;

const LIMIT = 10;

// initial state
const state = {
  messages: [],
  firstMessage: 0,  // refers to the message id at the top of our chat

  // we will change its value each time a new message retrieved
  newMessageFlag: false
}

// getters
const getters = {
  messages: (state) => {
    return state.messages;
  },

  newMessage: (state) => {
    return state.newMessageFlag;
  }
}

// mutations
const mutations = {
  ADD_MESSAGES(state, messages) {
    messages.forEach((message) => {
      state.messages.unshift(message);
    });
  },

  UPDATE_FIRST_MESSAGE(state, messages) {
    state.firstMessage = messages[messages.length - 1].id;
  },

  ADD_MESSAGE(state, message) {
    state.messages.push(message);
  },

  ADD_MESSAGE(state, message) {
    state.messages.push(message);
    state.newMessageFlag = !state.newMessageFlag;
  }
}

// actions
const actions = {
  getMessages({ state, commit, dispatch }) {
    let params = `?limit=${LIMIT}`;

    if (state.firstMessage !== 0) {
      params += `&id=${state.firstMessage}`
    }

    return axios.get(`/admin/messages${params}`)
      .then((response) => {
        let messages = response.data;

        commit('ADD_MESSAGES', messages);
        commit('UPDATE_FIRST_MESSAGE', messages);

        return messages.length < LIMIT;
      });
  },

  listenToNewMessages({ state, commit, dispatch }) {
    echo.private('authors-chat')
      .listen('.message.sent', (message) => {
        commit('ADD_MESSAGE', message);
      });
  },

  send({commit}, message) {
    return axios.post(`/admin/messages`, {
      message: message
    },
    {
      headers: {
        "X-Socket-Id": echo.socketId(),
      }
    })
    .then((response) => {
      let message = response.data;
      commit('ADD_MESSAGE', message);
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
