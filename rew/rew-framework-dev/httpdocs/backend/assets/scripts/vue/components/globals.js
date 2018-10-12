import Vue from 'vue';

/**
 * Preloader
 */
Vue.component('loader', () => import('./shared/loaders/Loader.vue'));
Vue.component('loader-text', () => import('./shared/loaders/LoaderText.vue'));

/**
 * Blankslates
 */
Vue.component('blankslate', () => import('./shared/blankslates/Blankslate.vue'));
Vue.component('blankslate-text', () => import('./shared/blankslates/BlankslateText.vue'));
Vue.component('blankslate-title', () => import('./shared/blankslates/BlankslateTitle.vue'));

/**
 * Buttons
 */
Vue.component('btn', () => import('./shared/buttons/Button.vue'));
Vue.component('btn-group', () => import('./shared/buttons/ButtonGroup.vue'));
Vue.component('btn-filter', () => import('./shared/buttons/ButtonFilter.vue'));
Vue.component('btn-campaign-copy', () => import('./shared/buttons/ButtonCampaignCopy.vue'));

/**
 * Cards
 */
Vue.component('card', () => import('./shared/cards/Card.vue'));
Vue.component('card-section', () => import('./shared/cards/CardSection.vue'));

/**
 * Collapse
 */
Vue.component('collapse', () => import('./shared/collapse/Collapse.vue'));

/**
 * Dropdowns
 */
Vue.component('dropdown', () => import('./shared/dropdowns/Dropdown.vue'));
Vue.component('dropdown-grid', () => import('./shared/dropdowns/Grid.vue'));
Vue.component('dropdown-header', () => import('./shared/dropdowns/Header.vue'));
Vue.component('dropdown-item', () => import('./shared/dropdowns/Item.vue'));
Vue.component('dropdown-menu', () => import('./shared/dropdowns/Menu.vue'));

/**
 * Flyouts
 */
Vue.component('flyout-feed', () => import('./shared/flyouts/Feed.vue'));
Vue.component('assign-leads', () => import('./shared/flyouts/AssignLeads.vue'));
Vue.component('delete-leads', () => import('./shared/flyouts/DeleteLeads.vue'));
Vue.component('filter-blog-posts', () => import('./shared/flyouts/FilterBlogPosts.vue'));
Vue.component('filter-agents', () => import('./shared/flyouts/FilterAgents.vue'));
Vue.component('filter-snippets', () => import('./shared/flyouts/FilterSnippets.vue'));
Vue.component('filter-pages', () => import('./shared/flyouts/FilterPages.vue'));
Vue.component('copy-campaigns', () => import('./shared/flyouts/CopyCampaign.vue'));

/**
 * Form Controls
 */
Vue.component('form-control', () => import('./shared/forms/Control.vue'));
Vue.component('form-group', () => import('./shared/forms/Group.vue'));
Vue.component('form-label', () => import('./shared/forms/Label.vue'));
Vue.component('form-select', () => import('./shared/forms/Select.vue'));
Vue.component('form-selectize', () => import('./shared/forms/Selectize.vue'));
Vue.component('form-textarea', () => import('./shared/forms/Textarea.vue'));
Vue.component('form-wysiwyg', () => import('./shared/forms/Wysiwyg.vue'));
Vue.component('form-slider', () => import('./shared/forms/Slider.vue'));
Vue.component('form-date', () => import('./shared/forms/DateField.vue'));
Vue.component('form-date-range', () => import('./shared/forms/DateRangeField.vue'));
Vue.component('preset-date-select', () => import('./shared/forms/PresetDateSelect.vue'));

/**
 * Globally Shared
 */
Vue.component('action-plan', () => import('./shared/global/sidebar/ActionPlan.vue'));
Vue.component('action-plan-empty', () => import('./shared/global/sidebar/ActionPlanEmpty.vue'));
Vue.component('action-plan-state', () => import('./shared/global/sidebar/ActionPlanState.vue'));

/**
 * Logos & Icons
 */
Vue.component('avatar-icons', () => import('./shared/icons/Avatars.vue'));
Vue.component('icon', () => import('./shared/icons/Icon.vue'));
Vue.component('icon-set', () => import('./shared/icons/IconSet.vue'));

/**
 * Leads
 */
Vue.component('lead', () => import('./leads/Lead.vue'));

/**
 * Modal Dialogs
 */
Vue.component('modal', () => import('./shared/modals/Modal.vue'));
Vue.component('modal-body', () => import('./shared/modals/Body.vue'));
Vue.component('modal-content', () => import('./shared/modals/Content.vue'));
Vue.component('modal-title', () => import('./shared/modals/Title.vue'));

/**
 * Navigations
 */
Vue.component('navigation', () => import('./shared/navigations/Navigation.vue'));
Vue.component('navigation-item', () => import('./shared/navigations/Item.vue'));

/**
 * Thumbnails
 */
Vue.component('thumb', () => import('./shared/thumbs/Thumb.vue'));

/**
 * Accordion
 */
Vue.component('accordion', () => import('./shared/accordion/Accordion.vue'));
Vue.component('accordion-section', () => import('./shared/accordion/AccordionSection.vue'));

/**
 * Date Picker
 */
Vue.component('date-picker', () => import('./shared/datepicker/DatePicker.vue'));
