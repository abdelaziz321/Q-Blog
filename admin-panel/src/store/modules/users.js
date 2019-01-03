import Vue from 'vue';
import axios from 'axios';

// initial state
const state = {
  user: {},
  users: [],
  usersSearch: [],
  totalPages: 0,
}

// getters
const getters = {
  user: (state) => {
    return state.user;
  },
  users: (state) => {
    return state.users;
  },
  totalPages: (state) => {
    return state.totalPages;
  },
  usersSearch: (state) => {
    return state.usersSearch;
  }
}

// mutations
const mutations = {
  SET_USER(state, user) {
    state.user = user;
  },

  SET_USERS(state, users) {
    state.users = users;
  },

  SET_USERS_SEARCH(state, users) {
    state.usersSearch = users;
  },

  SET_TOTAL_PAGES(state, totalPages) {
    state.totalPages = totalPages;
  },

  UPDATE_USER(state, user) {
    let userIndex = state.users.findIndex((item) => {
      return item.id == user.id;
    });
    Vue.set(state.users, userIndex, user);
  },

  DELETE_USER(state, user) {
    let index = state.users.indexOf(user);
    if (index > -1) {
      state.users.splice(index, 1);
    }
  },
}

// actions
const actions = {

  getAllUsers({commit}, page) {
    axios.get('/admin/users?page=' + page)
    .then((response) => {
      let res = response.data;
      commit('SET_USERS', res.data);

      let totalPages = Math.ceil(res.total / res.per_page);
      commit('SET_TOTAL_PAGES', totalPages);
    });
  },

  // =========================================================================

  getBannedUsers({commit}, page) {
    axios.get('/admin/users/banned?page=' + page)
    .then((response) => {
      let res = response.data;
      commit('SET_USERS', res.data);

      let totalPages = Math.ceil(res.total / res.per_page);
      commit('SET_TOTAL_PAGES', totalPages);
    });
  },

  // =========================================================================

  searchUsers({commit}, query) {
    return axios.get('/admin/users/search?q=' + query)
      .then((response) => {
        commit('SET_USERS_SEARCH', response.data);
      });
  },

  // =========================================================================

  getUser({commit}, user) {
    axios.get('/admin/users/' + user)
    .then((response) => {
      commit('SET_USER', response.data.user)
    });
  },

  // =========================================================================

  assignRole({commit}, payload) {
    axios.post('/admin/users/' + payload.user.slug + '/assign-role', {
      role: payload.role
    })
    .then(() => {
      payload.user.role = payload.role;
      commit('UPDATE_USER', payload.user);
    });
  },

  // =========================================================================

  deleteUser({commit}, user) {
    axios.post('/admin/users/' + user.slug, {
      '_method': 'DELETE'
    })
    .then((response) => {
      commit('DELETE_USER', user);
      return response;
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
