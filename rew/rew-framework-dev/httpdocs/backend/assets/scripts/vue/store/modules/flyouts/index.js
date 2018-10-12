const FLYOUT_OPEN = 'FLYOUT_OPEN';
const FLYOUT_CLOSE = 'FLYOUT_CLOSE';

/**
 * Toggle the layout based on the flyout state
 * @param {Object} getters
 * @returns {undefined}
 */
function toggleLayout(getters) {
    const rootElement = document.documentElement;
    const parent = document.getElementById('app');
    const toggleMethod = getters.isOpen ? 'add' : 'remove';
    parent.classList[toggleMethod]('flyout-open');
    rootElement.classList[toggleMethod]('flyout-open');
}

export default {
    namespaced: true,

    state: {
        flyout: '',
        context: {}
    },
    actions: {
        open ({ commit, getters }, payload) {
            commit(FLYOUT_OPEN, payload);
            toggleLayout(getters);
            sessionStorage.setItem('FLYOUT_OPEN', getters['isOpen']);
        },
        close ({ commit, getters }) {
            commit(FLYOUT_CLOSE);
            toggleLayout(getters);
            sessionStorage.setItem('FLYOUT_OPEN', false);
        }
    },
    mutations: {
        [FLYOUT_OPEN] (state, payload) {
            state.flyout = payload.flyout;
            state.context = payload.context || {};
        },
        [FLYOUT_CLOSE] (state) {
            state.flyout = '';
        }
    },
    getters: {
        isOpen (state) {
            return state.flyout;
        },
        getContext (state) {
            return state.context;
        }
    }
};