<template>
  <div class="flyout-filterleads" :class="{'-is-flyout-open' : is_open}" ref="flyout">
    <a class="close" @click.prevent="close" href="#">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" xml:space="preserve" width="24" height="24">
            <path fill="currentColor" d="M19.7,4.3c-0.4-0.4-1-0.4-1.4,0L12,10.6L5.7,4.3c-0.4-0.4-1-0.4-1.4,0s-0.4,1,0,1.4l6.3,6.3l-6.3,6.3 c-0.4,0.4-0.4,1,0,1.4C4.5,19.9,4.7,20,5,20s0.5-0.1,0.7-0.3l6.3-6.3l6.3,6.3c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3 c0.4-0.4,0.4-1,0-1.4L13.4,12l6.3-6.3C20.1,5.3,20.1,4.7,19.7,4.3z"></path>
        </svg>
    </a>
    <div>
        <avatar-icons></avatar-icons>
        <header class="flyout-header">
            <h2 class="title">Filter Leads</h2>
        </header>
        <form @submit.prevent="submitFilterLeads">
            <div class="flyout-content">
                <div>
                    <div class="-mar-bottom">
                        <label class="field__label">Joined</label>
                        <preset-date-select :start_set.sync="date_start" :end_set.sync="date_end" placeholder="Select date..." @input="clearInvalidWarnings"></preset-date-select>
                    </div>
                    <div class="-mar-bottom">
                        <label class="field__label">Status</label>
                        <form-selectize multiple v-model="status" placeholder="Select status..." :options="options.status" @input="clearInvalidWarnings"></form-selectize>
                    </div>
                    <div v-if="!isLender" class="-mar-bottom">
                        <label class="field__label">Groups</label>
                        <form-selectize multiple v-model="groups" placeholder="Select groups..." :options="options.groups" :settings="groupRenderSettings" @input="clearInvalidWarnings"></form-selectize>
                    </div>
                    <div class="-mar-bottom" v-if="permission('crm.can.view.agents')">
                        <label class="field__label">Agents</label>
                        <form-selectize multiple v-model="agents" placeholder="Select agents..." :options="options.agents" @input="clearInvalidWarnings"></form-selectize>
                    </div>
                    <div v-if="permission('crm.can.view.lenders')" class="-mar-bottom">
                        <label class="field__label">Lenders</label>
                        <form-selectize multiple v-model="lenders" placeholder="Select lenders..." :options="options.lenders" @input="clearInvalidWarnings"></form-selectize>
                    </div>
                    <div class="-mar-bottom">
                        <label class="field__label">Sort By</label>
                         <form-select v-model="order" placeholder="..." :options="options.order" @input="clearInvalidWarnings" class="-mar-bottom"></form-select>
                    </div>
                    <div class="-mar-bottom">
                        <label class="field__label">Order By</label>
                         <form-select v-model="sort" placeholder="..." :options="options.sort" @input="clearInvalidWarnings"></form-select>
                    </div>
                </div>

                <accordion>
                    <accordion-section title="Action Plans">
                        <div class="-mar-bottom">
                            <label class="field__label">Assigned action plan</label>
                            <form-selectize multiple v-model="action_plans" placeholder="Select action plans..." :options="options.action_plans" @input="clearInvalidWarnings"></form-selectize>
                        </div>
                        <div v-if="action_plans != ''" class="-mar-bottom">
                            <label class="field__label">Status</label>
                            <form-select v-model="action_plan_status" placeholder="All" :options="options.action_plan_status" @input="clearInvalidWarnings"></form-select>
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">Has Due Task(s)</label>
                            <form-select v-model="action_plan_due_tasks" placeholder="No Preference" :options="options.action_plan_due_tasks" @input="clearInvalidWarnings"></form-select>
                        </div>
                        <div class="-mar-bottom" v-if="action_plan_due_tasks == 'true'">
                            <label class="field__label">Due Task Types</label>
                            <form-selectize multiple v-model="action_plan_types" placeholder="No Preference" :options="options.action_plan_types" @input="clearInvalidWarnings" :disabled="action_plan_due_tasks != 'true'"></form-selectize>
                        </div>
                    </accordion-section>

                    <accordion-section title="Subscriptions">
                        <div class="-mar-bottom">
                            <label class="field__label">Subscribed to Campaigns</label>
                            <form-select v-model="opt_marketing" placeholder="No Preference" :options="options.opt_marketing" @input="clearInvalidWarnings"></form-select>
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">Subscribed to Searches</label>
                            <form-select v-model="opt_searches" placeholder="No Preference" :options="options.opt_searches" @input="clearInvalidWarnings"></form-select>
                        </div>
                    </accordion-section>

                    <accordion-section title="Search Preferences">
                        <div class="-mar-bottom">
                            <label class="field__label">Property Type</label>
                            <input v-model="search_type" type="text" name="search_type" @input="clearInvalidWarnings" />
                        </div>
                        <div class="-mar-bottom">
                           <label class="field__label">City</label>
                           <input v-model="search_city" type="text" name="search_city" />
                        </div>
                        <div class="-mar-bottom">
                           <label class="field__label">Subdivision</label>
                           <input v-model="search_subdivision" type="text" name="search_subdivision" />
                        </div>
                        <div class="-mar-bottom">
                           <label class="field__label">Minimum / Maximum Price</label>
                           <input data-currency type="number" name="search_minimum_price" placeholder="0" v-model="search_minimum_price" class="-mar-bottom" />
                           <input data-currency type="number" name="search_maximum_price" placeholder="0" v-model="search_maximum_price" />
                        </div>
                    </accordion-section>

                    <accordion-section title="Lead Activity">
                        <div class="-mar-bottom">
                            <label class="field__label">Last Active Date</label>
                            <form-date-range :start_set.sync="active_start" :end_set.sync="active_end" placeholder="Select date..."></form-date-range>
                        </div>
                        <div class="-mar-bottom">
                            <form-slider name="inquiries" :min_value.sync="inquiries_min" :max_value.sync="inquiries_max" title="# of Inquiries"></form-slider>
                        </div>
                        <div class="-mar-bottom">
                            <form-slider name="visits" :min_value.sync="visits_min" :max_value.sync="visits_max" title="# of Visits"></form-slider>
                        </div>
                        <div class="-mar-bottom">
                            <form-slider name="listings" :min_value.sync="listings_min" :max_value.sync="listings_max" title="# of Viewed Listings"></form-slider>
                        </div>
                        <div class="-mar-bottom">
                            <form-slider name="favorites" :min_value.sync="favorites_min" :max_value.sync="favorites_max" title="# of Favorite Listings"></form-slider>
                        </div>
                        <div class="-mar-bottom">
                            <form-slider name="searches" :min_value.sync="searches_min" :max_value.sync="searches_max" title="# of Saved Searches"></form-slider>
                        </div>
                        <div class="-mar-bottom">
                            <form-slider name="texts_incoming" :min_value.sync="texts_incoming_min" :max_value.sync="texts_incoming_max" title="# of Incoming Texts"></form-slider>
                        </div>
                    </accordion-section>

                    <accordion-section title="Agent Activity" class="filterlead-flyout-section">
                        <div class="-mar-bottom">
                            <form-slider name="calls" :min_value.sync="calls_min" :max_value.sync="calls_max" title="# of Calls"></form-slider>
                        </div>
                        <div class="-mar-bottom">
                            <form-slider name="emails" :min_value.sync="emails_min" :max_value.sync="emails_max" title="# of Emails"></form-slider>
                        </div>
                        <div class="-mar-bottom">
                            <form-slider name="texts_outgoing" :min_value.sync="texts_outgoing_min" :max_value.sync="texts_outgoing_max" title="# of Outgoing Texts"></form-slider>
                        </div>
                    </accordion-section>

                    <accordion-section title="Contact Info">
                        <div class="-mar-bottom">
                            <label class="field__label">First Name</label>
                            <input type="text" name="first_name" v-model="first_name" />
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">Last Name</label>
                            <input type="text" name="last_name" v-model="last_name" />
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">Email Address</label>
                            <input type="text" name="email" v-model="email" />
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">Phone Number</label>
                            <input type="text" name="phone" v-model="phone" />
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">Verified Email</label>
                            <form-select v-model="verified" placeholder="No Preferences" :options="options.verified" @input="clearInvalidWarnings"></form-select>
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">Have Phone Number</label>
                            <form-select v-model="has_phone" placeholder="No Preferences" :options="options.has_phone" @input="clearInvalidWarnings"></form-select>
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">Subscribed to Text Messages</label>
                            <form-select v-model="opt_texts" placeholder="No Preferences" :options="options.opt_texts" @input="clearInvalidWarnings"></form-select>
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">Preferred Contact Method</label>
                            <form-select v-model="contact_method" placeholder="No Preferences" :options="options.contact_method" @input="clearInvalidWarnings"></form-select>
                        </div>
                     </accordion-section>

                    <accordion-section title="Advanced Criteria">
                        <div class="-pad-bottom-xl">
                            <div class="-mar-bottom">
                                <label class="field__label">IP Address</label>
                                <input type="text" name="search_ip" v-model="search_ip" />
                            </div>
                            <div class="-mar-bottom">
                                <label class="field__label">Referer</label>
                                <input type="text" name="search_referer" v-model="search_referer" />
                            </div>
                            <div class="-mar-bottom">
                                <label class="field__label">Bounced</label>
                                <form-select v-model="bounced" placeholder="No Preferences" :options="options.bounced" @input="clearInvalidWarnings"></form-select>
                            </div>
                            <div class="-mar-bottom">
                                <label class="field__label">Reported SPAM</label>
                                <form-select v-model="fbl" placeholder="No Preferences" :options="options.fbl" @input="clearInvalidWarnings"></form-select>
                            </div>
                            <div class="-mar-bottom">
                                <label class="field__label">Lead Heat</label>
                                <form-selectize multiple v-model="heat" placeholder="Select heat..." :options="options.heat" @input="clearInvalidWarnings"></form-selectize>
                            </div>
                            <div class="-mar-bottom">
                                <label class="field__label">Connected Using</label>
                                <form-selectize multiple v-model="social" placeholder="Social Networks" :options="options.social" @input="clearInvalidWarnings"></form-selectize>
                            </div>
                        </div>
                    </accordion-section>
                </accordion>
            </div>
            <div>
                <div class="flyout-footer">
                    <div class="flyout-cta-fade"></div>
                    <button is="btn" type="submit" :modifiers="['primary']" class="btn--update">
                        <span class="flyout-cta-text">Update Filter</span>
                        <loader v-if="loading"></loader>
                    </button>
                    <button is="btn" class="clear--btn button--bordered" @click.native="resetFilters">
                        <span class="flyout-cta-text" alt="Clear Filters" title="Clear Filters">CLR</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="filterlead-flyout--shadow"></div>
  </div>
</template>


<style lang="scss" scoped>
    :root {
        --filterlead-flyout-background-color: #fff;
        --filterlead-flyout-width: 325px;
    }

    .flyout-filterleads {
        background-color: var(--filterlead-flyout-background-color);
        max-width: var(--filterlead-flyout-width);
        min-width: var(--filterlead-flyout-width);
        width: var(--filterlead-flyout-width);
        position: fixed;
        z-index: 1040;
        bottom: 0;
        right: 0;
        top: 0;
        transition: transform 210ms;
        transform: translate3d(100%, 0, 0);
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        padding-top: 1rem;
        padding-bottom: 1rem;
        overflow-y: scroll;

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
            top: 2.3125rem;
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

        &-section div {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }

    .flyout-header {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .flyout-content {
        height: 100vh;
        padding: 1rem;
        margin-bottom: 8px;
        min-height: calc(100vh - 200px);
        max-height: calc(100vh - 200px);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;

        .dropdown-wrap {
            width: 100%;
        }
    }

    @media (max-width: 500px) {
        .flyout-content {
            min-height: calc(100vh - 265px);
            max-height: calc(100vh - 265px);
        }
    }

    @supports(-webkit-overflow-scrolling: touch) {
        @media (max-height: 820px) {
            .flyout-content {
                min-height: calc(100vh - 240px);
                max-height: calc(100vh - 240px);
            }
        }

        @media (orientation: landscape) {
            .flyout-content {
                min-height: calc(100vh - 150px);
                max-height: calc(100vh - 150px);
            }
        }
    }

    .flyout-footer {
        position: relative;
        padding-left: 15px;
        padding-right: 31px;
    }

    .btn--update {
        position: relative;
        width: 80%;
    }

    .btn--update /deep/ .overlay {
        height: auto;
    }

    .btn--update /deep/ .loader {
        transform: none;
        right: 25px;
        top: 5px;
        width: 20px;
        height: 20px;
    }

    .clear--btn {
        width: 18%;
        height: 32px;
        background: transparent !important;
        border: 1px #90909d solid !important;
        padding: 0 !important;
        text-align: center;
        svg {
            margin-left: -8px;
            height: 22px !important;
            width: 22px !important;
            transform: scale(0.8);
        }
    }

    .flyout-cta-text {
        font-size: 16px;
        vertical-align: middle;
        text-shadow: 0 1px 0 rgba(#222, 0.35%);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: antialiased;
    }

    .flyout-cta-fade {
        color: #fff;
        position: absolute;
        left: 0;
        right: 0;
        top: -10px;
        z-index: 1005;
        padding-left: 10px;
        padding-bottom: 10px;
        margin-bottom: 35px;
        background: linear-gradient(rgba(255, 255, 255, 0.65), #fff);
    }

    input {
        width: 100%;
        padding: 6px;
    }

    /* Resets */

    /deep/ {
        .field {
            padding-top: 0;
            padding-bottom: 0;
        }

        .field__label {
            font-size: 16px;
        }

        .input,
        select,
        .selectize-input {
            padding: 6px;
        }

        .selectize-input {
            max-height: 69px;
            overflow: auto;
            background-size: 16px 16px;
            background-position: right 10px center;
            background-repeat: no-repeat;
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolygon points='5 9.21 11 9.21 8 12 5 9.21'/%3E%3Cpolygon points='11 6.79 5 6.79 8 4 11 6.79'/%3E%3C/svg%3E");
        }

        .ui-slider-horizontal {
            margin-left: 9px;
            margin-right: 9px;
        }

        .ui-slider-range,
        .ui-state-active {
            background: $primary;
            border-color: $primary;
        }
    }

    .-mar-bottom {
        margin-bottom: 12px;
    }

    .-pad-bottom-xl {
        padding-bottom: 180px !important;
    }

    .flyout-filterleads form {
        padding-bottom: 0 !important;
    }

</style>

<script>

    import store from 'store';
    import actions from 'vue/actions';
    import groupRenderSettings from 'common/groupPicker/renderSettings';

    const filters = store.getters['crm/leads/getFilters'];
    const fields = Object.keys(filters);
    const mapped = {};
    fields.forEach(name => {
        mapped[name] = {
            set (value) {
                store.dispatch('crm/leads/setFilter', {
                    name,
                    value
                });
            },
            get () {
                return store.getters['crm/leads/getFilters'][name];
            }
        };
    });

    export default {
        props: {
            options: {
                type: Object,
                required: true
            },
            name: {
                type: String,
                default: () => ''
            }
        },
        data: function () {
            return {
                groupRenderSettings,
                loading: false,
                invalid: []
            };
        },
        mounted: function () {
            // re-populate lead filters values to sync w/ query
            const filters = store.getters['crm/leads/getFilters'];
            const fields = Object.keys(filters);
            fields.forEach(name => {
                const value = filters[name];
                if (value) this[name] = value;
            });

            // Load search options
            if (!this.isLender) {
                store.dispatch('crm/leads/getGroupOptions');
            }
            store.dispatch('crm/leads/getActionPlanOptions');
            if (this.permission('crm.can.view.agents')) {
                store.dispatch('crm/leads/getAgentOptions');
            }
            if (this.permission('crm.can.view.lenders')) {
                store.dispatch('crm/leads/getLenderOptions');
            }
        },
        methods: {
            submitFilterLeads: function () {
                this.$emit('filter');
                this.loading = true;
                store.dispatch('crm/leads/updateLeadResults', {
                    next: null
                }).then(() => {
                    this.loading = false;
                    if (this.isMobile()) this.close();
                });
            },
            resetFilters: function () {
                store.dispatch('crm/leads/resetFilters');
                this.submitFilterLeads();
            },
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            close: function () {
                store.dispatch('flyouts/close');
            },
            getUser: async function () {
                let user = await actions.getUser();
                store.commit('SetUser', user);
            },
            permission: function (type) {
                return store.getters['auth/getUser'].permissions[type];
            },
            isMobile: function() {
                const defaultWidth = 325;
                const mobileBreakpoint = 500;
                const flyoutWidth = this.$refs.flyout.clientWidth;
                return  flyoutWidth !== defaultWidth && flyoutWidth <= mobileBreakpoint;
            }
        },
        computed: {
            is_open: function () {
                return this.name == store.getters['flyouts/isOpen'];
            },
            isLender: function () {
                const authUser = store.getters['auth/getUser'];
                return authUser.type === 'Lender';
            },
            ...mapped
        }
    };
</script>
