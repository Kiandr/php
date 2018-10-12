import actions from 'vue/actions';

const AUTH_REQUEST = 'AUTH_REQUEST';
const AUTH_SUCCESS = 'AUTH_SUCCESS';
const AUTH_FAILURE = 'AUTH_FAILURE';

export default {
    namespaced: true,
    state: {
        loading: false,
        user: null,
        error: ''
    },
    actions: {
        async authenticateUser({ commit , state }) {
            if (state.loading) return;
            commit(AUTH_REQUEST);
            await actions.getUser().then(user => {
                commit(AUTH_SUCCESS, user);
            }).catch(error => {
                commit(AUTH_FAILURE, error);
            });
        }
    },
    mutations: {
        [AUTH_REQUEST] (state) {
            state.loading = true;
            state.error = null;
        },
        [AUTH_SUCCESS] (state, payload) {
            state.loading = false;
            state.user = payload;
        },
        [AUTH_FAILURE] (state, payload) {
            state.loading = false;
            state.error = payload;
        }
    },
    getters: {
        getUser (state) {
            return state.user;
        },
        getType (state) {
            const user = state.user;
            if (!user) return '';
            return user.type;
        },
        getId (state) {
            const user = state.user;
            if (!user) return '';
            return user.id;
        }
    }
};
