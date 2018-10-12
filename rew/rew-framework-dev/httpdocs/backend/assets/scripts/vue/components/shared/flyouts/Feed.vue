<template>
  <div v-if="is_loaded" class="flyout-feed" :class="{'-is-flyout-open' : is_open}">
    <a class="close" @click.prevent="close" href="#">
      <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" xml:space="preserve" width="24" height="24">
      <path fill="currentColor" d="M19.7,4.3c-0.4-0.4-1-0.4-1.4,0L12,10.6L5.7,4.3c-0.4-0.4-1-0.4-1.4,0s-0.4,1,0,1.4l6.3,6.3l-6.3,6.3 c-0.4,0.4-0.4,1,0,1.4C4.5,19.9,4.7,20,5,20s0.5-0.1,0.7-0.3l6.3-6.3l6.3,6.3c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3 c0.4-0.4,0.4-1,0-1.4L13.4,12l6.3-6.3C20.1,5.3,20.1,4.7,19.7,4.3z"></path>
      </svg>
    </a>
    <div v-if="!loading">
      <avatar-icons></avatar-icons>
      <header class="flyout-feed-title">What's Next?</header>
      <div class="action-plans-states">
        <action-plan-state></action-plan-state>
      </div>
      <div class="action-plans" v-cloak>
        <div v-if="action_plans.data.length">
          <action-plan v-for="action_plan in action_plans.data" :key="action_plan.user_task_id" :action_plan="action_plan"></action-plan>
        </div>
        <div v-else>
          <action-plan-empty></action-plan-empty>
        </div>
      </div>
    </div>
    <div class="wrap" v-else>
      <loader></loader>
    </div>
    <div class="flyout-feed-shadow"></div>
  </div>
</template>

<style lang="scss" scoped>
    :root {
        --feed-flyout-background-color: #eef0f2;
        --feed-flyout-width: 320px;
    }

    .flyout-feed {
        background-color: var(--feed-flyout-background-color);
        max-width: var(--feed-flyout-width);
        min-width: var(--feed-flyout-width);
        width: var(--feed-flyout-width);
        position: fixed;
        z-index: 1040;
        bottom: 0;
        right: 0;
        top: 0;
        transition: transform 210ms;
        transform: translate3d(100%, 0, 0);

        @media (max-width: 500px) {
            width: 100%;
            min-width: 100%;
        }

        &.-is-flyout-open {
            transform: none;
        }

        .close {
            color: $black;
            position: absolute;
            right: .5rem;
            top: .5rem;
        }

        .wrap {
            &,
            .overlay {
                position: absolute;
                bottom: 0;
                right: 0;
                left: 0;
                top: 0;
            }

            /deep/ .loader {
                transform: translate(50%, 50%);
                position: absolute;
                bottom: 50%;
                right: 50%;
            }
        }

        &-title {
            font-weight: 700;
            line-height: 1.5;
            padding: 1.5rem;
        }

        &-shadow {
            box-shadow: inset 2px 0 3px 0 transparentize($gray-600, .8);
            pointer-events: none;
            position: absolute;
            z-index: 1000;
            width: 3px;
            bottom: 0;
            left: 0;
            top: 0;

            @include media-breakpoint-down(md) {
                @include shadow(4);
                width: 100%;
            }
        }

        /* Prevent the last action plan dropdown from getting cut off */
        .action-plan:last-of-type {
            margin-bottom: 85px;
        }
    }
</style>

<script>

    import store from 'store';

    export default {
        props: {
            name: {
                type: String,
                default: () => 'Feed'
            }
        },

        data: function () {
            return {
                loading: true
            };
        },

        mounted: function () {
            store.dispatch('auth/authenticateUser');
            this.getActionPlans();
            if (sessionStorage.getItem('FLYOUT_OPEN') === 'Feed') {
                this.toggleFeedFlyout();
            }
            this.$root.$on('reload-action-plans', () => {
                this.getActionPlans();
            });
        },

        methods: {
            getActionPlans: async function () {

                // Load action plans
                this.loading = true;
                store.dispatch('loadActionPlanTasks').then(() => {
                    this.loading = false;
                }).catch(() => {
                    // @TODO: error handling
                    this.loading = false;
                });

            },

            toggleFeedFlyout: function () {
                store.dispatch('flyouts/open', {
                    flyout: 'Feed'
                });
            },

            close: function () {
                store.dispatch('flyouts/close');
            }
        },

        computed: {
            action_plans: function () {
                return store.state.backend.flyouts.feeds.action_plans;
            },

            action_plans_coming_up: function () {
                return this.action_plans.coming_up || [];
            },

            action_plans_overdue: function () {
                return this.action_plans.overdue || [];
            },

            is_open: function () {
                return this.name == store.getters['flyouts/isOpen'];
            },

            is_loaded: function () {
                return store.getters['auth/getUser'];
            }
        }
    };
</script>
