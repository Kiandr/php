import actions from 'vue/actions';
import router from 'vue/router';
import showErrors from 'utils/showErrors';
import LEAD_FILTERS from './constants/LEAD_FILTERS';
import LEAD_OPTIONS from './constants/LEAD_OPTIONS';

/**
 * Context object passed to Vuex actions
 * Refer to {@link https://vuex.vuejs.org/en/api.html}
 * @typedef {Object} VuexContext
 * @prop {Object} state
 * @prop {Object} rootState
 * @prop {Function} commit
 * @prop {Function} dispatch
 * @prop {Object} getters
 * @prop {Object} rootGetters
 */

// UX Mutations
const LEADS_OPTION_SET = 'LEADS_OPTION_SET';
const LEAD_FILTER_FLYOUT_TOGGLE = 'LEAD_FILTER_FLYOUT_TOGGLE';
const LEAD_FILTER_FLYOUT_CLOSE = 'LEAD_FILTER_FLYOUT_CLOSE';
const LEAD_TOGGLE_SELECTED = 'LEAD_TOGGLE_SELECTED';
const LEAD_CLEAR_SELECTED = 'LEAD_CLEAR_SELECTED';
const LEAD_SEND_EMAIL = 'LEAD_SEND_EMAIL';
const LEAD_SEND_TEXT = 'LEAD_SEND_TEXT';
const LEAD_TRACK_CALL = 'LEAD_TRACK_CALL';
const LEAD_ASSIGN_ACTIONPLAN = 'LEAD_ASSIGN_ACTIONPLAN';
const LEAD_UPDATE_LISTINGS = 'LEAD_UPDATE_LISTINGS';
const LEADS_RESET_FILTERS = 'LEADS_RESET_FILTERS';

// API mutations
const LEAD_RESULTS_REQUEST = 'LEAD_RESULTS_REQUEST';
const LEAD_RESULTS_SUCCESS = 'LEAD_RESULTS_SUCCESS';
const LEAD_RESULTS_FAILURE = 'LEAD_RESULTS_FAILURE';
const DELETE_LEAD = 'DELETE_LEAD';
const UPDATE_LEAD = 'UPDATE_LEAD';

export default {
    namespaced: true,
    state: {
        options: LEAD_OPTIONS,
        count: 0,
        results: [],
        selected: [],
        listings: [],
        searching: false,
        loading: true,
        error: null,
        next: null
    },
    actions: {
        /**
         * Sets filter from root state
         * @param {VuexContext}
         * @param {Object} payload
         * @param {String} payload.name
         * @param {*} payload.value
         * @returns {undefined}
         */
        setFilter ({ rootState }, { name, value } ) {
            const query = rootState.route.query;
            router.push({
                query: {
                    ...query,
                    [name]: value
                }
            });
        },
        /**
         * Merge lead filters
         * @param {VuexContext}
         * @param {Object} payload
         * @returns {undefined}
         */
        setFilters ({ rootState }, payload) {
            const query = rootState.route.query;
            router.push({
                query: {
                    ...query,
                    ...payload
                }
            });
        },
        /**
         * Handle loading of available group options from API
         * @param {VuexContext}
         * @returns {Promise}
         */
        async getGroupOptions ({ commit }) {
            await actions.getGroups().then(data => {
                const options = data.groups.map(group => ({
                    value: group.id,
                    text: group.name,
                    style: group.style,
                    lead_count: group.count + ' Leads'
                }));
                commit(LEADS_OPTION_SET, ({
                    name: 'groups',
                    value: options
                }));
            }).catch(error => {
                showErrors([error.message]);
            });
        },
        /**
         * Handle loading of available agent options from API
         * @param {VuexContext}
         * @returns {Promise}
         */
        async getAgentOptions ({ commit }) {
            await actions.getAgents().then(data => {
                const options = data.agents.map(agent => ({
                    value: agent.id,
                    text: agent.first_name + ' ' + agent.last_name,
                    style: agent.last_name[0].toLowerCase(),
                    image: agent.image,
                    image_text: agent.first_name[0] + agent.last_name[0]
                }));
                commit(LEADS_OPTION_SET, ({
                    name: 'agents',
                    value: options
                }));
            }).catch(error => {
                showErrors([error.message]);
            });
        },
        /**
         * Handle loading of available action plans from API
         * @param {VuexContext}
         * @returns {Promise}
         */
        async getActionPlanOptions ({ commit }) {
            await actions.getActionPlans().then(data => {
                const options = data.action_plans.map(action_plan => ({
                    value: action_plan.id,
                    text: action_plan.name,
                    style: action_plan.style
                }));
                commit(LEADS_OPTION_SET, ({
                    name: 'action_plans',
                    value: options
                }));
            });
        },
        /**
         * Handle loading of available lender options from API
         * @param {VuexContext}
         * @returns {Promise}
         */
        async getLenderOptions ({ commit }) {
            await actions.getLenders().then(data => {
                const options = data.lenders.map(lender => ({
                    value: lender.id,
                    text: lender.first_name + ' ' + lender.last_name
                }));
                commit(LEADS_OPTION_SET, ({
                    name: 'lenders',
                    value: options
                }));
            }).catch(error => {
                showErrors([error.message]);
            });
        },
        /**
         * Toggle lead search filters
         * @param {VuexContext}
         */
        filterFlyoutToggle ({ commit }) {
            commit(LEAD_FILTER_FLYOUT_TOGGLE);
        },
        /**
         * Close lead search filters
         * @param {VuexContext}
         */
        filterFlyoutClose ({ commit }) {
            commit(LEAD_FILTER_FLYOUT_CLOSE);
        },
        /**
         * Toggle selected lead result
         * @param {VuexContext}
         * @param {Object} payload
         * @param {Number} payload.lead_id
         */
        toggleSelected ({ commit }, payload) {
            commit(LEAD_TOGGLE_SELECTED, payload);
        },
        /**
         * Clear selected lead results
         * @param {VuexContext}
         */
        clearSelected ({ commit }) {
            commit(LEAD_CLEAR_SELECTED);
        },
        /**
         * Update lead search results using set filters
         * @param {VuexContext}
         */
        async updateLeadResults ({ commit, getters }, payload) {
            commit(LEAD_RESULTS_REQUEST);
            await actions.getLeads(
                getters.getFilters,
                payload.next
            ).then(data => {
                commit(LEAD_RESULTS_SUCCESS, {
                    results: data.leads,
                    next: data.pagination.next,
                    prev: data.pagination.prev,
                    count: data.count
                });
            }).catch(error => {
                showErrors([error.message]);
                commit(LEAD_RESULTS_FAILURE, {
                    error
                });
            });
        },
        /**
         * Reset lead filters
         * @param {VuexContext}
         * @returns {undefined}
         */
        resetFilters () {
            router.push({query:{}});
        },
        /**
         * Handle accept of a pending lead
         * @param {VuexContext}
         * @param {Object} payload
         * @param {Number} payload.lead_id
         * @returns {Promise}
         */
        async acceptLead ({ commit }, { lead_id }) {
            await actions.acceptLead(lead_id).then(() => {
                commit(UPDATE_LEAD, {
                    status: 'accepted',
                    lead_id
                });
            }).catch((error) => {
                showErrors([error.message]);
            });
        },
        /**
         * Handle deletion of a lead from server
         * @param {VuexContext}
         * @param {Object} payload
         * @param {Number} payload.lead_id
         * @returns {Promise}
         */
        async deleteLead ({ commit }, { lead_id }) {
            await actions.deleteLead(lead_id).then(() => {
                commit(DELETE_LEAD, {
                    lead_id
                });
            });
        },
        sendEmail({ commit }, { leadId, subject, content }) {
            // Call API action, commit mutation & return promise as expected
            return actions.sendEmail(leadId, subject, content).then((data) => {
                commit(LEAD_SEND_EMAIL, { leadId });
                return data;
            });
        },
        sendText({ commit }, { leadId, content, phone_number }) {
            // Call API action, commit mutation & return promise as expected
            return actions.sendText(leadId, content, phone_number).then((data) => {
                commit(LEAD_SEND_TEXT, { leadId });
                return data;
            });
        },
        /**
         *
         * @param {VuexContext}
         * @param {Number} payload.leadId
         * @param {String} payload.type
         * @param {String} payload.content
         * @returns {*|Promise<T>}
         */
        trackCall({ commit }, { leadId, type, content }) {
            // Call API action, commit mutation & return promise as expected
            return actions.trackCall(leadId, type, content).then((data) => {
                commit(LEAD_TRACK_CALL, { leadId });
                return data;
            });
        },
        /**
         * Handle deletion of multiple leads from server
         * @param {VuexContext}
         * @param {Object} payload
         * @param {Array} payload.lead_ids
         * @returns {Promise}
         */
        async deleteLeads ({ commit }, { lead_ids }) {
            lead_ids.forEach((lead_id) => {
                commit(DELETE_LEAD, {
                    lead_id
                });
                Promise.all[actions.deleteLead(lead_id)];
            });
        },
        /**
         *
         * @param {VuexContext}
         * @param payload.selectedIds
         * @param payload.selectedOptions
         */
        assignActionPlan({ commit }, { selectedLeads, selectedOptions }) {
            // commit mutation
            commit(LEAD_ASSIGN_ACTIONPLAN, { selectedLeads, selectedOptions });
        },

        /**
         * Update the lead quick note
         * @param {VuexContext}
         * @param {Object} payload
         * @param {Number} payload.lead_id
         * @param {String} payload.notes
         * @returns {Promise}
         */
        async updateLeadNotes ({ commit }, { lead_id, notes }) {
            await actions.updateLeadNotes(lead_id, notes).then(() => {
                commit(UPDATE_LEAD, {
                    lead_id,
                    notes
                });
            });
        },
        /**
         * Update listings data for a lead
         * @param {VuexContext}
         * @param {Object} payload
         * @param {Number} payload.lead_id
         * @returns {Promise}
         */
        async updateLeadListings ({ commit }, { lead_id }) {
            await actions.getLeadListingStats(lead_id).then((data) => {
                commit(LEAD_UPDATE_LISTINGS, {
                    lead_id,
                    listings: data
                });
            }).catch((error) => {
                showErrors([error.message]);
            });
        },
        /**
         * Update Lead Sort Order
         * @param {Object} payload
         * @param {String} payload.sort
         * @param {String} payload.order
         */
        setSortOrder ({ dispatch }, { sort, order }) {
            dispatch('setFilters', {
                sort,
                order
            });
        }
    },
    mutations: {
        /**
         * Set option value
         * @param {Object} state
         * @param {String} payload.name
         * @param {Array} payload.value
         */
        [LEADS_OPTION_SET] (state, { name, value }) {
            state.options[name] = value;
        },
        /**
         * Toggle display of search filters
         * @param {Object} state
         */
        [LEAD_FILTER_FLYOUT_TOGGLE] (state) {
            state.searching = !state.searching;
        },
        /**
         * Close display of search filters
         * @param {Object} state
         */
        [LEAD_FILTER_FLYOUT_CLOSE] (state) {
            state.searching = false;
        },
        /**
         * Lead results required
         * @param {Object} state
         */
        [LEAD_RESULTS_REQUEST] (state) {
            state.loading = true;
            state.error = null;
        },
        /**
         * Lead results received
         * @param {Object} state
         * @param {Object} payload
         * @param {Array} payload.results
         */
        [LEAD_RESULTS_SUCCESS] (state, { results, next, prev, count }) {
            if (prev == null) {
                state.results = results;
            } else {
                state.results = state.results.concat(results);
            }
            state.next = next;
            state.count = count;
            state.loading = false;
        },
        /**
         * Lead results failed
         * @param {Object} state
         * @param {Object} payload
         * @param {Error} payload.error
         */
        [LEAD_RESULTS_FAILURE] (state, { error }) {
            state.loading = false;
            state.error = error;
        },
        /**
         * Toggle lead result selection
         * @param {Object} state
         * @param {Object} payload
         * @param {Number} payload.lead_id
         */
        [LEAD_TOGGLE_SELECTED] (state, { lead_id }) {
            if (state.selected.includes(lead_id)) {
                let index = state.selected.indexOf(lead_id);
                state.selected.splice(index, 1);
            } else {
                state.selected.push(lead_id);
            }
        },
        /**
         * Clear selectied lead result
         * @param {Object} state
         */
        [LEAD_CLEAR_SELECTED] (state) {
            state.selected = [];
        },
        /**
         * Handle deletion of a lead
         * @param {Object} state
         * @param {Object} payload
         * @param {Number} payload.lead_id
         */
        [DELETE_LEAD] (state, { lead_id }) {
            state.results = state.results.filter(({ id }) => id !== lead_id);
            state.selected = state.selected.filter(id => id !== lead_id);
        },
        /**
         * Update lead result data
         * @param {Object} state
         * @param {Object} payload
         * @param {Number} payload.lead_id
         * @param {*} payload.<PropName>
         */
        [UPDATE_LEAD] (state, { lead_id, ...data }) {
            const lead_index = state.results.findIndex((item) => item.id === lead_id);
            const lead_result = state.results[lead_index];
            state.results.splice(lead_index, 1, {
                ...lead_result,
                ...data
            });
        },
        /**
         * Increment emails
         * @param state
         * @param leadId
         */
        [LEAD_SEND_EMAIL](state, { leadId }) {
            // find the lead in store
            const index = state.results.findIndex(function(lead) {
                return lead.id == leadId;
            });
            state.results[index].num_emails = parseInt(state.results[index].num_emails) + 1;
        },
        /**
         * Increment texts
         * @param state
         * @param leadId
         */
        [LEAD_SEND_TEXT](state, { leadId }) {
            // find the lead in store
            const index = state.results.findIndex(function(lead) {
                return lead.id == leadId;
            });
            state.results[index].num_texts = parseInt(state.results[index].num_texts) + 1;
        },
        /**
         * Increment calls
         * @param state
         * @param leadId
         */
        [LEAD_TRACK_CALL](state, { leadId }) {
            // find the lead in store
            const index = state.results.findIndex(function(lead) {
                return lead.id == leadId;
            });
            state.results[index].num_calls = parseInt(state.results[index].num_calls) + 1;
        },
        /**
         * Update action plans
         * @param state
         * @param leadId
         */
        [LEAD_ASSIGN_ACTIONPLAN](state, { selectedLeads, selectedOptions }) {
            // find the lead in store
            const index = state.results.findIndex(function(lead) {
                return lead.id == selectedLeads;
            });
            if(state.results[index].action_plans === null){
                state.results[index].action_plans = [{'name': selectedOptions[0].text}];
            } else {
                state.results[index].action_plans.push({'name': selectedOptions[0].text});
            }
        },
        /**
         * Update lead listings data
         * @param {Object} state
         * @param {Object} payload
         */
        [LEAD_UPDATE_LISTINGS] (state, payload) {
            state.listings.push(payload);
        },
        /**
         * Update lead listings data
         * @param {VuexContext}
         */
        [LEADS_RESET_FILTERS] (rootState) {
            rootState.route.query = {};
        }
    },
    getters: {
        getFilters (state, getters, rootState) {
            const query = rootState.route.query;
            return { ...LEAD_FILTERS, ...query };
        },
        getOptions (state) {
            return state.options;
        },
        getSelected (state) {
            return state.selected;
        },
        isSearchOpen (state) {
            return state.searching === true;
        },
        getResultsCount (state) {
            return state.count;
        },
        getResults (state) {
            return state.results;
        },
        getResult: (state) => (id) => {
            let result = false;
            const { results } = state;
            if (results && results.length) {
                results.some(r => {
                    if (r.id === id) {
                        result = r;
                        return true;
                    }
                    return false;
                });
            }
            return result;
        },
        getAgent: (state) => (id) =>  {
            let result = false;
            const { options } = state;
            if (options.agents && options.agents.length) {
                options.agents.some(r => {
                    if (r.value === id) {
                        result = r;
                        return true;
                    }
                    return false;
                });
            }
            return result;
        },
        getListings: (state) => (id) => {
            let listingData = false;
            const { listings } = state;
            if (listings && listings.length) {
                listings.some((lead) => {
                    if (lead.lead_id === id) {
                        listingData = lead.listings;
                        return true;
                    }
                    return false;
                });
            }
            return listingData;
        }

    }
};
