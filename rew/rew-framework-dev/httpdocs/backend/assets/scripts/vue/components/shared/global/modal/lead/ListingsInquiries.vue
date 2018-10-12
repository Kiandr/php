<template>
    <div class="w1/1">
        <div class="lead__info">
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
            <button @click.native="closeListing()" is="btn" :modifiers="['ghost']" class="btn--action" :class="{ hide: isHidden} " tabindex="0">
                <icon name=icon--left :width="16" :height="16" />
            </button>
        </div>
        <!-- When the content is loading -->
        <loader v-if="loader"></loader>
        <div v-else-if="listings.length" :class="{hide: !isHidden}">
            <collapse :defaultHeight="255" :class="{'hide-cta': listings.length <= 4}">
                <div class="listings__wrap">
                    <div class="listings">
                        <div v-for="listing in listings" @click="openListing(listing)" class="listing__wrap">
                            <listing-result :listing="listing" :key="listing.ListingMLS"></listing-result>
                        </div>
                    </div>
                </div>
            </collapse>
        </div>
        <div v-if="selectedListing && selectedListing.Address" class="selected-address"><a :href="selectedListing.url" target="_blank">{{ selectedListing.Address }}, {{ selectedListing.AddressCity }}, {{ selectedListing.AddressState }}</a></div>
        <div class="listing__form" :class="{ hide: isHidden}">
            <div class="listing">
                <div class="listing__image" :style="{'background-image': `url(${selectedListing.ListingImage})`}"></div>
                <h3 class="listing__price">${{ selectedListing.ListingPrice }}</h3>
                <h3 class="listing__address">{{ selectedListing.Address }}, {{ selectedListing.AddressCity }}, {{ selectedListing.AddressState }}</h3>
                <p class="listing__info">{{ selectedListing.NumberOfBedrooms }} Bedrooms, {{ selectedListing.NumberOfBathrooms }} Bath, {{ selectedListing.NumberOfSqFt }} sqft</p>
                <p v-if="selectedListing.timestamp" class="listing__timestamp">{{ utc(selectedListing.timestamp) | moment('from') }}</p>
            </div>
            <form @submit.prevent="handleSubmit(selectedListing.form)">
                <p class="listing__comment">{{ selectedListing.comments }}</p>
                <form-group :class="{ '-is-danger' : invalid.includes('content') }">
                    <form-wysiwyg id="email-content" class="email" rows="15" v-model="content" @input="clearInvalidWarnings" placeholder="Email Body" autofocus></form-wysiwyg>
                    <span v-if="invalid.includes('content')" class="form-error">You need to type an email body</span>
                </form-group>

                <div class="group__send w1/1 marB">
                    <button is="btn" type="submit" :modifiers="['primary']">Send</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import ListingResult from 'vue/components/shared/listings/ListingResult.vue';
    import getLeadLinkFromLeadData from 'vue/utils/getLeadLinkFromLeadData';
    import showSuccess from 'utils/showSuccess';
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
                loader: true,
                selectedListing: [],
                actionTitle: '',
                actionTimestamp: '',
                content: '',
                invalid: [],
                isHidden: true
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
            }
        },
        methods: {
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            getListings: async function () {
                const type = 'inquiries';

                await actions.getLeadInquiriesData(
                    this.context.lead_id,
                    type
                ).then(data => {
                    this.listings = data;
                    this.loader = false;
                }).catch(error => {
                    showErrors([error.message]);
                    throw error; // Re-throw
                });
            },
            handleSubmit: async function (subject) {

                // Require both a subject & message to be provided
                if (!this.content.length) {
                    if (!this.content.length) this.invalid.push('content');
                    return false;
                }

                // Require contextual data for request
                const lead_id = this.context.lead_id;
                const emailSubject = 'RE: ' + subject;
                const emailContent = this.content;
                const leadLink = getLeadLinkFromLeadData(this.lead);

                // Send lead email
                await actions.sendEmail(
                    lead_id,
                    emailSubject,
                    emailContent
                ).then(() => {

                    this.content = '';

                    // Show success message
                    showSuccess([`Email has been sent to ${leadLink}.`], undefined, {
                        close: true,
                        expires: 10000
                    });

                    // Failed to send
                }).catch(error => {
                    showErrors([error.message]);
                    throw error; // Re-throw
                });

            },
            openListing: function (listing) {
                this.selectedListing = listing;
                this.isHidden = false;
            },
            closeListing: function () {
                this.selectedListing = [];
                this.isHidden = true;
            },
            utc: function (time) {
                return dateUtils.toUtc(time);
            }
        }
    };
</script>

<style lang="scss" scoped>
    .lead__info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .lead__info .icon {
        width: 16px !important;
        height: 16px !important;
    }

    .lead__info .btn--action {
        align-self: flex-start;
    }

    .listings {
        overflow: hidden;
    }

    .listings__wrap {
        overflow: hidden;
    }

    .listing__wrap {
        position: relative;
        width: 100%;
        border: 8px solid #fff;
        width: 25%;
        min-height: 295px;
        float: left;
        background-size: cover;
        cursor: pointer;
    }

    .listing__wrap .listing {
        flex-basis: 100%;
        width: 100%;
    }

    .listing {
        position: relative;
        width: 100%;
        flex-grow: 1;
        border: 8px solid #fff;
        flex-basis: 25%;
        width: 25%;
        background-size: cover;
    }

    .listing__image {
        height: 150px;
        margin-bottom: 12px;
        background-size: cover;
    }

    .listing__price {
        margin: 0;
        position: absolute;
        bottom: 95px;
        left: 16px;
        font-size: 14px;
        color: #fff;
        text-shadow: 0 1px 1px #222;
    }

    .listing__timestamp {
        text-transform: capitalize;
    }

    .listing__address,
    .listing__info,
    .listing__timestamp {
        font-size: 12px;
        margin: 0;
        font-weight: bold;
        line-height: 1.5;
    }

    .listing__address,
    .listing__info {
        margin-bottom: 4px;
    }

    .listing__comment {
        margin: 0;
    }

    .listing__form {
        display: flex;
    }

    .listing__form .listing {
        flex-basis: 65%;
        width: 65%;
        margin-right: 16px;
    }

    .listing__form .listing__price {
        bottom: 170px;
    }

    .selected-address {
        margin-bottom: 12px;
    }

    .selected-address a {
        font-size: 14px;
        text-decoration: none;
    }

    @media (max-width: 800px) {
        .listing__wrap {
            width: 50%;
            min-height: 260px;
            margin-bottom: 30px;
        }

        .listing__comment {
            margin-bottom: 12px;
        }

        .listing__form {
            flex-direction: column;
        }

        .listing__form .listing {
            flex-basis: 100%;
            width: 100%;
            margin-right: 0;
            border: 0;
        }

        .listing__form .listing__price {
            bottom: 100px;
        }

        .listing__form .listing__timestamp {
            margin-bottom: 16px;
        }

        .hide-cta .listing__wrap {
            margin-bottom: 0;
        }

        .collapse__wrap.-is-open .listing {
            margin-bottom: 0;
        }
    }

    @media (max-width: 500px) {
        .listing__wrap {
            width: 100%;
        }

        .listing__image {
            height: 165px;
        }
    }

    .hide-cta /deep/ .more {
        display: none;
    }

    .hide-cta /deep/ .collapse {
        height: auto !important;
        overflow: visible !important;
    }

    /deep/ .collapse {
        margin: 0 -10px 12px -10px;
    }

    .hide {
        display: none;
    }
</style>