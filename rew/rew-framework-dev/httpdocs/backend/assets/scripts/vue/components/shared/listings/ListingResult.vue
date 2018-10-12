<template>
    <div class="listing">
        <div class="listing__image__wrap">
            <div class="listing__image" :style="{'background-image': `url(${listing.ListingImage || listingPlaceholder})`}"></div>
            <h3 class="listing__price">${{ formatPrice(listing.ListingPrice) }}</h3>
        </div>
        <h3 class="listing__address">{{ listing.Address }}, {{ listing.AddressCity }}, {{ listing.AddressState }}</h3>
        <p class="listing__info">{{ listing.NumberOfBedrooms }} Bedrooms, {{ listing.NumberOfBathrooms }} Bath, {{ listing.NumberOfSqFt }} sqft</p>
        <p v-if="listing.timestamp" class="listing__timestamp">{{ utc(listing.timestamp) | moment('from') }}</p>
    </div>
</template>

<script>
    import dateUtils from 'utils/date';
    export default {
        props: {
            listing: {
                type: Object,
                default: () => {}
            },
            listingPlaceholder: {
                type: String,
                default: () => '/img/no-photo.png'
            }
        },
        methods: {
            formatPrice(value) {
                let val = (value/1).toFixed(0);
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            },
            utc: function (time) {
                return dateUtils.toUtc(time);
            }
        }
    };
</script>

<style scoped>
    .listing {
        position: relative;
        width: 100%;
        border: 8px solid #fff;
        width: 25%;
        min-height: 295px;
        float: left;
        background-size: cover;
    }

    .listing__image__wrap {
        position: relative;
    }

    .listing__image {
        height: 150px;
        margin-bottom: 12px;
        background-size: cover;
    }

    .listing__price {
        margin: 0;
        position: absolute;
        bottom: 8px;
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

    @media (max-width: 800px) {
        .listing {
            width: 50%;
            min-height: 260px;
            margin-bottom: 30px;
        }

        .collapse__wrap.-is-open .listing {
            margin-bottom: 0;
        }
    }

    @media (max-width: 500px) {
        .listing {
            width: 100%;
        }

        .listing__image {
            height: 165px;
        }
    }
</style>
