import moment from 'moment';

export default {
    /**
     * @param {String} timestampString MySQL Formatted Timestamp
     * @return {Date} UTC date object
     */
    toUtc(timestampString) {
        return moment.utc(timestampString);
    },

    /**
     * @param {Object} store
     * @return {String} ?
     */
    getUserTimezone({ state }) {
        return state.auth.user.TZ ||
            Intl.DateTimeFormat().resolvedOptions().timeZone
        ;
    }
};