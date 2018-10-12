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
            <h4 class="modal__subtitle">Viewed</h4>
        </div>
        <!-- When the content is loading -->
        <loader v-if="this.loading"></loader>
        <collapse v-else-if="listings.length" :defaultHeight="255" :class="{'hide-cta': listings.length <= 4}">
            <div class="listings__wrap">
                <div class="w1/3 listings">
                    <listing-result @click.native="openListing(listing.url)" :listing="listing" :key="listing.ListingMLS" v-for="listing in listings"></listing-result>
                </div>
            </div>
        </collapse>
        <div v-else>
            <h3>Oh no, there are no Listings!</h3>
        </div>
    </div>
</template>

<script>
    import ListingResult from 'vue/components/shared/listings/ListingResult.vue';
    import showErrors from 'utils/showErrors';
    import dateUtils from 'utils/date';
    import actions from 'vue/actions';
    import store from 'store';

    export default {
        components: {
            'listing-result': ListingResult
        },
        data: function () {
            return {
                listings: [],
                loading: true,
                actionTitle: '',
                actionTimestamp: ''
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
        },
        methods: {
            getListings: async function () {
                const type = 'views';

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
</style>
