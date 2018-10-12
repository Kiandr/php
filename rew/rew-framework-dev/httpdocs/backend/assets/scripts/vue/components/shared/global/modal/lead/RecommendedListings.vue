<template>
    <div class="w1/1">
        <div class="w1/1">
            <div class="article">
                <div class="article__body">
                    <div class="article__thumb">
                        <thumb :context="[lead.first_name, lead.last_name]" :score="lead.score" />
                    </div>
                    <div class="article__content">
                        <a :href="'./lead/summary/?id=' + lead.id" class="article__title text text--strong">
                            {{ lead.first_name }} {{ lead.last_name }}
                        </a>
                        <div v-if="this.actionTimestamp && this.actionTimestamp != '0000-00-00 00:00:00'" :title="lead.timestamp_active" class="text text--mute actioned">
                            {{ this.actionTitle }} {{ utc(this.actionTimestamp) | moment('from') }}
                        </div>
                    </div>
                </div>
            </div>
            <h4 class="modal__subtitle">Recommended</h4>
            <button @click.native="toggleRecomend()" is="btn" :modifiers="['primary']" class="btn--action"  tabindex="0">
                <span>Recommend Listing</span>
            </button>
        </div>
        <!-- When the content is loading -->
        <loader v-if="this.loading"></loader>
        <collapse v-else-if="listings.length" :defaultHeight="255" :class="{'hide-cta': listings.length <= 4}">
            <div class="listings__wrap">
                <div class="w1/3 listings" :class="{ hide: !isHidden}">
                    <listing-result @click.native="openListing(listing.url)" :listing="listing" :key="listing.ListingMLS" v-for="listing in listings"></listing-result>
                </div>
            </div>
        </collapse>
        <div v-else>
            <h3>Oh no, there are no Listings!</h3>
        </div>
        <div class="w1/1" :class="{ hide: isHidden}">
            <recommend-form :submitHandler="recommendListing"></recommend-form>
        </div>
    </div>
</template>

<script>
    import getLeadLinkFromLeadData from 'vue/utils/getLeadLinkFromLeadData';
    import RecommendForm from 'vue/components/leads/actions/RecommendForm.vue';
    import ListingResult from 'vue/components/shared/listings/ListingResult.vue';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';
    import dateUtils from 'utils/date';
    import actions from 'vue/actions';
    import store from 'store';

    export default {
        components: {
            'recommend-form': RecommendForm,
            'listing-result': ListingResult
        },
        data: function () {
            return {
                listings: [],
                loading: true,
                actionTitle: '',
                actionTimestamp: '',
                hidden: true
            };
        },
        mounted: function () {
            this.getListings();
            this.actionTitle = this.lead.last_action ? this.lead.last_action.title : 'Joined';
            this.actionTimestamp = this.lead.last_action ? this.lead.last_action.timestamp : (this.lead.timestamp_created != null ? this.lead.timestamp_created : null);
        },
        computed: {
            context: function () {
                return store.getters['modal/getContext'];
            },
            lead: function () {
                return store.getters['crm/leads/getResult'](
                    this.context.lead_id
                );
            },
            isHidden: function () {
                return this.hidden;
            }
        },
        methods: {
            getListings: async function () {
                const type = 'recommended';

                await actions.getListings(
                    this.context.lead_id,
                    type
                ).then(data => {
                    this.listings = data;
                    this.loading = false;
                }).catch(error => {
                    showErrors([error.message]);
                    throw error; // Re-throw
                });
            },
            utc: function (time) {
                return dateUtils.toUtc(time);
            },
            toggleRecomend: function () {
                this.hidden = !this.hidden;
            },
            recommendListing: function ({ mls_number, message, send_message, feed}) {

                // Get context data
                const lead_id = this.context.lead_id;
                const leadLink = getLeadLinkFromLeadData(this.lead);

                // Recommend listing
                actions.recommendListing(
                    lead_id,
                    mls_number,
                    message,
                    send_message,
                    feed
                ).then(() => {

                    this.message = '';
                    this.mls_number = '';
                    this.toggleRecomend();

                    // Show success message
                    showSuccess([`Listing has been recommended to ${leadLink}.`], undefined, {
                        close: true,
                        expires: 10000
                    });

                    // Failed to complete
                }).catch(error => {
                    showErrors([error.message]);
                });

            },
            openListing: function (url) {
                window.open(url);
            }
        }
    };
</script>

<style scoped>
    .listings {
        overflow: hidden;
    }

    .listings__wrap {
        overflow: hidden;
        cursor: pointer;
    }

    .hide-cta /deep/ .collapse {
        height: auto !important;
        overflow: visible !important;
    }

    .hide-cta /deep/ .more {
        display: none;
    }

    /deep/ .collapse {
        margin: 0 -10px 12px -10px;
    }

    .hide {
        display: none;
    }

    .save__listing {
        justify-content: space-between;
        align-items: center;

        label {
            font-size: $font-size-base;
        }
    }
</style>
