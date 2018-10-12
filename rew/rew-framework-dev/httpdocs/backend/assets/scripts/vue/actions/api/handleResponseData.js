/**
 * Handle API responses:
 *  - require valid JSON response
 *  - return NULL on 204 http code
 *  - throw SyntaxError if no data
 * @param {Object} response
 * @throws {SyntaxError}
 * @returns {Object}
 */
export default function (response) {
    const { data, status } = response;
    if (status === 204) return null;
    if (!data || typeof data !== 'object') {
        throw new SyntaxError;
    }
    return data || {};
}
