import isEqual from 'lodash.isequal';
import tinyMCE from 'tinymce';
import diff from 'deep-diff';

// Check these forms for changes
const $forms = $('form.rew_check');

/**
 * Check for form changes
 * @param {jQuery} $forms
 * @returns {String|undefined}
 */
const checkFormsForChanges = ($forms) => {
    let formChanged = false;
    $forms.each(function () {
        const $form = $(this);
        const changed = checkFormChanges($form);
        $form.toggleClass('changed', changed);
        formChanged = formChanged || changed;
    });
    return formChanged;
};

/**
 * Ran on beforeunload to check for changes
 * @returns {String|undefined}
 */
const checkFormsBeforeLeaving = () => {
    if (checkFormsForChanges($forms)) {
        return 'One or more forms on this page have changed. Are you sure you want to leave this page?';
    }
};

/**
 * Check if form has changed
 * @param {jQuery} $form
 * @returns {Boolean}
 */
const checkFormChanges = ($form) => {
    const then = $form.data('formData');
    const now = getFormData($form);
    //eslint-disable-next-line no-console, no-undef
    if (__DEV__) console.log('form changes:', diff(then, now));
    return !isEqual(now, then);
};

/**
 * Get form data to check
 * @param {jQuery} $form
 * @returns {String}
 */
const getFormData = ($form) => {
    let formData = {};
    tinyMCE.triggerSave();
    const $inputs = $form.find(':input').not('.skip-check');
    $inputs.serializeArray().forEach(i => {
        formData[i.name] = i.value;
    });
    return formData;
};

/**
 * Save form changes
 */
export const saveFormData = () => {
    $forms.each(function () {
        const $form = $(this);
        const data = getFormData($form);
        $form.data('formData', data);
    });
};

// Initial save
saveFormData();

// Check if other forms have been changed
$forms.on('submit.checker', function (e) {
    const $otherForms = $forms.not($(this));
    if (checkFormsForChanges($otherForms)) {
        if (!confirm('Another form on this page has been changed. Are you sure you want to continue with this form submission?')) {
            e.preventDefault();
            return false;
        }
    }
    // Remove form check events from page
    $(window).off('beforeunload.checker');
    $(window).off('popstate.checker');
});

// Check for form changes before leaving the current page
$(window).on('beforeunload.checker', checkFormsBeforeLeaving);
$(window).on('popstate.checker', checkFormsBeforeLeaving);
