export default {
    isFlyoutFeedOpen: (state) => {
        return state.backend.flyouts.feeds.mobile.open === true;
    },
    getTaskById: (state) => (taskId) => {
        let taskData = false;
        const tasks = state.backend.flyouts.feeds.action_plans.data;
        if (tasks && tasks.length) {
            tasks.some(task => {
                if (task.user_task_id === taskId) {
                    taskData = task;
                    return true;
                }
                return false;
            });
        }
        return taskData;
    }
};
