import 'jquery-notify';

let notice = null;
const $notices = $('#notifications');
const defaultOptions = { close: false };

/**
 * Display success notices
 * @param {Array} success
 * @param {String} title
 * @param {Object} options
 */
export default (success, title = 'Action Successful!', options = defaultOptions) => {
    const { close, ...noticeOpts } = options;
    if (notice && close === true) notice.close();
    notice = $notices.notify('create', 'notify-success', {
        text: `<ul><li>${success.join('</li><li>')}</li></ul>`,
        title: title || ''
    }, noticeOpts);
};