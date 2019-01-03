import Vue from 'vue';
import axios from 'axios';

// initial state
const state = {
  category: {},
  categories: [],
  search: [],
  totalPages: 0,
}

// getters
const getters = {
  category: (state) => {
    return state.category;
  },
  categories: (state) => {
    return state.categories;
  },
  totalPages: (state) => {
    return state.totalPages;
  },
  search: (state) => {
    return state.search;
  }
}

// mutations
const mutations = {
  SET_CATEGORY(state, category) {
    state.category = category;
  },

  SET_CATEGORIES(state, categories) {
    state.categories = categories;
  },

  SET_CATEGORIES_SEARCH(state, categories) {
    state.search = categories;
  },

  SET_TOTAL_PAGES(state, totalPages) {
    state.totalPages = totalPages;
  },

  ADD_CATEGORY(state, category) {
    state.categories.push(JSON.parse(JSON.stringify(category)));
  },

  UPDATE_CATEGORY(state, category) {
    let categoryIndex = state.categories.findIndex((item) => {
      return item.id == category.id;
    });
    category.total_posts = state.categories[categoryIndex].total_posts;
    Vue.set(state.categories, categoryIndex, JSON.parse(JSON.stringify(category)));
  },

  DELETE_CATEGORY(state, category) {   
    let index = state.categories.indexOf(category);
    if (index > -1) {
      state.categories.splice(index, 1);
    }
  },
}

// actions
const actions = {

  getAllCategories ({commit}, page) {
    axios.get('/admin/categories?page=' + page).then((response) => {
      let res = response.data;
      commit('SET_CATEGORIES', res.data);

      let totalPages = Math.ceil(res.total / res.per_page);
      commit('SET_TOTAL_PAGES', totalPages);
    });
  },

  // =========================================================================

  searchCategories({ commit }, query) {
    return axios.get('/categories/search?q=' + query)
      .then((response) => {
        commit('SET_CATEGORIES_SEARCH', response.data);
      });
  },

  // =========================================================================

  getCategory({commit}, slug) {
    axios.get('/admin/categories/' + slug)
    .then((response) => {
      commit('SET_CATEGORY', response.data.category);
    });
  },

  // =========================================================================

  createCategory ({commit}, category) {
    let moderatorId = null;
    if (category.moderator != null) {
      moderatorId = category.moderator.id;
    }

    return axios.post('/admin/categories', {
      title: category.title,
      description: category.description,
      moderator: moderatorId
    })
    .then((response) => {
      commit('ADD_CATEGORY', response.data.category);
    });
  },

  // =========================================================================

  updateCategory ({commit}, category) {
    let moderatorId = null;
    if (category.moderator != null) {
      moderatorId = category.moderator.id;
    }
    return axios.post('/admin/categories/' + category.slug, {
      _method: 'PUT',
      title: category.title,
      description: category.description,
      moderator: moderatorId
    })
    .then((response) => {
      commit('UPDATE_CATEGORY', response.data.category);
    });
  },

  // =========================================================================

  deleteCategory({commit}, category) {
    return axios.post('/admin/categories/' + category.slug, {
      '_method': 'DELETE'
    })
    .then((response) => {
      commit('DELETE_CATEGORY', category);
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
