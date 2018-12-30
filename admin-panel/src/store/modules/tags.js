import axios from 'axios';

// initial state
const state = {
  tags: {},
  tagsSearch: [],
  totalPages: 0
}

// getters
const getters = {
  tags: (state) => {
    return state.tags;
  },
  totalPages: (state) => {
    return state.totalPages;
  },
  tagsSearch: (state) => {
    return state.tagsSearch;
  }
}

// mutations
const mutations = {
  SET_TAGS(state, tags) {
    state.tags = tags;
  },

  SET_TAGS_SEARCH(state, tags) {
    state.tagsSearch = tags;
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

  searchTags({ commit }, query) {
    return axios.get('/tags/search?q=' + query)
      .then((response) => {
        commit('SET_TAGS_SEARCH', response.data);
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
    return axios.post('/admin/tags/' + tag.slug, {
      _method: 'PUT',
      name: tag.name
    })
    .then((response) => {
      commit('UPDATE_TAG', response.data.tag);
      return response;
    });
  },

  // =========================================================================

  deleteTag({dispatch, commit}, tag) {
    return axios.post('/admin/tags/' + tag.slug, {
      '_method': 'DELETE'
    }).then((response) => {
      commit('DELETE_TAG', tag);
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
