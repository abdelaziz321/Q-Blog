import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import App from '@/views/App';
import Login from '@/views/Login';
import NotFound from '@/views/NotFound';


const routes = [
  {
    path: '/login',
    component: Login,
    meta: {
      auth: false
    }
  },
  {
    path: '/',
    component: App,
    redirect: '/dashboard',
    meta: {
      auth: ['admin', 'moderator', 'author'],
    },
    children: [
      // ---------- dashboard routes
      {
        path: '/dashboard',
        component: () => import('@/components/dashboard'),
        name: 'dashboard',
        meta: {
          breadcrumb: 'Dashboard'
        }
      },

      // ---------- categories routes
      {
        path: '/categories',
        component: () => import('@/components/categories/categories'),
        meta: {
          breadcrumb: 'Categories'
        },
        children: [
          {
            path: '',
            component: () => import('@/components/categories/category_table'),
          },
          {
            path: ':category/posts',
            component: () => import('@/components/categories/category'),
            meta: {
              breadcrumb: (routeParams) => `${routeParams.category}`
            }
          },
        ]
      },

      // ---------- tags routes
      {
        path: '/tags',
        component: () => import('@/components/tags/tags'),
        meta: {
          breadcrumb: 'tags'
        },
        children: [
          {
            path: '',
            component: () => import('@/components/tags/tag_table'),
          },
          {
            path: '/tags/:tag/posts',
            component: () => import('@/components/tags/tag'),
            meta: {
              breadcrumb: (routeParams) => `${routeParams.tag}`
            }
          }
        ],
      },

      // ---------- users routes
      {
        path: '/users',
        component: () => import('@/components/users/users'),
        meta: {
          breadcrumb: 'users'
        },
        children: [
          {
            path: '',
            component: () => import('@/components/users/all_users'),
          },
          {
            path: 'banned',
            component: () => import('@/components/users/banned_users'),
            meta: {
              breadcrumb: 'banned'
            }
          },
          {
            path: '/users/:user/posts',
            component: () => import('@/components/users/user'),
            meta: {
              breadcrumb: (routeParams) => `${routeParams.user}`
            }
          }
        ],
      },

      // ---------- posts routes
      {
        path: '/posts',
        component: () => import('@/components/posts/posts'),
        meta: {
          breadcrumb: 'posts'
        },
        children: [
          {
            path: '',
            component: () => import('@/components/posts/all_posts'),
          },
          {
            path: 'unpublished',
            component: () => import('@/components/posts/unpublished_posts'),
            meta: {
              breadcrumb: 'unpublished posts'
            }
          },
          {
            path: 'new-post',
            component: () => import('@/components/posts/form'),
            meta: {
              breadcrumb: 'new posts'
            }
          },
          {
            path: 'update-post/:post',
            component: () => import('@/components/posts/form'),
            meta: {
              breadcrumb: (routeParams) => `${routeParams.post}`
            }
          },
          {
            path: ':post',
            component: () => import('@/components/posts/post'),
            meta: {
              breadcrumb: (routeParams) => `${routeParams.post}`
            }
          }
        ]
      },

      // ---------- comments routes
      {
        path: '/comments',
        component: () => import('@/components/comments/comments'),
        meta: {
          breadcrumb: 'comments'
        }
      },
    ]
  },

  // ---------- 404 route
  {
    path: "/404",
    alias: "*",
    component: NotFound
  }
];

const router = new VueRouter({
  mode: 'history',
  base: 'admin-panel',
  linkActiveClass: 'active',
  routes,
});

export default router;
