import 'jquery-notify';

let notice = null;
const $notices = $('#notifications');
const defaultOptions = { close: false };

/**
 * Display error notices
 * @param {Array} errors
 * @param {String} title
 * @param {Object} options
 */
export default (errors, title = 'An Error Has Occurred!', options = defaultOptions) => {
    const { close, ...noticeOpts } = options;
    if (notice && close === true) notice.close();
    notice = $notices.notify('create', 'notify-error', {
        text: `<ul><li>${errors.join('</li><li>')}</li></ul>`,
        title: title || ''
    }, noticeOpts);
};