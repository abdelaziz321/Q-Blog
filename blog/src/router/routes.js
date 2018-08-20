import Login from 'pages/auth/login';
import Register from 'pages/auth/register';
import Layout from 'layouts/layout.vue';

import PostsIndex from 'pages/posts/index';
import PostsList from 'pages/posts/list';
import SinglePost from 'pages/posts/single';

import UpdateUser from 'pages/users/update';

import About from 'pages/about';
import Contact from 'pages/contact';


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
        component: PostsIndex,
        children: [
          {
            path: '',
            component: PostsList
          },
          {
            path: ':post',
            component: SinglePost
          }
        ]
      },

      // user routes
      {
        path: 'users/update-profile',
        component: UpdateUser,
        meta: {
          auth: true
        },
        // children: [
        //   // TODO: show the user activities
        //   // {
        //   //   path: ':user',
        //   //   component: User
        //   // },
        //   {
        //     path: 'update-profile',
        //     component: UpdateUser
        //   }
        // ]
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
