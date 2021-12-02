window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

/**
 * Auto set wp nonce to every axios calls
 */
if (wp_api_settings) {
  window.axios.defaults.headers.common["X-WP-Nonce"] = wp_api_settings.nonce;
}

/**
 * We'll add interceptors to redirect user to login once we get 401 response
 *
 * */

window.axios.interceptors.response.use(
  function (response) {
    return response;
  },
  function (error) {
    if (error.response.status === 401) {
      window.location.href = "/wp-admin";
    }

    return Promise.reject(error);
  }
);

import { Model } from "vue-api-query";

// inject global axios instance as http client to Model
Model.$http = axios;
