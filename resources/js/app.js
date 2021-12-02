// Axios & Echo global
require("./bootstrap");

import store from "./store";

/* Core */
import Vue from "vue";
import Vuex from "vuex";
Vue.use(Vuex);

/* Register all components */

/* This is main entry point */

new Vue({
  el: "#main",
  store: new Vuex.Store(store),
  mounted() {
    console.log("mounted");
  },
});
