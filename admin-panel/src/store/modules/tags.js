// initial state
const state = {
  tags: {},
  totalPages: 0
}

// getters
const getters = {
  tags: (state) => {
    return state.tags;
  },
  totalPages: (state) => {
    return state.totalPages;
  }
}

// mutations
const mutations = {
  SET_TAGS(state, tags) {
    state.tags = tags;
  },

  SET_TOTAL_PAGES(state, totalPages) {
    state.totalPages = totalPages;
  },

  UPDATE_TAG(state, tag) {
    let tagIndex = state.tags.findIndex((item) => {
      return item.id == tag.id;
    });

    tag.total_posts = state.tags[tagIndex].total_posts;
    Vue.set(state.tags, tagIndex, JSON.parse(JSON.stringify(tag)));
  },

  DELETE_TAG(state, tag) {
    let index = state.tags.indexOf(tag);
    if (index > -1) {
      state.tags.splice(index, 1);
    }
  },
}

// actions
const actions = {

  getAllTags({commit}, page) {
    axios.get('/admin/tags?page=' + page)
    .then((response) => {
      let res = response.data;
      commit('SET_TAGS', res.data);

      let totalPages = Math.ceil(res.total / res.per_page);
      commit('SET_TOTAL_PAGES', totalPages);
    });
  },

  // =========================================================================

  getTag({commit}, tag) {
    axios.get('/admin/tags/' + tag)
    .then((response) => {
      commit('SET_TAGS', response.data.tag)
    })
  },

  // =========================================================================

  updateTag ({commit, dispatch}, tag) {
    axios.post('/admin/tags/' + tag.slug, {
      _method: 'PUT',
      name: tag.name
    })
    .then((response) => {
      // send successful message
      dispatch('message/update', {
        title: tag.name,
        body: `${tag.name} tag has been updated successfully`,
        class: 'success',
        confirm: false
      }, { root: true });

      commit('UPDATE_TAG', response.data.tag);
      $('#modal').modal('hide');
    })
    .catch((error) => {
      let response = error.response;
      // send error message
      dispatch('message/update', {
        title: tag.name,
        body: response.data.message,
        itemsErrors: response.data.errors,
        class: 'danger',
        confirm: false
      }, { root: true });
    });
  },

  // =========================================================================

  deleteTag({dispatch, commit}, tag) {
    axios.post('/admin/tags/' + tag.slug, {
      '_method': 'DELETE'
    }).then((response) => {
      // send successful message
      dispatch('message/update', {
        title: tag.title,
        body: response.data.message,
        class: 'success',
        confirm: false
      }, { root: true });

      commit('DELETE_TAG', tag);
    }).catch((error) => {
      // send error message
      dispatch('message/update', {
        title: tag.name,
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
