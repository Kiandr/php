import LeadQuickNotes from 'components/Leads/LeadQuickNotes';
import { createElement } from 'react';
import { render } from 'react-dom';

// Render quick notes component to page
$('[data-lead-quick-notes]').each(function () {
    const leadQuickNotes = $(this).data('lead-quick-notes');
    render(createElement(LeadQuickNotes, leadQuickNotes), this);
});

const toggleAgentNotes = (() => { // eslint-disable-line no-unused-vars
    var notes = document.getElementById('agentNotes'),
        container = document.getElementById('agentNotesContainer'),
        button = document.createElement('button'),
        ctaText = 'View More Notes',
        fade = document.createElement('div');
    (function appendButton(parent) {
        if (notes && notes.clientHeight == 500) { // CSS maxHeight value set in `assets/style/pages/leads/lead/summary.css`
            button.className = 'btn w1/1';
            buttonText(button, ctaText);
            parent.appendChild(button);
            appendFade(notes);
        }
    })(container);
    function appendFade(parent) {
        fade.classList.add('fade');
        parent.appendChild(fade);
    }
    function buttonText(btn, text) {
        btn.innerHTML = text;
    }
    button.addEventListener('click', function() {
        notes.classList.toggle('is-active');
        notes.classList.contains('is-active') ? notes.removeChild(fade) : appendFade(notes);
        button.innerHTML == ctaText ? buttonText(this, 'View Less Notes') : buttonText(this, ctaText);
    });
})();

// DotLoop Rate Limit Exceeded - Timer
const $dotloop_rl_timer = $('#dotloop-rate-timer');
if ($dotloop_rl_timer.length > 0) {
    let rlt_remaining = $dotloop_rl_timer.data('remaining');
    let rlt_interval = setInterval(function() {
        rlt_remaining = rlt_remaining - 1;
        if (rlt_remaining > 0) {
            $dotloop_rl_timer.html(' in ' + rlt_remaining + ' seconds');
        } else {
            $dotloop_rl_timer.empty();
            clearInterval(rlt_interval);
        }
    }, 1000);
}
