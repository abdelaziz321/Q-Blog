import Vue from 'vue';
window.Vue = Vue;

window._ = require('lodash');
window.$ = window.jQuery = require('jquery');
require('bootstrap');

// api
window.Laravel = {
  baseURL: 'http://127.0.0.1:8000'
}

import axios from 'axios';
import VueAxios from 'vue-axios';
Vue.use(VueAxios, axios);

let apiURL = window.Laravel.baseURL + '/api';
Vue.axios.defaults.baseURL = apiURL;
axios.defaults.headers['X-Requested-With'] = 'XMLHttpRequest';

window.axios = axios;

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
