import Layout     from 'layouts/layout.vue';

import About      from 'pages/about';
import Contact    from 'pages/contact';
import Posts      from 'pages/posts';
import UserUpdate from 'pages/user_update';
import Login      from 'pages/auth/login';
import Register   from 'pages/auth/register';


const routes = [
  {
    path: '/',
    component: Layout,
    children: [
      // post routes
      {
        path: '',
        redirect: 'posts'
      },
      {
        path: 'posts',
        component: Posts,
        children: [
          {
            path: '',
            component: () => import('components/post_list')
          },
          {
            path: ':post',
            component: () => import('components/post_single')
          }
        ]
      },

      // user routes
      {
        path: 'users/update-profile',
        component: UserUpdate,
        meta: {
          auth: true
        }
      },

      // about and contact
      {
        path: 'about',
        component: About
      },
      {
        path: 'contact',
        component: Contact
      },

      // auth routes
      {
        path: 'login',
        component: Login,
        meta: {
          auth: false
        },
      },
      {
        path: 'register',
        component: Register,
        meta: {
          auth: false
        },
      }
    ]
  }
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  });
}

export default routes;
