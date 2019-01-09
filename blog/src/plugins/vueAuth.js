import axios from 'axios';
import VueAxios from 'vue-axios';

// leave the export, even if you don't use it
export default ({ app, router, Vue }) => {
  Vue.router = router;
  Vue.use(VueAxios, axios);
  Vue.prototype.router = router;

  Vue.use(require('@websanova/vue-auth'), {
    auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),

    tokenDefaultName: 'access_token',
    tokenStore: ['localStorage'],
    authRedirect: { path: '/login' },
    notFoundRedirect: { path: '/404' },
    rolesVar: 'role',
    refreshData: {
      enabled: false, interval: 0
    },
    parseUserData: function (data) {
      return data.user;
    }
  });
}
