/**
 * Error handling
 * @param messageOrEvent
 * @param source
 * @param lineno
 * @param colno
 * @param error
 * @returns {boolean}
 */
window.errors = [];
window.onerror = (messageOrEvent, source, lineno, colno, error) => { // eslint-disable-line no-unused-vars
    window.errors.push(messageOrEvent);
};
