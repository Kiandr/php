<template>
  <div class="action-plan" :class="{
    'plan--danger' : is_overdue,
    'plan--warning' : is_coming_up,
    'no-height': no_height }" :id="`action-plan-${action_plan.user_task_id}`">
    <div class="plan__header text--strong">
      <div class="plan__title">{{ action_plan.title }}</div>
      <div class="plan__time">{{ utc(action_plan.timestamp_due) | moment('timezone', user_timezone, 'h:mma') }}</div>
    </div>
    <div class="plan__body text--mute">
      <div class="plan__meta">
        <span v-if="action_plan.type.toLowerCase() === 'call'">Log Call</span>
        <span v-if="action_plan.type.toLowerCase() === 'email'">Send Email</span>
        <span v-if="action_plan.type.toLowerCase() === 'search'">Save Search</span>
        <span v-if="action_plan.type.toLowerCase() === 'listing'">Send Listing</span>
        <span v-if="action_plan.type.toLowerCase() === 'text'">Send Text</span>
        <span v-if="action_plan.type.toLowerCase() === 'group'">Add To Group</span>
        <span v-if="action_plan.type.toLowerCase() === 'custom'">Custom Task</span>
      </div>
      <div class="plan__countdown">{{ utc(action_plan.timestamp_due) | moment('from') }}</div>
    </div>
    <div class="plan__footer">
      <a class="plan__agent text text--strong text--mute">
        <thumb :src="action_plan.lead_image" :modifiers="['xs']" :context="[action_plan.lead_first_name, action_plan.lead_last_name]"></thumb>
        <span>{{ lead_full_name }}</span>
      </a>

      <dropdown>
        <span slot="toggle">Actions</span>
        <dropdown-menu slot="menu">
          <dropdown-item @click.native="completeActionPlan(action_plan.user_task_id, action_plan.type.toLowerCase(), action_plan.lead_id, action_plan.context_data)">
            <span v-if="action_plan.type.toLowerCase() === 'call'">Log Call</span>
            <span v-if="action_plan.type.toLowerCase() === 'email'">Send Email</span>
            <span v-if="action_plan.type.toLowerCase() === 'search'">Save Search</span>
            <span v-if="action_plan.type.toLowerCase() === 'listing'">Send Listing</span>
            <span v-if="action_plan.type.toLowerCase() === 'text'">Send Text</span>
            <span v-if="action_plan.type.toLowerCase() === 'group'">Add To Group</span>
            <span v-if="action_plan.type.toLowerCase() === 'custom'">Complete Task</span>
          </dropdown-item>
          <dropdown-item @click.native="snoozeActionPlan(action_plan.user_task_id)">Snooze</dropdown-item>
          <dropdown-item @click.native="dismissActionPlan(action_plan.user_task_id, action_plan.lead_id, action_plan.context_data)">Dismiss</dropdown-item>
        </dropdown-menu>
      </dropdown>

    </div>
  </div>
</template>

<style lang="scss" scoped>
    :root {
        --action-plan-gutter: 1.5rem;
    }

    // Default Plan styles
    .action-plan {
        padding-bottom: var(--action-plan-gutter);
        padding-top: var(--action-plan-gutter);
        position: relative;
        margin-right: var(--action-plan-gutter);
        margin-left: var(--action-plan-gutter);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        font-size: 0.9375rem;
        color: $gray-450;
        transition-timing-function: ease-in-out;
        transition-duration: 210ms;

        .plan__header {
            color: $black;
        }

        .plan__title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-right: 5px;
        }

        // Animation Prep
        box-sizing: content-box;
        height: 5.13rem;

        > div {
            display: flex;
            justify-content: space-between;
        }

        &.no-height {
            position: absolute;
            transform: translate(100%);
            padding-bottom: 0;
            padding-top: 0;
            opacity: 0;
            height: 0;
        }

        &::after {
            content: "";
            display: block;
            width: calc(100% + 1.5rem);
            height: 1px;
            position: absolute;
            right: -1.5rem;
            bottom: 0;
            flex: 9 0 auto;
            align-self: center;
            background: $border-color;
        }

        // Plan States

        // Warning
        &.plan--warning {
            background-color: $warning;
            border-color: $warning;
            color: $white;
            margin: 0;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            .plan__header {
                color: $white;
            }
            .icon {
                fill: $white;
            }
            &::after {
                display: none;
            }
        }

        // Danger
        &.plan--danger {
            background-color: $danger;
            border-color: $danger;
            color: $white;
            margin: 0;
            padding-left: 1.5rem;
            padding-right: 1.5rem;

            .plan__header {
                color: $white;
            }
            .icon {
                fill: $white;
            }
            &::after {
                display: none;
            }
        }
    }

    .plan__footer {
        margin-top: .5rem;
    }

    .plan__agent {
        display: flex;
    }

    .thumb-wrap {
        margin-right: .25rem;
    }

    .icon {
        border-radius: $border-radius;
        cursor: pointer;

        .-is-open &,
        &:hover,
        &.hover {
            background: transparentize($gray-600, .92);
        }
    }

    .plan--danger /deep/ .dropdown-wrap button,
    .plan--warning /deep/ .dropdown-wrap button {
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
    }

    /deep/ .dropdown-wrap button {
        font-size: 14px;
        color: $gray-450;
        border-radius: 2px;
        padding-left: 4px;
        padding-right: 4px;
        border: 1px solid #ccc !important;
    }
</style>

<script>


    import email from 'vue/components/shared/global/modal/actionplan/Email.vue';
    import call from 'vue/components/shared/global/modal/actionplan/Call.vue';
    import text from 'vue/components/shared/global/modal/actionplan/Text.vue';
    import listing from 'vue/components/shared/global/modal/actionplan/Listing.vue';
    import search from 'vue/components/shared/global/modal/actionplan/Search.vue';
    import custom from 'vue/components/shared/global/modal/actionplan/Custom.vue';
    import group from 'vue/components/shared/global/modal/actionplan/Group.vue';
    import dismiss from 'vue/components/shared/global/modal/actionplan/Dismiss.vue';
    import snooze from 'vue/components/shared/global/modal/actionplan/Snooze.vue';
    import store from 'store';
    import dateUtils from 'utils/date';

    export default {
        props: ['action_plan'],

        data: function () {
            return {
                no_height: false
            };
        },

        methods: {
            completeActionPlan: async function (id, type, lead_id, context_data = {}) {
                /**
                 * Define the title and component to feed into the modal for each type
                 */
                let modal_content = {
                    email: {
                        title: 'Send Email',
                        content: email,
                        context: {
                            task_data: context_data,
                            lead_id: lead_id,
                            id: id
                        }
                    },
                    call: {
                        title: 'Log Phone Call',
                        content: call,
                        context: {
                            task_data: context_data,
                            lead_id: lead_id,
                            id: id
                        }
                    },
                    text: {
                        title: 'Send Text Message',
                        content: text,
                        context: {
                            task_data: context_data,
                            lead_id: lead_id,
                            id: id
                        }
                    },
                    listing: {
                        title: 'Send Listing',
                        content: listing,
                        context: {
                            task_data: context_data,
                            lead_id: lead_id,
                            id: id
                        }
                    },
                    search: {
                        title: 'Create Saved Search',
                        content: search,
                        context: {
                            task_data: context_data,
                            lead_id: lead_id,
                            id: id
                        }
                    },
                    custom: {
                        title: 'Custom Task',
                        content: custom,
                        context: {
                            task_data: context_data,
                            lead_id: lead_id,
                            id: id
                        }
                    },
                    group: {
                        title: 'Add To Group',
                        content: group,
                        context: {
                            task_data: context_data,
                            lead_id: lead_id,
                            id: id
                        }
                    }
                };

                /**
                 * Dynamically populate the modal content based on the type parameter
                 * @bkaternick - check this out.
                 */
                store.dispatch('modal/open' , modal_content[type]);
            },

            /**
             * Open modal to snooze chosen task
             */
            snoozeActionPlan: function (id) {
                const title = this.action_plan.title;

                store.dispatch('modal/open' , {
                    title: `Snooze Task: "${title}"`,
                    content: snooze,
                    context: {
                        id: id
                    }
                });

            },

            /**
             * Open modal to dismiss chosen task
             */
            dismissActionPlan: function (id, lead_id, context_data = {}) {
                const title = this.action_plan.title;

                store.dispatch('modal/open' , {
                    title: `Dismiss Task: "${title}"`,
                    content: dismiss,
                    context: {
                        task_data: context_data,
                        lead_id: lead_id,
                        id: id
                    }
                });

            },

            utc: function (time) {
                return dateUtils.toUtc(time);
            }
        },

        computed: {
            action_plans: function () {
                return store.state.backend.flyouts.feeds.action_plans;
            },

            lead_full_name: function () {
                return `${this.action_plan.lead_first_name} ${this.action_plan.lead_last_name}`;
            },

            completed: function () {
                return store.state.backend.flyouts.feeds.action_plans.completed;
            },

            is_overdue: function () {
                return store.state.backend.flyouts.feeds.action_plans.overdue.includes(this.action_plan.user_task_id);
            },

            is_coming_up: function () {
                return store.state.backend.flyouts.feeds.action_plans.coming_up.includes(this.action_plan.user_task_id);
            },

            user_timezone: function () {
                return dateUtils.getUserTimezone(store);
            }
        },

        watch: {
            completed: function () {
                if (this.completed.includes(this.action_plan.user_task_id)) {
                    /**
                     * This action plan has been marked as completed.
                     * We can self-dismiss now because of that, nothing else is needed.
                     * This is purely for the visual effect.
                     */
                    this.no_height = true;
                }
            }
        }
    };
</script>