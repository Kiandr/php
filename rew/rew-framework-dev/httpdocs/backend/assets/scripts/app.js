import 'babel-polyfill';
import 'svgxuse';

/**
 * Load the lodash utility library
 */
import lodash from 'lodash';

/**
 * Add Vue import and some additives to make our lives easier.
 * Vue          - The Progressive JavaScript Framework
 * Vuex         - State management pattern library
 */
import Vue from 'vue';
import Vuex from 'vuex';
import VueMoment from 'vue-moment';
import momentTz from 'moment-timezone';
import router from './vue/router';
import { sync } from 'vuex-router-sync';

/**
 * Load the axios HTTP library to easily issue requests
 */
import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = '/backend/ajax'; // Intentional: Done this way so the requests in components start with a slash.

/**
 * Apply some global aliases
 */
window._ = lodash;

/**
 * Bind to the Vue instance
 */
Vue.use(Vuex);
Vue.use(VueMoment, {
    momentTz
});

/**
 * Create and attach the global store to newly instantiated VueRouter.
 */
const store = require('./vue/store/store').default;

/**
 * Sync route and store
 */
sync(store, router);

/**
 * Register global Vue components
 */
require('./vue/components/globals').default;

/**
 * Register global Vue directives
 */
require('./vue/components/directives').default;

// Initialize interface
const mounted = () => {
    import('./bindings');
    import('./bootstrap');
};

// Load Vue.js app instance
const el = document.getElementById('app');
if (el) {
    new Vue({
        el,
        store,
        router,
        mounted
    });
} else {
    mounted();
}
