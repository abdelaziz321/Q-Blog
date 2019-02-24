import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

import chat from './modules/chat.js';

import modal from './modules/modal.js';
import message from './modules/message.js';

import categories from './modules/categories.js';
import tags from './modules/tags.js';
import users from './modules/users.js';
import posts from './modules/posts.js';
import comments from './modules/comments.js';

export default new Vuex.Store({
  modules: {
    chat,

    modal,
    message,

    categories,
    tags,
    users,
    posts,
    comments
  }
});
