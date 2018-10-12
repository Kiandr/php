import Vuex from 'vuex';
import state from './state';
import actions from './actions';
import getters from './getters';
import mutations from './mutations';
import modules from './modules';

const store = new Vuex.Store({
    state,
    actions,
    getters,
    mutations,
    modules
});

export default store;
