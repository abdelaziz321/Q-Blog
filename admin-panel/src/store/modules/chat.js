import axios from 'axios';
import { reject } from 'q';

// initial state
const state = {
  users: new Map()
}

// getters
const getters = {
  users: (state) => {
    return state.users;
  }
}

// mutations
const mutations = {
  SET_USERS(state, users) {
    users.forEach((user) => {
      state.users.set(user.id, {
        name: user.username,
        avatar: user.avatar
      });
    });    
  }
}

// actions
const actions = {
  getUsers({ state, commit }, usersIds) {
    let missidUsers = [];
    usersIds.forEach((id) => {
      if (!state.users.has(id)) {
        missidUsers.push(id);
      }
    });

    if (missidUsers.length === 0) {
      return;
    }

    let query = '';
    missidUsers.forEach((id) => {
      query += `ids[]=${id}&`;
    });
    query = query.substr(0, query.length - 1);
    
    return axios.get('/admin/chat/users?' + query)
    .then((response) => {
      let res = response;
      commit('SET_USERS', res.data);
    })
    .catch(() => {});
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
