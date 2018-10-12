import Vue from 'vue';
import VueRouter from 'vue-router';
import qs from 'qs';

import routes from './routes';

const router = new VueRouter({
    mode: 'history',
    base: '/',
    routes,
    parseQuery(query) {
        return qs.parse(query);
    },
    stringifyQuery(query) {
        const result = qs.stringify(query);
        return result ? ('?' + result) : '';
    }
});

Vue.use(VueRouter);

export default router;
