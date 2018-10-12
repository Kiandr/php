import actions from '../actions';
import moment from 'moment';

/**
 * @param {Object} getters
 * @returns {undefined}
 */
const checkFeedFlyoutClass = (getters) => {
    $('#app').toggleClass('flyout-open',
        getters.isFlyoutFeedOpen || getters['crm/leads/isSearchOpen']
    );
};

export default {

    /**
     * Toggle flyout feed
     * @returns {undefined}
     */
    toggleFeedFlyout({ commit, dispatch, getters }) {
        const toggle = !getters.isFlyoutFeedOpen;
        sessionStorage.setItem('ACTION_PLAN_FEED_MOBILE_TOGGLE', toggle);
        commit('ACTION_PLAN_FEED_MOBILE_TOGGLE');
        dispatch('crm/leads/filterFlyoutClose');
        checkFeedFlyoutClass(getters);
    },

    /**
     * Close flyout feed
     * @returns {undefined}
     */
    closeFeedFlyout({ commit, getters }, remember) {
        if (remember) sessionStorage.setItem('ACTION_PLAN_FEED_MOBILE_TOGGLE', false);
        commit('ACTION_PLAN_FEED_MOBILE_CLOSE');
        checkFeedFlyoutClass(getters);
    },

    /**
     * Toggle flyout filter leads
     * @returns {undefined}
     */
    toggleFilterLeadsFlyout({ dispatch, getters }) {
        dispatch('crm/leads/filterFlyoutToggle');
        dispatch('closeFeedFlyout');
        checkFeedFlyoutClass(getters);
    },

    /**
     * Close flyout filter leads
     * @returns {undefined}
     */
    closeFilterLeadsFlyout({ dispatch, getters }) {
        dispatch('crm/leads/filterFlyoutClose');
        checkFeedFlyoutClass(getters);
    },

    /**
     * Load action plan tasks from API
     * & map overdue & coming up tasks
     * @returns <Promise>
     */
    loadActionPlanTasks({ commit }) {

        // Fetch action plan tasks from API server
        return actions.getPendingActionPlanTasks().then(data => {
            const { tasks } = data;

            // Commit loaded action plan data
            commit('ACTION_PLAN_DATA_SET', {
                data: tasks
            });

            // Map overdue &
            // coming up tasks
            let overdueTasks = [];
            let comingUpTasks = [];
            const now = moment.utc();
            tasks.forEach(task => {
                const dueTime = moment.utc(task.timestamp_due);
                const comingUpTime = dueTime.clone().utc().subtract(60, 'minutes');
                if (now.isSameOrAfter(dueTime)) {
                    overdueTasks.push(task.user_task_id);
                } else if (now.isBetween(comingUpTime, dueTime)) {
                    comingUpTasks.push(task.user_task_id);
                }
            });

            // Commit mapped tasks changes to store
            commit('ACTION_PLAN_OVERDUE_ADD', overdueTasks);
            commit('ACTION_PLAN_COMING_UP_ADD', comingUpTasks);

        });

    }
};
