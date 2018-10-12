export const ACTION_PLAN_DATA_SET = (state, payload) => {
    state.backend.flyouts.feeds.action_plans.data = payload.data;
};

export const ACTION_PLAN_COMPLETE = (state, payload) => {
    /**
     * Simplify the call required to get the action_plans from the store
     * @type {*|computed.action_plans}
     */
    let action_plans = state.backend.flyouts.feeds.action_plans;

    /**
     * Add this record's ID to the completed array so it can animate and disappear accordingly.
     */
    action_plans.completed.push(payload.id);

    /**
     * Remove this record and it's ID's from the lists for overdue and upcoming tasks.
     */
    ACTION_PLAN_COMING_UP_REMOVE(state, payload);
    ACTION_PLAN_OVERDUE_REMOVE(state, payload);
};

export const ACTION_PLAN_DISMISS = (state, payload) => {
    /**
     * Simplify the call required to get the action_plans from the store
     * @type {*|computed.action_plans}
     */
    let action_plans = state.backend.flyouts.feeds.action_plans;
    /**
     * Snag the index so we can edit just this record.
     */
    let index = action_plans.data.findIndex(item => item.user_task_id === payload.id);

    /**
     * Remove this record from the store
     */
    action_plans.data.splice(index, 1);

    // Remove from coming up list
    let comingUp = action_plans.coming_up.findIndex(item => item === payload.id);
    if (comingUp !== -1) action_plans.coming_up.splice(comingUp, 1);

    // Remove from overdue tasks
    let overDue = action_plans.overdue.findIndex(item => item === payload.id);
    if (overDue !== -1) action_plans.overdue.splice(overDue, 1);

};

export const ACTION_PLAN_COMING_UP_ADD = (state, payload) => {
    /**
     * Simplify the call required to get the action_plans from the store
     * @type {*|computed.action_plans}
     */
    let action_plans = state.backend.flyouts.feeds.action_plans;

    /**
     * Add this record's ID to the coming_up array
     */
    action_plans.coming_up = payload;
};

export const ACTION_PLAN_COMING_UP_REMOVE = (state, payload) => {
    let action_plans = state.backend.flyouts.feeds.action_plans;
    let index = action_plans.coming_up.findIndex(item => item === payload.id);
    
    if (index !== -1) {
        action_plans.coming_up.splice(index, 1);
    }
};

export const ACTION_PLAN_COMING_UP_FLUSH = (state) => {
    let action_plans = state.backend.flyouts.feeds.action_plans;

    action_plans.coming_up = [];
};

export const ACTION_PLAN_OVERDUE_ADD = (state, payload) => {
    /**
     * Simplify the call required to get the action_plans from the store
     * @type {*|computed.action_plans}
     */
    let action_plans = state.backend.flyouts.feeds.action_plans;

    /**
     * Add this record's ID to the coming_up array
     */
    action_plans.overdue = payload;
};

export const ACTION_PLAN_OVERDUE_REMOVE = (state, payload) => {
    let action_plans = state.backend.flyouts.feeds.action_plans;
    let index = action_plans.overdue.findIndex(item => item === payload.id);

    if (index !== -1) {
        action_plans.overdue.splice(index, 1);
    }
};

export const ACTION_PLAN_OVERDUE_FLUSH = (state) => {
    let action_plans = state.backend.flyouts.feeds.action_plans;

    action_plans.overdue = [];
};
