<documentation>
    <!--
      RecommendForm is used to recommend listings.

      Usage:
        <recommend-form :submitHandler="sendRecommend"></recommend-form>

      The submitHandler is a callback that returns a promise.

      ** If submitHandler has error catching, it should re-throw to work properly

      Example of expected submitHandler:

      ```
      sendRecommend: function () {
          return doSomething().then(data => {
              showSuccess(['It works!']);
          }).catch(error => {
              showSuccess(['It broke!']);
              throw error;
          });
      }
      ```

    -->
</documentation>

<template>
    <div class="w1/1">
        <form @submit.prevent="handleSubmit">
            <div class="instruction-note" v-if="instructions && instructions.length" v-html="instructions"></div>
            <form-label v-if="has_feeds" class="select">
                <select name="feed" v-model="feed">
                    <option v-for="(feed, key) in feeds" :value="key">{{ feed.title }}</option>
                </select>
            </form-label>

            <form-group :class="{ '-is-danger' : invalid.includes('mls_number') }">
                <form-control ref="mls_number" placeholder="Enter MLS® Number or Street Address" v-model="mls_number" class="autocomplete" @mounted="initListingPicker" @input="clearInvalidWarnings" autofocus></form-control>
                <span v-if="invalid.includes('mls_number')" class="form-error">You need to select a listing</span>
            </form-group>

            <form-group v-if="send_message" :class="{ '-is-danger' : invalid.includes('message') }">
                <form-wysiwyg id="message-content" class="email" rows="15" v-model="message" placeholder="Include this Message..." @input="clearInvalidWarnings" autofocus></form-wysiwyg>
                <span v-if="invalid.includes('message')" class="form-error">You need to type an message</span>
                <p>Available Tags: {signature}, {first_name}, {last_name}, {email}, {verify}</p>
            </form-group>

            <div class="w1/1 -dF save__listing marB">
                <button is="btn" type="submit" :modifiers="['primary']">Send</button>
                <form-label class="boolean">
                    <input type="checkbox" v-model="send_message">
                    Send via Email
                </form-label>
            </div>
        </form>
    </div>
</template>

<style lang="scss" scoped>
    .save__listing {
        justify-content: space-between;
        align-items: center;

        label {
            font-size: $font-size-base;
        }
    }
</style>

<style lang="scss">
    @media only screen and (max-width: 500px) {
        .modal_is-open {
            .ui-autocomplete.ui-menu {
                width: calc(100% - 80px) !important;
                left: 40px !important;
            }
        }
    }
</style>

<script>
    import listingPicker from 'utils/listingPicker';
    import actions from 'vue/actions';

    export default {
        props: {
            instructions: {
                type: String,
                default: ''
            },
            submitHandler: {
                type: Function,
                required: true
            }
        },
        data: function () {
            return {
                message: '',
                mls_number: '',
                send_message: true,
                feeds: [],
                feed: '',
                has_feeds: false,
                invalid: []
            };
        },
        mounted: function () {
            const { subject, content } = this;
            this.subject = subject || '';
            this.content = content || '';
            this.getFeeds();
        },
        methods: {
            getFeeds: async function () {
                let feeds = await actions.getFeeds();
                this.feeds = feeds;
                let length = 0;
                for (var property in feeds) {
                    if (Object.prototype.hasOwnProperty.call(feeds, property)) {
                        if (length === 0) this.feed = property;
                        length++;
                    }
                }
                this.has_feeds = length > 1;
            },
            initListingPicker(el) {
                listingPicker(el, (mls_number) => {
                    this.mls_number = mls_number;
                });
            },
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            handleSubmit: async function () {

                // Require both type of call & call notes
                if (!this.mls_number.length || (!this.message.length && this.send_message)) {
                    if (!this.mls_number.length) this.invalid.push('mls_number');
                    if (!this.message.length && this.send_message) this.invalid.push('message');
                    return false;
                }

                // Data for handler
                const formData = {
                    mls_number: this.mls_number,
                    message: this.message,
                    send_message: this.send_message,
                    feed: this.feed
                };

                // Show loader & call submit handler
                Promise.resolve(
                    this.submitHandler(formData)
                );

            }
        }
    };
</script>
