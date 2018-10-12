import api from './api';

// @TODO: document optional params for actions

export default {
    /**
     * Send email to lead
     * @param {number} lead_id
     * @param {string} subject
     * @param {string} content
     * @returns {Promise}
     */
    sendEmail(lead_id, subject, content) {
        return api.post(`crm/leads/${lead_id}/email/send`, {
            subject,
            content
        });
    },
    /**
     * Send text to lead
     * @param {number} lead_id
     * @param {string} content
     * @param {string} phone_number
     * @returns {Promise}
     */
    sendText(lead_id, content, phone_number) {
        return api.post(`crm/leads/${lead_id}/text/send`, {
            content,
            phone_number
        });
    },
    /**
     * Track call to lead
     * @param {number} lead_id
     * @param {string} type
     * @param {string} details
     * @returns {Promise}
     */
    trackCall(lead_id, type, details) {
        return api.post(`crm/leads/${lead_id}/phone/track`, {
            details,
            type
        });
    },
    /**
     * Create new lead note
     * @param {number} lead_id
     * @param {string} content
     * @returns {Promise}
     */
    createNote(lead_id, content) {
        return api.post(`crm/leads/${lead_id}/note/add`, {
            content
        });
    },
    /**
     * Assign lead to group
     * @param {number} lead_id
     * @param {Array.<number>} group_ids
     * @returns {Promise}
     */
    assignGroup(lead_id, group_ids) {
        return api.post(`crm/leads/${lead_id}/groups/assign`, {
            group_ids
        });
    },
    /**
     * Assign leads to groups
     * @param {Array.<number>} leads_id
     * @param {Array.<number>} group_ids
     * @returns {Promise}
     */
    assignGroups(lead_ids, group_ids) {
        return api.post('crm/groups/assign', {
            lead_ids,
            group_ids
        });
    },
    /**
     * Assign lead to group
     * @param {number} lead_id
     * @param {Array.<number>} group_ids
     * @returns {Promise}
     */
    assignAction(action_id, lead_ids) {
        return api.post(`crm/action_plans/${action_id}/assign`, {
            lead_ids
        });
    },
    /**
     * Assign lead to group
     * @param {number} agent_id
     * @param {Array.<number>} lead_ids
     * @returns {Promise}
     */
    assignAgent(agent_id, lead_ids) {
        return api.post(`crm/agents/${agent_id}/assign`, {
            lead_ids
        });
    },
    /**
     * Copy campaign(s) to agent
     * @param {number} agent_id
     * @param {Array.<number>} campaign_ids
     * @returns {Promise}
     */
    copyCampaigns(agent_id, campaign_ids) {
        return api.post(`crm/agents/${agent_id}/copy`, {
            campaign_ids
        });
    },
    /**
     * Send a listing recommendation to a lead
     * @param {number} lead_id
     * @param {number} mls_number
     * @param {string} message
     * @param {boolean} notify
     * @param {string} feed
     * @returns {Promise}
     */
    recommendListing(lead_id, mls_number, message, notify, feed) {
        return api.post(`/crm/leads/${lead_id}/listing/recommend`, {
            message,
            mls_number,
            notify,
            feed
        });
    },
    /**
     * Complete lead task
     * @param {number} user_task_id
     * @returns {Promise}
     */
    completeTask(user_task_id) {
        return api.put(`/crm/user/tasks/${user_task_id}`, {
            action: 'complete'
        });
    },
    /**
     * Dismiss lead task
     * @param {number} user_task_id
     * @param {string} note
     * @param {boolean} dismiss_followup_tasks
     * @returns {Promise}
     */
    dismissTask(user_task_id, note, dismiss_followup_tasks) {
        return api.put(`/crm/user/tasks/${user_task_id}`, {
            action: 'dismiss',
            note,
            dismiss_followup_tasks
        });
    },
    /**
     * Snooze lead task
     * @param {number} user_task_id
     * @param {string} note
     * @param {number} duration
     * @param {string} unit
     * @returns {Promise}
     */
    snoozeTask(user_task_id, note, duration, unit) {
        return api.put(`/crm/user/tasks/${user_task_id}`, {
            action: 'snooze',
            duration,
            note,
            unit
        });
    },
    /**
     * Get pending AP tasks
     * @returns {Promise}
     */
    getPendingActionPlanTasks() {
        return api.get('/crm/user/tasks', {
            statuses: ['Pending'],
            hide_automated: true
        });
    },
    /**
     * Get Action plans
     * @returns {Promise}
     */
    getActionPlans() {
        return api.get('/crm/action_plans');
    },
    /**
     * Get list of groups
     * @returns {Promise}
     */
    getGroups() {
        return api.get('/crm/groups');
    },
    /**
     * Get list of agents
     * @returns {Promise}
     */
    getAgents() {
        return api.get('/crm/agents');
    },
    /**
     * Get list of lenders
     * @returns {Promise}
     */
    getLenders() {
        return api.get('/crm/lenders');
    },
    /**
     * Get User
     * @returns {Promise}
     */
    getUser() {
        return api.get('/crm/user');
    },
    /**
     * Get Leads Favorite, viewed or recommended Listings
     * @param {number} lead_id
     * @param {string} type
     * @returns {Promise}
     */
    getListings(lead_id, type) {
        return api.get(`/crm/leads/${lead_id}/listings/${type}`);
    },
    /**
     * Get list of leads
     * @param {Object} filters
     * @param {String} after
     * @returns {Promise}
     */
    getLeads(filters = {}, after = null) {
        return api.get('/crm/leads', {
            ...filters,
            after,
        });
    },
    /**
     * Get list of feeds
     * @returns {Promise}
     */
    getFeeds() {
        return api.get('/crm/feeds');
    },
    /**
     * Accept Lead
     * @param {number} lead_id
     * @returns {Promise}
     */
    acceptLead(lead_id) {
        return api.post(`/crm/leads/${lead_id}/accept`);
    },
    /**
     * Delete Lead
     * @param {number} id
     * @returns {Promise}
     */
    deleteLead(lead_id) {
        return api.delete(`/crm/leads/${lead_id}`);
    },
    /**
     * Verify Texting
     * @param {number} lead_id
     * @returns {Promise}
     */
    verifyTexting(lead_id) {
        return api.get(`/crm/leads/${lead_id}/text/verify`);
    },
    /**
     * Update lead quick notes
     * @param {number} lead_id
     * @param {string} notes
     * @returns {Promise}
     */
    updateLeadNotes(lead_id, notes) {
        return api.put(`/crm/leads/${lead_id}`, {
            notes
        });
    },
    /**
     * Get listings info associated with a lead
     * @param {number} lead_id
     * @returns {Promise}
     */
    getLeadListingStats(lead_id) {
        return api.get(`/crm/leads/${lead_id}/listings`);
    },
    /**
     * Get Inquiries OR Showing Requests from a Lead
     * @param {number} lead_id
     * @param {string} type
     * @returns {Promise}
     */
    getLeadInquiriesData(lead_id, type) {
        return api.get(`/crm/leads/${lead_id}/inquiries/${type}`);
    },
    /**
     * Get Site Settings
     * @returns {Promise}
     */
    getSettings() {
        return api.get('/crm/settings');
    }
};
