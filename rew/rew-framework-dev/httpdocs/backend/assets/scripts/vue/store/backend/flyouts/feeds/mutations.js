export const ACTION_PLAN_FEED_MOBILE_TOGGLE = (state) => {
    state.backend.flyouts.feeds.mobile.open = !state.backend.flyouts.feeds.mobile.open;
};

export const ACTION_PLAN_FEED_MOBILE_CLOSE = (state) => {
    state.backend.flyouts.feeds.mobile.open = false;
};
