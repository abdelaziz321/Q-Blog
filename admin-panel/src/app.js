import './bootstrap';
import Vue from 'vue';
import axios from 'axios';
import Gate from './gate';
import router from './router.js';

Vue.router = router;

Vue.use(require('@websanova/vue-auth'), {
  auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
  http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
  router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
  tokenDefaultName: 'access_token',
  tokenStore: ['localStorage'],
  authRedirect: {path: '/login'},
  notFoundRedirect: {path: '/404'},
  rolesVar: 'role',
  parseUserData: function (data) {
    return data.user;
  }
});

Vue.prototype.$gate = new Gate();

axios.interceptors.response.use(function (response) {
  // console.log(response);
  return response;
}, function (error) {
  window.console.log(error);
  return Promise.reject(error);
});

import Paginate from 'vuejs-paginate';
Vue.component('paginate', Paginate);

import wysiwyg from "vue-wysiwyg";
Vue.use(wysiwyg, {});

import Breabcrumbs from 'vue-2-breadcrumbs';
Vue.use(Breabcrumbs);

import './admin.js';
import './fontawesome.js';

import store from './store';
import EntryPoint from './components/entry_point';

const EventBus = new Vue();
Object.defineProperties(Vue.prototype, {
  $bus: {
    get: function () {
      return EventBus;
    }
  }
});

// register global components
Vue.component('sidebar', require('./components/partials/sidebar').default);
Vue.component('navbar', require('./components/partials/navbar').default);
Vue.component('foooter', require('./components/partials/footer').default);

Vue.component('modal', require('./components/notifications/modal').default);
Vue.component('message', require('./components/notifications/message').default);


// Vue instance
new Vue({
  el: '#app',
  router,
  store,
  render: h => h(EntryPoint)
});
