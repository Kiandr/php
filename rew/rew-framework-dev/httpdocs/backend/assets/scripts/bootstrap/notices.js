import 'jquery-notify';
setTimeout(() => {

    // Load notices from global window.__NOTIFICATIONS
    const notices = window.__NOTIFICATIONS__ || {};

    showNotification(notices);
    if (window.location.pathname != '/backend/login/') {
        $.get('/backend/inc/php/ajax/json.php', {notifications: true},function(notices){
            if (typeof notices['notifications'] != 'undefined') {
                showNotification(notices['notifications']);
            }
        });
    }

}, 0);

function showNotification(notices) {
    // Application notifications
    let $notices = $('#notifications').notify();
    for (let noticeType in notices) {
        let messages = notices[noticeType];
        if (messages && messages.length > 0) {
            let text = `<ul><li>${messages.join('</li><li>')}</li></ul>`;
            switch (noticeType) {
            case 'error':
                $notices.notify('create', 'notify-error', {
                    title: 'An Error Has Occurred!',
                    text
                });
                break;
            case 'success':
                $notices.notify('create', 'notify-success', {
                    title: 'Action Successful!',
                    text
                });
                break;
            case 'warning':
                $notices.notify('create', 'notify-warning', {
                    title: 'Warning!',
                    text
                });
                break;
            }
        }
    }
}