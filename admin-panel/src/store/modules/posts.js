import axios from 'axios';

// initial state
const state = {
  post: {},

  posts: [],
  totalPages: 0
}

// getters
const getters = {
  post: (state) => {
    return state.post;
  },
  posts: (state) => {
    return state.posts;
  },
  totalPages: (state) => {
    return state.totalPages;
  }
}

// mutations
const mutations = {
  SET_POST(state, post) {
    state.post = post;
  },

  SET_POSTS(state, posts) {
    state.posts = posts;
  },

  SET_ACTION(state, action) {
    state.action = action;
  },

  SET_TOTAL_PAGES(state, totalPages) {
    state.totalPages = totalPages;
  },

  ADD_POST(state, post) {
    state.posts.push(JSON.parse(JSON.stringify(post)));
  },

  UPDATE_POSTS(state, post) {
    let postIndex = state.posts.findIndex((item) => {
      return item.id == post.id;
    });
    Vue.set(state.posts, postIndex, JSON.parse(JSON.stringify(post)));
  },

  DELETE_POSTS(state, post) {
    let index = state.posts.indexOf(post);
    if (index > -1) {
      state.posts.splice(index, 1);
    }
  }
}

// actions
const actions = {

  getAllPosts({commit}, page) {
    axios.get('/admin/posts?page=' + page)
    .then((response) => {
      let res = response.data;
      commit('SET_POSTS', res.data);

      let totalPages = Math.ceil(res.total / res.per_page);
      commit('SET_TOTAL_PAGES', totalPages);
    });
  },

  // =========================================================================

  getUnpublishedPosts({commit}, page) {
    axios.get('/admin/posts/unpublished?page=' + page)
    .then((response) => {
      let res = response.data;
      commit('SET_POSTS', res.data);

      let totalPages = Math.ceil(res.total / res.per_page);
      commit('SET_TOTAL_PAGES', totalPages);
    });
  },

  // =========================================================================

  getPost({commit}, post) {
    return new Promise((resolve, reject) => {
      axios.get('/admin/posts/' + post)
      .then((response) => {
        commit('SET_POST', response.data.post);
        resolve();
      }).catch(() => {
        reject();
      });
    });
  },

  // =========================================================================

  getCategoryPosts({commit}, payload) {
    axios.get('/admin/categories/' + payload.category + '/posts?page=' + payload.page)
    .then((response) => {
       let res = response.data;
       commit('SET_POSTS', res.data);

       let totalPages = Math.ceil(res.total / res.per_page);
       commit('SET_TOTAL_PAGES', totalPages);
     });
  },

  // =========================================================================

  getTagPosts({commit}, payload) {
    axios.get('/admin/tags/' + payload.tag + '/posts?page=' + payload.page)
    .then((response) => {
       let res = response.data;
       commit('SET_POSTS', res.data);

       let totalPages = Math.ceil(res.total / res.per_page);
       commit('SET_TOTAL_PAGES', totalPages);
     });
  },

  // =========================================================================

  getUserPosts({commit}, payload) {
    axios.get('/admin/users/' + payload.user + '/posts?page=' + payload.page)
    .then((response) => {
       let res = response.data;
       commit('SET_POSTS', res.data);

       let totalPages = Math.ceil(res.total / res.per_page);
       commit('SET_TOTAL_PAGES', totalPages);
     });
  },

  // =========================================================================

  createPost({commit, dispatch}, post) {
    let tagsNames = post.tags.map((tag) => {
      return tag.slug;
    });

    let formData = new FormData();
    formData.append('title', post.title);
    formData.append('body', post.body);
    formData.append('caption', post.caption);
    formData.append('category_id', post.category.id);
    for (var i = 0; i < tagsNames.length; i++) {
        formData.append('tags[]', tagsNames[i]);
    }

    axios.post('/admin/posts', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    .then((response) => {
      // send successful message
      dispatch('message/update', {
        title: post.title,
        body: `${post.title} post has been added successfully`,
        class: 'success',
        confirm: false
      }, { root: true });
    })
    .catch((error) => {
      let response = error.response;

      // send error message
      dispatch('message/update', {
        title: post.title,
        body: response.data.message,
        itemsErrors: response.data.errors,
        class: 'danger',
        confirm: false
      }, { root: true });
    });
  },

  // =========================================================================

  updatePost ({commit, dispatch}, post) {
    return new Promise((resolve, reject) => {

      let tagsNames = post.tags.map((tag) => {
        return tag.slug;
      });

      let formData = new FormData();
      formData.append('_method', 'PUT');
      formData.append('title', post.title);
      formData.append('body', post.body);
      formData.append('caption', post.caption);
      formData.append('category_id', post.category.id);
      for (var i = 0; i < tagsNames.length; i++) {
          formData.append('tags[]', tagsNames[i]);
      }

      axios.post('/admin/posts/' + post.slug, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      .then((response) => {
        // send successful message
        dispatch('message/update', {
          title: post.title,
          body: `${post.title} post has been updated successfully`,
          class: 'success',
          confirm: false
        }, { root: true });

        resolve(response.data.post.slug);
      })
      .catch((error) => {
        let response = error.response;

        // send error message
        dispatch('message/update', {
          title: post.title,
          body: response.data.message,
          itemsErrors: response.data.errors,
          class: 'danger',
          confirm: false
        }, { root: true });

        reject();
      });
    });
  },

  // =========================================================================

  deletePost({commit, dispatch}, post) {
    axios.post('/admin/posts/' + post.slug, {
      '_method': 'DELETE'
    }).then((response) => {
      // send successful message
      dispatch('message/update', {
        title: post.title,
        body: response.data.message,
        class: 'success',
        confirm: false
      }, { root: true });

      commit('DELETE_POSTS', post);
    }).catch((error) => {
      // send error message
      dispatch('message/update', {
        title: post.title,
        class: 'danger',
        body: error.response.data.message,
        errors: error.response.data.errors,
        confirm: false
      }, { root: true });
    });
  },

  // =========================================================================

  publishing({ dispatch, commit }, payload) {
    axios.post('/admin/posts/publishing/' + payload.post.slug + '?action=' + payload.action)
      .then((response) => {
        dispatch('message/update', {
          title: post.title,
          body: response.data.message,
          class: 'success',
          confirm: false
        }, { root: true });

        post.published = 1;
        post.published_at = 'Just now';
        commit('SET_POST', post);
      });
  },

  // =========================================================================

  assignTags({dispatch, commit}, post) {
    let tagsNames = post.tags.map((tag) => {
      return tag.slug;
    });

    let formData = new FormData();
    for (var i = 0; i < tagsNames.length; i++) {
        formData.append('tags[]', tagsNames[i]);
    }

    axios.post('/admin/posts/tags/' + post.slug, formData)
    .then((response) => {
      dispatch('message/update', {
        title: post.title,
        body: 'post\'s tags have been updated successfully',
        class: 'success',
        confirm: false
      }, { root: true });

      commit('SET_POST', response.data.post);
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
