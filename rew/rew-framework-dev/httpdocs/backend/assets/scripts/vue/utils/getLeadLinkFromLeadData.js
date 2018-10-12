/**
 * Generate link from lead object
 * @param {Object} lead
 * @param {string} lead.id
 * @param {string} lead.first_name
 * @param {string} lead.last_name
 * @returns {string}
 */
export default (lead) => {
    const { id, first_name, last_name } = lead;
    return `<a href="/backend/leads/lead/summary/?id=${id}">${first_name} ${last_name}</a>`;
};
