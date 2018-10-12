const MODAL_CLOSE = 'MODAL_CLOSE';
const MODAL_OPEN = 'MODAL_OPEN';
const MODAL_LOADER_SHOW = 'MODAL_LOADER_SHOW';
const MODAL_LOADER_HIDE = 'MODAL_LOADER_HIDE';
const MODAL_UPDATE_ACTIVE = 'MODAL_UPDATE_ACTIVE';

/**
 * Toggle the layout based on the modal state
 * @param {Object} getters
 * @returns {undefined}
 */
function toggleLayout(getters) {
    const rootElement = document.documentElement;
    const toggleMethod = getters.isOpen ? 'add' : 'remove';
    rootElement.classList[toggleMethod]('modal-open');
}

export default {
    namespaced: true,

    state: {
        open: false,
        loading: false,
        context: {},
        title: '',
        content: '',
        activeContent: 0
    },
    actions: {
        open ({ commit, getters }, payload) {
            commit(MODAL_OPEN, payload);
            toggleLayout(getters);
        },
        close ({ commit, getters }) {
            commit(MODAL_CLOSE);
            toggleLayout(getters);
        },
        loader_show ({ commit }) {
            commit(MODAL_LOADER_SHOW);
        },
        loader_hide ({ commit }) {
            commit(MODAL_LOADER_HIDE);
        },
        setActiveContent ({ commit }, payload) {
            commit(MODAL_UPDATE_ACTIVE, payload);
        }
    },
    mutations: {
        [MODAL_CLOSE] (state) {
            state.open = false;
            state.content = '';
        },
        [MODAL_OPEN] (state, payload) {
            state.open = true;
            state.title = payload.title || false;
            state.activeContent = payload.activeContent || 0;
            state.content = payload.content || '';
            state.context = payload.context || {};
        },
        [MODAL_LOADER_SHOW] (state) {
            state.loading = true;
        },
        [MODAL_LOADER_HIDE] (state) {
            state.loading = false;
        },
        [MODAL_UPDATE_ACTIVE] (state, payload) {
            state.activeContent = payload.activeContent;
        }
    },
    getters: {
        isOpen (state) {
            return !!state.open;
        },
        isLoading (state) {
            return !!state.loading;
        },
        getTitle (state) {
            return state.title;
        },
        getContent (state) {
            const {content, activeContent} = state;
            const isTabbedContent = Array.isArray(content);
            return isTabbedContent ? content[activeContent].content : content;
        },
        getContentTabs(state) {
            const { content, activeContent } = state;
            const isTabbedContent = Array.isArray(content);
            if (!isTabbedContent) return null;
            return content.map(function (tabbed, index) {
                return {
                    title: tabbed.title,
                    active: index === activeContent
                };
            });
        },
        getInitialContent (state) {
            return state.initialContent;
        },
        getContext (state) {
            return state.context;
        }
    }
};