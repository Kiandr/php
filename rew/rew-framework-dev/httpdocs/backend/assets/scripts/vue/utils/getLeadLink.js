/**
 * Generate lead link from lead object
 * @param {Object} lead
 * @param {string} lead.id
 * @param {string} lead.first_name
 * @param {string} lead.last_name
 * @returns {string}
 */
function fromLeadData (lead) {
    const {id, first_name, last_name} = lead;
    return `<a href="/backend/leads/lead/summary/?id=${id}">${first_name} ${last_name}</a>`;
}

export default {
    /**
     * Generate lead link from task object
     * @param {Object} task
     * @param {string} task.lead_id
     * @param {string} task.lead_first_name
     * @param {string} task.lead_last_name
     * @returns {string}
     */
    fromTaskData: (task) => {
        const { lead_id, lead_first_name, lead_last_name } = task;
        return fromLeadData({id:lead_id, first_name:lead_first_name, last_name:lead_last_name});
    },

    fromLeadData: fromLeadData
};
