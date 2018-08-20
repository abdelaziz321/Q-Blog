import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import App from './components/App';
import Login from './components/pages/auth/login';

import Dashboard from './components/pages/dashboard';

import Categories from './components/pages/categories/categories';
import CategoryTable from './components/pages/categories/category_table';
import Category from './components/pages/categories/category';

import Tags from './components/pages/tags/tags';
import TagTable from './components/pages/tags/tag_table';
import Tag from './components/pages/tags/tag';

import Users from './components/pages/users/users';
import AllUsers from './components/pages/users/all_users';
import BannedUsers from './components/pages/users/banned_users';
import User from './components/pages/users/user';

import Posts from './components/pages/posts/posts';
import AllPosts from './components/pages/posts/all_posts';
import UnpublishedPosts from './components/pages/posts/unpublished_posts';
import Post from './components/pages/posts/post';
import PostForm from './components/pages/posts/form';

import Comments from './components/pages/comments/comments';

import PageNotFound from './components/partials/404';


// router
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
        component: Dashboard,
        name: 'dashboard',
        meta: {
          breadcrumb: 'Dashboard'
        }
      },

      // ---------- categories routes
      {
        path: '/categories',
        component: Categories,
        meta: {
          breadcrumb: 'Categories'
        },
        children: [
          {
            path: '',
            component: CategoryTable
          },
          {
            path: ':category/posts',
            component: Category,
            meta: {
              breadcrumb: (routeParams) => `${routeParams.category}`
            }
          },
        ]
      },

      // ---------- tags routes
      {
        path: '/tags',
        component: Tags,
        meta: {
          breadcrumb: 'tags'
        },
        children: [
          {
            path: '',
            component: TagTable
          },
          {
            path: '/tags/:tag/posts',
            component: Tag,
            meta: {
              breadcrumb: (routeParams) => `${routeParams.tag}`
            }
          }
        ],
      },

      // ---------- users routes
      {
        path: '/users',
        component: Users,
        meta: {
          breadcrumb: 'users'
        },
        children: [
          {
            path: '',
            component: AllUsers
          },
          {
            path: 'banned',
            component: BannedUsers,
            meta: {
              breadcrumb: 'banned'
            }
          },
          {
            path: '/users/:user/posts',
            component: User,
            meta: {
              breadcrumb: (routeParams) => `${routeParams.user}`
            }
          }
        ],
      },

      // ---------- posts routes
      {
        path: '/posts',
        component: Posts,
        meta: {
          breadcrumb: 'posts'
        },
        children: [
          {
            path: '',
            component: AllPosts
          },
          {
            path: 'unpublished',
            component: UnpublishedPosts,
            meta: {
              breadcrumb: 'unpublished posts'
            }
          },
          {
            path: 'new-post',
            component: PostForm,
            meta: {
              breadcrumb: 'new posts'
            }
          },
          {
            path: 'update-post/:post',
            component: PostForm,
            meta: {
              breadcrumb: (routeParams) => `${routeParams.post}`
            }
          },
          {
            path: ':post',
            component: Post,
            meta: {
              breadcrumb: (routeParams) => `${routeParams.post}`
            }
          }
        ]
      },

      // ---------- comments routes
      {
        path: '/comments',
        component: Comments,
        meta: {
          breadcrumb: 'comments'
        }
      },
    ]
  },

  // ---------- 400 routes
  {
    path: "/404",
    alias: "*",
    component: PageNotFound
  }
];

const router = new VueRouter({
  mode: 'history',
  base: 'admin-panel',
  linkActiveClass: 'active',
  routes,
});

export default router;
