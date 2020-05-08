import Vue from 'vue'
import Vuex from 'vuex'
//import todos from './modules/todos'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    token: '',
    admin: false,
    abteilung: 0,
    abo: [],
    aboFix: [],
    aboFlex: [],
    kategorien: []
  },
  mutations: {
    setToken: function(state, payload) {
      state.token = payload;
    },
    setAdmin: function(state) {
      state.admin = true;
    },
    setAbo: function(state, payload) {
      state.abo = payload;
    },
    setAboFix: function(state, payload) {
      state.aboFix = payload;
    },
    setAboFlex: function(state, payload) {
      state.aboFlex = payload;
    },
    setAbteilung: function(state, payload) {
      state.abteilung = payload;
    },
    setKategorien: function(state, payload) {
      state.kategorien = payload;
    }
  },
})
