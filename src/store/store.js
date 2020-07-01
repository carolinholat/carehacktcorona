import Vue from 'vue'
import Vuex from 'vuex'
//import todos from './modules/todos'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
      // url: 'http://116.203.207.113/carehacktcorona',
    url: location.protocol + "//" +  'scio.projuve.de',
   // url: 'http://localhost:8000',
    token: '',
    admin: false,
    abteilung: 0,
    abo: [],
    aboFix: [],
    aboFlex: [],
    kategorien: [],
    serverBug: false
  },
  mutations: {
    setToken: function(state, payload) {
      state.token = payload;
    },
    setAdmin: function(state) {
      state.admin = true;
    },
    unsetAdmin: function(state) {
      state.admin = false;
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
    },
    setServerBug: function(state, payload) {
      state.serverBug = payload;
    }
  },
})
