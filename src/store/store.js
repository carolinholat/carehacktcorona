import Vue from 'vue'
import Vuex from 'vuex'
//import todos from './modules/todos'

Vue.use(Vuex)

export default new Vuex.Store({
 // modules: {
  //  todos
 // },
  state: {
    counter: 0,
    name: 'Carolin'
  },
  getters: {
    extendName: function(state) {
      let name= state.name;
      return name + ' some appended text';
    },
    counterAdd5: function(state) {
      return state.counter + 5;
    }
  },
  mutations: {

  },
  actions: {

  }
})
