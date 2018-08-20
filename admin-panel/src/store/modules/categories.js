// initial state
const state = {
  category: {},

  categories: [],
  totalPages: 0
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

  getCategory({commit}, slug) {
    axios.get('/admin/categories/' + slug)
    .then((response) => {
      commit('SET_CATEGORY', response.data.category);
    });
  },

  // =========================================================================

  createCategory ({commit, dispatch}, category) {
    let moderatorId = null;
    if (category.moderator != null) {
      moderatorId = category.moderator.id;
    }

    axios.post('/admin/categories', {
      title: category.title,
      description: category.description,
      moderator: moderatorId
    })
    .then((response) => {
      // send successful message
      dispatch('message/update', {
        title: category.title,
        body: `${category.title} category has been added successfully`,
        class: 'success',
        confirm: false
      }, { root: true });

      commit('ADD_CATEGORY', response.data.category);
      $('#modal').modal('hide');
    })
    .catch((error) => {
      let response = error.response;

      // send error message
      dispatch('message/update', {
        title: category.title,
        body: response.data.message,
        itemsErrors: response.data.errors,
        class: 'danger',
        confirm: false
      }, { root: true });
    });
  },

  // =========================================================================

  updateCategory ({commit, dispatch}, category) {
    let moderatorId = null;
    if (category.moderator != null) {
      moderatorId = category.moderator.id;
    }
    axios.post('/admin/categories/' + category.slug, {
      _method: 'PUT',
      title: category.title,
      description: category.description,
      moderator: moderatorId
    })
    .then((response) => {
      // send successful message
      dispatch('message/update', {
        title: category.title,
        body: `${category.title} category has been updated successfully`,
        class: 'success',
        confirm: false
      }, { root: true });

      commit('UPDATE_CATEGORY', response.data.category);
      $('#modal').modal('hide');
    })
    .catch((error) => {
      let response = error.response;

      // send error message
      dispatch('message/update', {
        title: category.title,
        body: response.data.message,
        itemsErrors: response.data.errors,
        class: 'danger',
        confirm: false
      }, { root: true });
    });
  },

  // =========================================================================

  deleteCategory({commit, dispatch}, category) {
    axios.post('/admin/categories/' + category.slug, {
      '_method': 'DELETE'
    }).then((response) => {
      // send successful message
      dispatch('message/update', {
        title: category.title,
        body: response.data.message,
        class: 'success',
        confirm: false
      }, { root: true });

      commit('DELETE_CATEGORY', category);
    }).catch((error) => {
      // send error message
      dispatch('message/update', {
        title: category.title,
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
