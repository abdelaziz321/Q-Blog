// initial state
const state = {
  user: {},

  users: [],
  totalPages: 0
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

  getUser({commit}, user) {
    axios.get('/admin/users/' + user)
    .then((response) => {
      commit('SET_USER', response.data.user)
    });
  },

  // =========================================================================

  banUser({dispatch, commit}, user) {
    axios.post('/admin/users/ban/' + user.slug)
    .then((response) => {
      dispatch('message/update', {
        title: user.username,
        body: `${user.username} user has been banned`,
        class: 'info',
        confirm: false
      }, { root: true });

      user.role = 'banned user';
      commit('UPDATE_USER', user);
    })
    .catch((error) => {
      let response = error.response;
      // send error message
      dispatch('message/update', {
        title: user.username,
        body: response.data.message,
        itemsErrors: response.data.errors,
        class: 'danger',
        confirm: false
      }, { root: true });
    });
  },

  // =========================================================================

  unbanUser({dispatch, commit}, user) {
    axios.post('/admin/users/unban/' + user.slug)
    .then((response) => {
      dispatch('message/update', {
        title: user.username,
        body: `${user.username} user has been unbanned successfully`,
        class: 'success',
        confirm: false
      }, { root: true });
    });

    user.role = 'regular user';
    commit('UPDATE_USER', user);
  },

  // =========================================================================

  assignRole({dispatch, commit}, user) {
    axios.post('/admin/users/assign-role/' + user.slug, {
      role: user.role
    })
    .then((response) => {
      dispatch('message/update', {
        title: user.username,
        body: `${user.username} user has been updated successfully`,
        class: 'info',
        confirm: false
      }, { root: true });

      commit('UPDATE_USER', user);
    })
    .catch((error) => {
      let response = error.response;

      // send error message
      dispatch('message/update', {
        title: user.title,
        body: response.data.message,
        itemsErrors: response.data.errors,
        class: 'danger',
        confirm: false
      }, { root: true });
    });
  },

  // =========================================================================

  deleteUser({dispatch, commit}, user) {
    axios.post('/admin/users/' + user.slug, {
      '_method': 'DELETE'
    }).then((response) => {
      // send successful message
      dispatch('message/update', {
        title: user.username,
        body: response.data.message,
        class: 'success',
        confirm: false
      }, { root: true });

      commit('DELETE_USER', user);
    }).catch((error) => {
      // send error message
      dispatch('message/update', {
        title: user.username,
        class: 'danger',
        body: error.response.data.message,
        errors: error.response.data.errors,
        confirm: false
      }, { root: true });
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
