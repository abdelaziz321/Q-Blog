// initial state
const state = {
  comments: {},
  totalPages: 0
}

// getters
const getters = {
  comments: (state) => {
    return state.comments;
  },
  totalPages: (state) => {
    return state.totalPages;
  }
}

// mutations
const mutations = {
  SET_COMMENTS(state, comments) {
    state.comments = comments;
  },

  SET_TOTAL_PAGES(state, totalPages) {
    state.totalPages = totalPages;
  },

  DELETE_COMMENT(state, comment) {
    let index = state.comments.indexOf(comment);
    if (index > -1) {
      state.comments.splice(index, 1);
    }
  }
}

// actions
const actions = {

  getAllComments({commit}, page) {
    axios.get('/admin/comments?page=' + page)
    .then((response) => {
      let res = response.data;
      commit('SET_COMMENTS', res.data);

      let totalPages = Math.ceil(res.total / res.per_page);
      commit('SET_TOTAL_PAGES', totalPages);
    });
  },

  // =========================================================================

  getPostComments({commit}, post) {
    axios.get('/admin/posts/' + post + '/comments')
    .then((response) => {
      commit('SET_COMMENTS', response.data);
     });
  },

  // =========================================================================

  deleteComment({commit, dispatch}, comment) {
    axios.post('/admin/comments/' + comment.id, {
      '_method': 'DELETE'
    }).then((response) => {
      // send successful message
      dispatch('message/update', {
        body: response.data.message,
        class: 'success',
        confirm: false
      }, { root: true });

      commit('DELETE_COMMENT', comment);
    }).catch((error) => {
      // send error message
      dispatch('message/update', {
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
