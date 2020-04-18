import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import Input from './views/Input.vue'
import Infos from './views/Infos.vue'
import Login from './views/Login.vue'
import Forum from './views/Forum.vue'
import Profil from './views/Profil.vue'
import Thread from './views/Thread.vue'



Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/input',
      name: 'input',
      component: Input
    },
    {
      path: '/infos',
      name: 'infos',
      component: Infos
    },
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/forum',
      name: 'forum',
      component: Forum
    },
    {
      path: '/profil',
      name: 'profil',
      component: Profil
    },
    {
      path: '/thread',
      name: 'thread',
      component: Thread
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (about.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import(/* webpackChunkName: "about" */ './views/About.vue')
    }
  ]
})
