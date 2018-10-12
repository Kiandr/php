<template>
    <card class="lead lead__results">
        <svg v-if="store_leads_selected.includes(lead.id)" class="lead-icon lead-icon_checked" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 31.97 32" width="24" height="24" @click="toggleSelectedLead">
          <title>Click to Unselect</title>
          <path d="M376.49,446.77a16,16,0,1,1-16,16A16,16,0,0,1,376.49,446.77Z" transform="translate(-360.5 -446.77)" fill-rule="evenodd" fill="#2592f8"></path>
          <path d="M374.34,470.31a1.15,1.15,0,0,1-.82-0.34l-5.15-5.15A1.15,1.15,0,1,1,370,463.2l4.28,4.28,8.77-9.91a1.15,1.15,0,0,1,1.73,1.53l-9.58,10.82a1.16,1.16,0,0,1-.83.39h0Z" transform="translate(-360.5 -446.77)" fill="#fff"></path>
        </svg>
        <svg v-else="" class="lead-icon lead-icon_unchecked" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="24" height="24" @click="toggleSelectedLead">
          <title>Click to Select</title>
          <path fill="#a1a1a1" d="M422,552a15.93,15.93,0,0,1-6.11-1.2,0.94,0.94,0,1,1,.72-1.74,14,14,0,0,0,5.37,1.06,1,1,0,0,1,1,.94,0.93,0.93,0,0,1-.93.94v0h0Zm5.77-1.15a0.94,0.94,0,0,1-.36-1.81A14.08,14.08,0,0,0,432,546a0.94,0.94,0,0,1,1.33,1.33,16,16,0,0,1-5.18,3.48A0.93,0.93,0,0,1,427.74,550.86Zm-16.41-3.23a0.94,0.94,0,0,1-.66-0.27,15.94,15.94,0,0,1-3.48-5.18,0.94,0.94,0,1,1,1.74-.73A14.06,14.06,0,0,0,412,546,0.94,0.94,0,0,1,411.33,547.63Zm24.55-4.93a0.94,0.94,0,0,1-.87-1.3,14,14,0,0,0,1.06-5.39,1,1,0,0,1,.94-0.95,0.93,0.93,0,0,1,.94.93v0a15.89,15.89,0,0,1-1.21,6.11A0.94,0.94,0,0,1,435.88,542.7Zm-29-5.72a0.92,0.92,0,0,1-.94-0.92v0a15.9,15.9,0,0,1,1.19-6.08,0.94,0.94,0,0,1,1.74.72,14,14,0,0,0-1.05,5.36A1,1,0,0,1,406.89,537Zm29-5.8a0.94,0.94,0,0,1-.87-0.58,14.08,14.08,0,0,0-3.07-4.58,0.94,0.94,0,0,1,1.33-1.33,16,16,0,0,1,3.47,5.18A0.94,0.94,0,0,1,435.87,531.18Zm-24.6-4.84a0.94,0.94,0,0,1-.67-1.6,15.92,15.92,0,0,1,5.17-3.49,0.94,0.94,0,0,1,.73,1.74,14,14,0,0,0-4.56,3.08A0.94,0.94,0,0,1,411.27,526.35ZM427.71,523a0.94,0.94,0,0,1-.36-0.07,14,14,0,0,0-5.4-1.07,1,1,0,0,1-1-.94,0.92,0.92,0,0,1,.91-0.94H422a15.92,15.92,0,0,1,6.12,1.21A0.94,0.94,0,0,1,427.71,523Z" transform="translate(-405.95 -520.02)"></path>
        </svg>
        <card-section>
            <div class="article">
                <div class="article__body">
                    <div class="info">
                        <div class="article__thumb">
                            <thumb :context="[lead.first_name, lead.last_name]" :modifiers="['lg']" />
                        </div>
                        <div class="article__content">
                            <a :href="'./lead/summary/?id=' + lead.id" class="article__title text text--strong">
                                {{ full_name }}
                            </a>
                            <div v-if="this.actionTimestamp && this.actionTimestamp != '0000-00-00 00:00:00'" :title="lead.timestamp_active" class="text text--mute actioned">
                                {{ this.actionTitle }} {{ utc(this.actionTimestamp) | moment('from') }}
                            </div>
                            <div class="lead__score">
                                <div class="lead__score__bar" :style="`width: ${lead.score}%`"></div>
                            </div>
                        </div>
                    </div>
                    <div class="actions">
                        <button is="btn" :modifiers="['ghost']" class="btn--action" tabindex="0">
                            <div class="token">
                                <icon name=icon--phone :width="16" :height="16" />
                                <span class="token__label">{{ lead.num_calls }} <span class="hidden--sm">Calls</span></span>
                            </div>
                        </button>
                        <button is="btn" :modifiers="['ghost']" class="btn--action" tabindex="0">
                            <div class="token">
                                <icon name=icon--email :width="16" :height="16" />
                                <span class="token__label">{{ lead.num_emails }} <span class="hidden--sm">Emails</span></span>
                            </div>
                        </button>
                        <button is="btn" :modifiers="['ghost']" class="btn--action" tabindex="0">
                            <div class="token">
                                <icon name=icon--message :width="16" :height="16" />
                                <span class="token__label">{{ lead.num_texts }} <span class="hidden--sm">Texts</span></span>
                            </div>
                        </button>
                        <button is="btn" :modifiers="['ghost']" class="btn--action">
                            <div class="token">
                                <icon name=icon--inquiries :width="16" :height="16" />
                                <span class="token__label">{{ lead.num_forms }} <span class="hidden--sm">Inquiries</span></span>
                            </div>
                        </button>
                        <div class="token">
                            <a v-if="hasBombbomb(settings.partners)" @click="openBombbomb(settings.partners)" href="#" title="BombBomb Quick Send"><img src="/backend/img/partners/bombbomb-32.png" class="icon--sm"></a>
                        </div>
                    </div>
                </div>
                <div class="lead__info">
                    <span class="token">
                        <span class="token__thumb thumb thumb--tiny">
                            <a v-if="lead_is_assigned_to_self === 'Agent' || lead_is_assigned_to_viewable_agent" :href="'../agents/agent/summary/?id=' + lead.agent.id">
                                <img v-if="lead.agent.image" :src="lead.agent.image" :alt="[lead.agent.first_name + ' ' + lead.agent.last_name]" class="image--agent" />
                                <img v-else src="/thumbs/312x312/uploads/agents/na.png" alt="" />
                            </a>
                            <img v-else-if="lead.agent.image" :src="lead.agent.image" :alt="[lead.agent.first_name + ' ' + lead.agent.last_name]" class="image--agent" />
                            <img v-else src="/thumbs/312x312/uploads/agents/na.png" alt="" />
                        </span>
                        <span class="token__label test--strong">
                            <a v-if="lead_is_assigned_to_self === 'Agent'" :href="'../agents/agent/summary/?id=' + lead.agent.id" class="agent__title">Me</a>
                            <a v-else-if="lead_is_assigned_to_viewable_agent" :href="'../agents/agent/summary/?id=' + lead.agent.id" class="agent__title">{{ lead.agent.first_name }} {{ lead.agent.last_name }}</a>
                            <span v-else class="agent__title">{{ lead.agent.first_name }} {{ lead.agent.last_name }}</span>
                        </span>
                    </span>
                    <span v-if="lead.lender" class="token">
                        <span class="token__thumb thumb thumb--tiny">
                            <a v-if="lead_is_assigned_to_self === 'Lender' || can_view_assigned_lenders" :href="'../lenders/lender/summary/?id=' + lead.lender.id">
                                <img v-if="lead.lender.image" :src="lead.lender.image" :alt="[lead.lender.first_name + ' ' + lead.lender.last_name]" class="image--agent" />
                                <img v-else src="/thumbs/312x312/uploads/agents/na.png" alt="" />
                            </a>
                            <img v-else-if="lead.lender.image" :src="lead.lender.image" :alt="[lead.lender.first_name + ' ' + lead.lender.last_name]" class="image--agent" />
                            <img v-else src="/thumbs/312x312/uploads/agents/na.png" alt="" />
                        </span>
                        <span class="token__label text--strong">
                            <a v-if="lead_is_assigned_to_self === 'Lender'" :href="'../lenders/lender/summary/?id=' + lead.lender.id" class="agent__title">Me</a>
                            <a v-else-if="can_view_assigned_lenders" :href="'../lenders/lender/summary/?id=' + lead.lender.id" class="agent__title">{{ lead.lender.first_name }} {{ lead.lender.last_name }}</a>
                            <span v-else class="agent__title">{{ lead.lender.first_name }} {{ lead.lender.last_name }}</span>
                        </span>
                    </span>
                    <div v-if="lead.groups">
                        <span v-for="group in lead.groups" class="token group">
                            <span class="token__thumb thumb--tiny" :class="['-bg-' + group.style]"></span>
                            <span class="token__label">{{ group.name }}</span>
                        </span>
                    </div>
                </div>
                <collapse :defaultHeight="60" :onClick="updateListingsStats" class="article__foot">
                    <div class="keyvals">
                        <div ref="leadDetails">
                            <div class="keyvals__row">
                                <span class="keyvals__key">Email</span>
                                <span v-if="lead.email" class="keyvals__val keyvals__val--email" :title="`${lead.email ? lead.email : ''}`">
                                    <a @click="showEmailModal" href="#">{{ lead.email }}</a>
                                </span>
                                <span v-else class="keyvals__val">
                                    <span>-</span>
                                </span>
                            </div>
                            <div class="keyvals__row">
                                <span class="keyvals__key">Phone</span>
                                <span v-if="lead.phone || lead.phone_cell" class="keyvals__val">
                                    <a @click="showCallModal" href="#">
                                        {{ lead.phone || lead.phone_cell}}
                                    </a>
                                    <a @click="showTextModal" href="#">
                                        <icon name=icon--message :width="16" :height="16" class="primary" />
                                        <span class="cell-text__label">Text</span>
                                    </a>
                                </span>
                                <span v-else class="keyvals__val">-</span>
                            </div>
                            <div class="keyvals__row">
                                <span class="keyvals__key">Last Touch</span>
                                <span class="keyvals__val touch">
                                    <span v-if="lead.last_touched && lead.last_touched.timestamp != '0000-00-00 00:00:00'">
                                        <span>{{ lead.last_touched.method }}ed</span>
                                        {{ utc(lead.last_touched.timestamp) | moment('from') }}
                                    </span>
                                    <span v-else>Never</span>
                                </span>
                            </div>
                            <div class="keyvals__row">
                                <span class="keyvals__key">Joined</span>
                                <span class="keyvals__val joined">
                                    <span v-if="lead.timestamp_created && lead.timestamp_created != '0000-00-00 00:00:00'">
                                        {{ utc(lead.timestamp_created) | moment('from') }}
                                    </span>
                                    <span v-else>-</span>
                                </span>
                            </div>
                            <div class="keyvals__row">
                                <span class="keyvals__key">Value</span>
                                <span class="keyvals__val">
                                    <span v-if="lead.value">${{ formatPrice(lead.value) }}</span>
                                    <span v-else>-</span>
                                </span>
                            </div>
                            <div class="keyvals__row">
                                <span class="keyvals__key">Origin</span>
                                <span class="keyvals__val">
                                    <span v-if="lead.source">{{ lead.source }}</span>
                                    <span v-else>Direct Traffic</span>
                                </span>
                            </div>
                            <div class="keyvals__row">
                                <span class="keyvals__key">Action Plan</span>
                                <span class="keyvals__val" :class="{'keyvals__val--plans' : lead.action_plans}">
                                    <span v-if="lead.action_plans">
                                        <span v-for="plan in lead.action_plans" class="keyvals__val--plan">
                                            {{ plan.name }}<span v-if="lead.action_plans.length > 1 && Array.indexOf(plan) !== lead.action_plans.length - 1">,</span>
                                        </span>
                                    </span>
                                    <span v-else>Not yet Assigned! <a href="#" v-if="canAssignLeadToActionPlan" @click="assignActions">Click here to assign</a></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="lead__listings">
                        <div class="lead__listings__row">
                            <div @click="showInquiryModal('inquiries')" class="lead__listings__wrap">
                                <span class="lead__listings__text">Listing Inquiries</span>
                                <span class="lead__listings__count">{{ listingsStats ? listingsStats.inquiries.count : 0 }}</span>
                                <img :src="listingsStats && listingsStats.inquiries.last_listing ? listingsStats.inquiries.last_listing.image_url : this.image_not_found" alt="Listing inquiries" />
                            </div>
                            <div @click="showInquiryModal('showings')" class="lead__listings__wrap">
                                <span class="lead__listings__text">Showing Requests</span>
                                <span class="lead__listings__count">{{ listingsStats ? listingsStats.showings.count : 0 }}</span>
                                <img :src="listingsStats && listingsStats.showings.last_listing ? listingsStats.showings.last_listing.image_url : this.image_not_found" alt="Listing showing requests" />
                            </div>
                        </div>
                        <div class="lead__listings__row">
                            <div @click="showListingModal('favorites')" class="lead__listings__wrap">
                                <span class="lead__listings__text">Favs</span>
                                <span class="lead__listings__count">{{ listingsStats ? listingsStats.favorites.count : 0 }}</span>
                                <img :src="listingsStats && listingsStats.favorites.last_listing ? listingsStats.favorites.last_listing.image_url : this.image_not_found" alt="Favorite listings" />
                            </div>
                            <div @click="showListingModal('recommended')" class="lead__listings__wrap">
                                <span class="lead__listings__text">Recommended</span>
                                <span class="lead__listings__count">{{ count_recommended }}</span>
                                <img :src="listingsStats && listingsStats.recommended.last_listing ? listingsStats.recommended.last_listing.image_url : this.image_not_found" alt="Recommended listings" />
                            </div>
                            <div @click="showListingModal('views')" class="lead__listings__wrap">
                                <span class="lead__listings__text">Views</span>
                                <span class="lead__listings__count">{{ listingsStats ? listingsStats.views.count : 0 }}</span>
                                <img :src="listingsStats && listingsStats.views.last_listing ? listingsStats.views.last_listing.image_url : this.image_not_found" alt="Viewed listings" />
                            </div>
                        </div>
                    </div>
                </collapse>
            </div>
            <div class="actions" v-if="lead_is_pending && !isLender && !isAssociate">
                <div class="actions--pending">
                    <button @click.native="acceptLead(lead)" is="btn" :modifiers="['primary']" class="-mar-right" tabindex="0" v-if="can_accept_lead">
                        <icon name="icon--thumbs-up" :width="16" :height="16" :modifiers="['invert']" />
                        <span class="text">Accept</span>
                   </button>
                   <a :href="'./lead/summary/?id=' + lead.id" class="btn btn--ghost details" tabindex="0">
                       <icon name="icon--eye" :width="16" :height="16" class="primary" />
                       <span class="text primary">Details</span>
                  </a>
                </div>
            </div>
            <div v-else-if="lead_is_unassigned && !lead.notes" class="actions--unassigned">
                <a :href="'./lead/summary/?id=' + lead.id" class="btn--ghost details">
                    <icon name="icon--eye" :width="16" :height="16" class="primary" />
                    <span class="text primary">Details</span>
                </a>
            </div>
            <div v-else class="actions--accepted">
                <lead-quick-notes :lead="lead"></lead-quick-notes>
            </div>
        </card-section>
    </card>
</template>

<style lang="scss" scoped>
    .lead__results {
        position: relative;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .lead__results:not(:last-of-type) {
        margin-bottom: 16px;
    }

    .lead-icon {
        transform: translateX(0);
        position: absolute;
        z-index: 10;
        transition: 350ms;
        top: 50%;
        margin-top: -24px;
    }

    .lead__score {
        background-color: #ededf0;
        border-radius: 3px;
        height: 5px;

        @media (min-width: 1060px) {
            width: 280px;
        }
    }

    .lead__score__bar {
        background-color: $primary;
        display: block;
        border-radius: 3px;
        height: 5px;
    }

    .article {
        margin-bottom: 8px;
    }

    .article__body {
        align-items: flex-start;
        justify-content: space-between;

        @media(max-width: 439px) {
            flex-direction: column;
        }
    }

    .article__title,
    .agent__title {
        font-weight: bold;
        color: inherit;
        text-decoration: none;
    }

    .article__foot {
        display: flex;
        justify-content: space-between;
        padding-top: 24px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;

        &.-is-open {
            flex-direction: column;
        }
    }

    .article__foot /deep/ .more {
        margin-top: 8px;
    }

    .article__body .info {
        display: flex;
        margin-right: 24px;
    }

    .card--section {
        position: relative;
        z-index: 20;
        background-color: #fff;
    }

    .keyvals {
        width: auto;
        display: block;
        overflow: hidden;
        white-space: nowrap;
        transition: height 0.25s;
    }

    @media (min-width: 1020px) and (max-width: 1051px) {
        .keyvals {
            width: 60%;
        }
    }

    .keyvals__row {
        display: flex;
    }

    .keyvals__key {
        display: initial;
        font-size: 13px;
        min-width: 25%;
        padding: 4px 8px 0 0;
        text-transform: uppercase;

        @media(max-width: 398px) {
            min-width: 30%;
        }
    }

    .keyvals__val {
        padding: 4px 8px 0;
        margin-left: 20px;

        @media(max-width: 765px) {
            display: inline;
            white-space: normal;
        }
    }

    .keyvals__val--email,
    .keyvals__val--email a {
        display: block;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .keyvals__val a {
        color: #2692f7;
        margin-right: 4px;
        text-decoration: none;
    }

    /deep/ .collapse__container {
        display: flex;
        justify-content: space-between;

        @media (max-width: 1019px) {
            flex-direction: column;
        }
    }

    .lead__listings {
        opacity: 0;
        visibility: hidden;
        overflow: hidden;
        width: 368px;
        margin-left: 20px;

        @media (max-width: 1019px) {
            width: 100%;
            margin: 20px 0 0;
            transition: 0.05s opacity, 0.05s visibility;
        }
    }

    .-is-open .lead__listings {
        opacity: 1;
        visibility: visible;
    }

    .lead__listings__wrap {
        width: 100%;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .lead__listings__wrap::after {
        content: "";
        display: block;
        padding-bottom: 66.666%;
    }

    .lead__listings__wrap img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .lead__listings__row {
        display: flex;
    }

    .lead__listings__row:first-of-type {
        border-bottom: 2px solid #fff;
    }

    .lead__listings img {
        display: block;
        width: 100%;
        height: 100%;
        border: 1px solid #fff;
    }

    .lead__listings__count {
        font-size: 12px;
        font-weight: bold;
        position: absolute;
        top: 10px;
        right: 8px;
        z-index: 1020;
        width: 32px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid #424042;
        background-color: #424042;
        color: #fff;
    }

    .lead__listings__text {
        color: #fff;
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1020;
        padding-left: 10px;
        padding-bottom: 10px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        background: linear-gradient(transparent, #2b2b2b);
    }

    .keyvals__val--plans {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .keyvals__val--plan {
        padding-right: 6px;
    }

    .phone .icon {
        fill: #2692f7;
        margin-bottom: 3px;
    }

    @media (max-width: 334px) {
        .cell-text__label {
            display: none;
        }
    }

    .details {
        color: #2692f7;
    }

    .actions {
        overflow: hidden;
        position: relative;

        @media(max-width: 439px) {
            margin-top: 12px;
        }
    }

    .actions .text,
    .actions .icon {
        vertical-align: middle;
    }

    .actions--unassigned .text {
        vertical-align: middle;
    }

    .actions--unassigned .btn--ghost {
        padding: 9px 14px;
        text-decoration: none;
    }

    .btn--action .token__label {
        text-transform: capitalize;
        cursor: auto;
    }

    .lead__info {
        display: flex;
        flex-wrap: wrap;
        padding-top: 10px;
    }

    .lead__info .image--agent {
        object-position: top;
    }

    .lead__info .group {
        border: 0;
        margin: 0;
        padding: 0 0 4px;
        overflow: visible;
    }

    .lead__info .group::before {
        display: none;
    }

    .lead__info .token {
        margin-right: 4px;
    }

    .lead__info .token__label {
        font-size: 16px;
    }

    /**
     * Temp fix to disable styles for click interactions
     * https://realestatewebmasters.atlassian.net/browse/RC-179
     */
    .btn:active,
    .btn:focus,
    .btn:hover {
        background-color: inherit;
        box-shadow: none;
        cursor: auto;
    }

    /* Resets */

    .article__thumb /deep/ .thumb-text-center {
        display: flex;
    }

    @media(max-width: 480px) {
        .article__content .actioned {
            font-size: 14px;
            white-space: nowrap;
        }
    }

    .token__label {
        padding-left: 4px;
        padding-right: 4px;
    }

    .actioned {
        margin-bottom: 6px;
    }

    .actioned,
    .touch,
    .joined {
        text-transform: capitalize;
    }

    .card {
        display: block !important;
        overflow: visible;
    }

    .icon {
        width: 16px !important;
        height: 16px !important;
    }

    .primary,
    .icon.primary {
        fill: #2692f7;
        color: #2692f7;
    }

    .btn--action {
        padding-left: 0;
        padding-right: 0;
    }

    .-mar-right {
        margin-right: 12px;
    }

    @media (max-width: 680px) {
        .hidden--sm {
            display: none;
        }
    }
</style>

<script>

    import RecommendedListings from 'vue/components/shared/global/modal/lead/RecommendedListings.vue';
    import FavoriteListings from 'vue/components/shared/global/modal/lead/FavoriteListings.vue';
    import ViewedListings from 'vue/components/shared/global/modal/lead/ViewedListings.vue';
    import store from 'store';
    import getLeadLink from 'vue/utils/getLeadLink';
    import text from 'vue/components/shared/global/modal/lead/Text.vue';
    import call from 'vue/components/shared/global/modal/lead/Call.vue';
    import email from 'vue/components/shared/global/modal/lead/Email.vue';
    import ListingsInquiries from 'vue/components/shared/global/modal/lead/ListingsInquiries.vue';
    import ListingShowings from 'vue/components/shared/global/modal/lead/ListingShowings.vue';
    import LeadQuickNotes from 'vue/components/leads/lead/LeadQuickNotes.vue';
    import showSuccess from 'utils/showSuccess';
    import dateUtils from 'utils/date';

    export default {
        props: {
            lead: {
                type: Object,
                default: () => {}
            },
            settings: {
                type: Object
            }
        },
        data: function () {
            return {
                open: false,
                listingsLoaded: false,
                actionTitle: this.lead.last_action ? this.lead.last_action.title : 'Joined',
                actionTimestamp: this.lead.last_action ? this.lead.last_action.timestamp : (this.lead.timestamp_created != null ? this.lead.timestamp_created : null),
                image_not_found : '/img/no-photo.png'
            };
        },
        methods: {
            acceptLead: async function (lead) {
                store.dispatch('crm/leads/acceptLead', {
                    lead_id: lead.id
                }).then(() => {
                    const leadLink = getLeadLink.fromLeadData(lead);
                    showSuccess([`${leadLink} has successfully been accepted.`], undefined, {
                        close: true,
                        expires: 10000
                    });
                });
            },
            toggleSelectedLead: function () {
                store.dispatch('crm/leads/toggleSelected', {
                    lead_id: this.lead.id
                });
            },
            toggleSnapshot: function () {
                this.snapshot.expanded = !this.snapshot.expanded;
            },
            showCallModal: function () {
                store.dispatch('modal/open', {
                    title: 'Phone Log',
                    content: call,
                    context: {
                        lead_id: this.lead.id
                    }
                });
            },
            showEmailModal: function () {
                store.dispatch('modal/open', {
                    title: 'Send Email',
                    content: email,
                    context: {
                        lead_id: this.lead.id
                    }
                });
            },
            showTextModal: function () {
                store.dispatch('modal/open', {
                    title: 'Text Message',
                    content: text,
                    context: {
                        lead_id: this.lead.id,
                        message: ''
                    }
                });
            },
            formatPrice(value) {
                let val = (value/1).toFixed(0);
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            },
            utc: function (time) {
                return dateUtils.toUtc(time);
            },
            updateListingsStats: function () {
                if (this.listingsLoaded) return;
                const lead_id = this.lead.id;
                store.dispatch('crm/leads/updateLeadListings', {
                    lead_id
                });
                this.listingsLoaded = true;
            },
            showListingModal: function (type) {
                const num_favorites = this.listingsStats.favorites.count;
                const num_recommended = this.listingsStats.recommended.count;
                const num_views = this.listingsStats.views.count;
                // todo: make these components
                const Favorite = FavoriteListings;
                const Recommended = RecommendedListings;
                const Viewed = ViewedListings;
                let activeContent = 0;
                if (type === 'favorites') activeContent = 0;
                if (type === 'recommended') activeContent = 1;
                if (type === 'views') activeContent = 2;
                store.dispatch('modal/open', {
                    title: 'Listings Activity',
                    activeContent,
                    content: [
                        {title: `${num_favorites} Favs`, content: Favorite},
                        {title: `${num_recommended} Recommended`, content: Recommended},
                        {title: `${num_views} Views`, content: Viewed}
                    ],
                    context: {
                        lead_id: this.lead.id
                    }
                });
            },
            assignActions: function () {
                store.dispatch('flyouts/open', {
                    flyout: 'Action Plans',
                    context: {
                        lead_id: this.lead.id
                    }
                });
            },
            showInquiryModal: function (type) {
                const num_inquiries = this.listingsStats.inquiries.count;
                const num_showings = this.listingsStats.showings.count;
                // todo: make these components
                const Inquiries = ListingsInquiries;
                const Showings = ListingShowings;
                let activeContent = 0;
                if (type === 'inquiries') activeContent = 0;
                if (type === 'showings') activeContent = 1;
                store.dispatch('modal/open', {
                    title: 'Listings Activity',
                    activeContent,
                    content: [
                        { title: `${num_inquiries} Inquiries`, content: Inquiries},
                        { title: `${num_showings} Showings`, content: Showings},
                    ],
                    context: {
                        lead_id: this.lead.id
                    }
                });
            },
            permission: function (type) {
                return store.getters['auth/getUser'].permissions[type];
            },
            openBombbomb: function (partners) {
                let bombbomb = partners.bombbomb;
                if (typeof bombbomb === 'undefined') return false;
                var subject = 'Hey there!',
                    videoURL = 'http://app.bombbomb.com/app/?module=emails&page=QuickSend&popup=1&subject=' + encodeURIComponent(subject) + '&comma_delim_emails=' + encodeURIComponent(this.lead.email),
                    loginURL = 'https://app.bombbomb.com/app/?module=login&actn=login&api_key=' + encodeURIComponent(bombbomb.api_key) + '&redir=' + encodeURIComponent(videoURL),
                    popupURL = loginURL;

                let popup = window.open(popupURL, 'bombbomb' + this.lead.id, 'height=500,width=830,scrollbars=1,location=no,toolbar=no,resizable=yes');
                if (popup) {
                    popup.focus();
                }
            },
            hasBombbomb: function (partners) {
                return partners !== null && typeof partners.bombbomb !== undefined;
            }
        },
        computed: {
            count_recommended: function () {
                return (typeof this.listingsStats.recommended !== 'undefined' ? this.listingsStats.recommended.count : 0);
            },
            can_accept_lead: function () {
                const authId = store.getters['auth/getId'];
                const authType = store.getters['auth/getType'];
                let agent = this.lead.agent.id;
                return (
                    agent === authId
                    && authType === 'Agent'
                );
            },
            can_view_assigned_lenders: function () {
                const authUser = store.getters['auth/getUser'];
                return (authUser.permissions['crm.can.view.lenders'] === true);
            },
            full_name: function () {
                let first_name = this.lead.first_name;
                let last_name = this.lead.last_name;
                return `${first_name} ${last_name}`;
            },
            lead_is_pending: function () {
                return this.lead.status === 'pending';
            },
            lead_is_assigned_to_viewable_agent: function () {
                const authUser = store.getters['auth/getUser'];
                return (
                    (authUser.type === 'Agent' && parseInt(authUser.id) === 1)
                    || (parseInt(this.lead.agent.id) !== 1 && authUser.permissions['crm.can.view.agents'] === true)
                );
            },
            lead_is_assigned_to_self: function () {
                const authId = store.getters['auth/getId'],
                    authType = store.getters['auth/getType'];
                if (
                    (this.lead.agent && this.lead.agent.id === authId && authType === 'Agent')
                    || (this.lead.lender && this.lead.lender.id === authId && authType === 'Lender')
                ) {
                    return authType;
                }
                return false;
            },
            lead_is_unassigned: function () {
                return this.lead.status === 'unassigned';
            },
            store_leads_selected: function () {
                // @todo: this is just too much info for this component to know
                // instead - should be passed by parent as 'is-selected' prop
                return store.getters['crm/leads/getSelected'];
            },
            isLender: () => {
                const authUser = store.getters['auth/getUser'];
                return authUser.type === 'Lender';
            },
            listingsStats: function () {
                const getListings = store.getters['crm/leads/getListings'];
                return getListings(this.lead.id);
            },
            isAssociate: () => {
                const authUser = store.getters['auth/getUser'];
                return authUser.type === 'Associate';
            },
            canAssignLeadToActionPlan: function () {
                const authId = store.getters['auth/getId'];
                const authType = store.getters['auth/getType'];
                let agent = this.lead.agent.id;
                return (
                    (
                        agent === authId
                        && authType === 'Agent'
                    )
                    && this.permission('crm.can.assign.own.action.plans')
                )
                || this.permission('crm.can.assign.all.action.plans');
            }
        },
        components: {
            'lead-quick-notes': LeadQuickNotes
        }
    };
</script>
