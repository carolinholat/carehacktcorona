import Vue from 'vue'
import './plugins/axios'
import App from './App.vue'
import router from './router'
import store from './store/store'
import vuetify from './plugins/vuetify';
import axios from 'axios';
import VueResource from 'vue-resource';



Vue.use(VueResource);

Vue.config.productionTip = false;

const eventBus = new Vue();

export default eventBus;

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
