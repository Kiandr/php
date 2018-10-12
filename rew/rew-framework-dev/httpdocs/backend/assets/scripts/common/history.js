import showErrors from 'utils/showErrors';
import URLS from 'constants/urls';

// Load script dependencies
const twemoji = require('twemoji');
const ajaxUrl = `${URLS.backendAjax}json.php?eventDetails`;

// Expand all history events
$('#expand-all').on('click', function () {
    const $this = $(this).toggleClass('expanded');
    const expanded = $this.hasClass('expanded');
    const $events = $timeline.find('[data-event]');
    $this.text(expanded ? 'Collapse All' : 'Expand All');
    $events.find('.event-details').toggleClass('hidden', expanded);
    $events.find('.expand-event').trigger('click');
    return false;
});

// Toggle history event details
const $timeline = $('#history-timeline');
$timeline.on('click', '.expand-event', function () {
    const $this = $(this);
    const $event = $this.closest('[data-event]');
    const $details = $event.find('.event-details');
    const eventType = $event.data('eventType');
    const eventId = $event.data('event');
    if ($details.hasClass('hidden')) {
        if (!$details.hasClass('loaded')) {
            $details.html('Loading...');
            $.ajax({
                url: ajaxUrl,
                cache: false,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    event: eventId
                },
                success: function (data) {
                    if (data.errors) showErrors(data.errors);
                    if (data.html) {
                        $details.addClass('loaded');
                        // Load email within <iframe>
                        if (eventType === 'Email') {
                            $details.html('');
                            var iframe = document.createElement('iframe');
                            iframe.style.display = 'inline-block';
                            iframe.style.border = 0;
                            iframe.style.width = '100%';
                            iframe.sandbox = 'allow-same-origin';
                            $details.get(0).appendChild(iframe);
                            iframe.contentWindow.document.open();
                            iframe.contentWindow.document.write(data.html);
                            iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
                            iframe.contentWindow.document.close();
                        } else {
                            $details.html(data.html);
                        }
                        // Emoji conversion via twemoji
                        var $sms = $details.find('.sms');
                        if ($sms.length === 1) {
                            twemoji.parse($sms.get(0), {
                                size: 16,
                                callback: function(icon, options) {
                                    switch (icon) {
                                    case 'a9':	// © copyright
                                    case 'ae':	// ® registered trademark
                                    case '2122':// ™ trademark
                                        return false;
                                    }
                                    return '' . concat(options.base, options.size, '/', icon, options.ext);
                                }
                            });
                        }

                    }
                },
                error: function (jqXHR) {
                    if (jqXHR.status === 0 || jqXHR.readyState === 0) return;
                    showErrors(['Your request could not be completed.']);
                }
            });
        }
        $details.removeClass('hidden');
        $this.text('-');
    } else {
        $details.addClass('hidden');
        $this.text('+');
    }
});
