import Vue from 'vue';
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
      return item.id === post.id;
    });
    Vue.set(state.posts, postIndex, JSON.parse(JSON.stringify(post)));
  },

  DELETE_POSTS(state, post) {
    let postIndex = state.posts.findIndex((item) => {
      return item.id === post.id;
    });

    if (postIndex > -1) {
      state.posts.splice(postIndex, 1);
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
    return axios.get('/admin/posts/' + post)
    .then((response) => {
      commit('SET_POST', response.data.post);
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

  createPost({}, post) {
    let tagsNames = post.tags.map((tag) => {
      return tag.name;
    });

    let formData = new FormData();
    formData.append('title', post.title);
    formData.append('body', post.body);
    formData.append('caption', post.caption);
    formData.append('category_id', post.category.id);
    for (var i = 0; i < tagsNames.length; i++) {
        formData.append('tags[]', tagsNames[i]);
    }
    
    return axios.post('/admin/posts', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    .then((response) => {
      return response.data.post.slug;
    })
  },

  // =========================================================================

  updatePost ({}, post) {
    let tagsNames = post.tags.map((tag) => {
      return tag.name;
    });

    let formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('title', post.title);
    formData.append('body', post.body);
    
    if (post.caption !== undefined) {
      formData.append('caption', post.caption);
    }
    
    formData.append('category_id', post.category.id);
    for (var i = 0; i < tagsNames.length; i++) {
        formData.append('tags[]', tagsNames[i]);
    }

    return axios.post('/admin/posts/' + post.slug, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    .then((response) => {
      return response.data.post.slug;
    })
  },

  // =========================================================================

  deletePost({commit}, post) {
    return axios.post('/admin/posts/' + post.slug, {
      '_method': 'DELETE'
    })
    .then((response) => {
      commit('DELETE_POSTS', post);
      return response.data.message;
    });    
  },

  // =========================================================================

  publishing({commit}, payload) {
    return axios.post('/admin/posts/' + payload.post.slug + '/publishing?action=' + payload.action)
      .then((response) => {
        switch (payload.action) {
          case 'publish':
            payload.post.published = 1;
            payload.post.published_at = 'Just now';
            break;
          case 'unpublish':
            payload.post.published = 0;
            payload.post.published_at = '';
            break;
        }

        commit('SET_POST', payload.post);
        return response;
      });
  },

  // =========================================================================

  assignTags({commit}, post) {
    let tagsNames = post.tags.map((tag) => {
      return tag.name;
    });

    let formData = new FormData();
    for (var i = 0; i < tagsNames.length; i++) {
        formData.append('tags[]', tagsNames[i]);
    }

    return axios.post('/admin/posts/' + post.slug + '/assign-tags', formData)
    .then((response) => {
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
