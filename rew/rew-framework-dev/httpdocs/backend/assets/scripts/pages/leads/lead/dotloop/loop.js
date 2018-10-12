//DotLoop Rate Limit Exceeded - Timer
const $dotloop_rl_timer = $('#dotloop-rate-timer');
if ($dotloop_rl_timer.length) {
    let rlt_remaining = $dotloop_rl_timer.data('remaining');
    let rlt_interval = setInterval(() => {
        rlt_remaining = rlt_remaining - 1;
        if (rlt_remaining > 0) {
            $dotloop_rl_timer.html(' in ' + rlt_remaining + ' seconds');
        } else {
            $dotloop_rl_timer.empty();
            clearInterval(rlt_interval);
        }
    }, 1000);
}

/**
 * @param {string} parent - Id
 * @param {string} children - class name
 * @param {string} target - element
 * @param {string} evt
 */
const toggleSection = (parent, children, target, evt = 'click') => {
    const container = document.getElementById(parent);
    if (!container) return;
    container.addEventListener(evt, (e) => {
        const sections = Array.from(document.querySelectorAll(children));
        sections.forEach((section) => {
            if (section.contains(e.target) && e.target.nodeName == target.toUpperCase()) section.classList.toggle('is-active');
        });
    });
};

// Toggle loop details sections
toggleSection('loopDetails', '.block--loopDetails', 'h3');

// Toggle participants sections
toggleSection('participantsList', '.block--participants', 'button');