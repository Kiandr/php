<template>
    <Card class="lead__results">
        <CardSection>
            <div class="article">
                <div class="article__body">
                    <div class="info">
                        <div class="article__thumb">
                            <Thumb :context=context :score=score />
                        </div>
                        <div class="article__content">
                            <a class="article__title text text--strong" href="#">{{title}}</a>
                            <div class="text text--mute">Joined 3 Days Ago</div>
                        </div>
                    </div>
                    <div class="actions">
                        <Token :label="`${totalCalls} Calls`"
                               :modifiers="['xs']"
                               icon="icon--phone" />

                        <Token :label="`${totalEmails} Emails`"
                               :modifiers="['xs']"
                               icon="icon--email" />

                        <Token :label="`${totalTexts} Texts`"
                               :modifiers="['xs']"
                               icon="icon--message" />

                        <Token :label="`${totalInquiries} Inquiries`"
                               :modifiers="['xs']"
                               icon="icon--inquiries" />
                    </div>
                </div>
                <div class="article__foot">
                    <div v-if="lead_details" class="keyvals" :class="{'-is-open': open}" :style="{height: height}">
                        <div class="leadDetails-wrap" ref="leadDetails">
                            <div v-for="(value, key) in lead_details" class="keyvals__row">
                                <span class="keyvals__key">{{ key }}</span>
                                <span class="keyvals__val">
                                    <a href="#" v-html="value">
                                        {{ value }}
                                    </a>
                                    <span v-if="key == 'phone'">
                                        <Icon name="icon--message" class="primary" :width=16 :height=16 />
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="more">
                        <a tabindex="0" @click="toggle">
                            <span class="caret" :class="{'-is-open': open}"></span>
                            <span>Show More</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="actions">
                <Button :modifiers="['primary']">
                    <Icon name="icon--thumbs-up" :width=16 :height=16 :modifiers="['invert']" />
                    <span class="text">Accept</span>
               </Button>
               <Button class="details" :modifiers="['ghost']">
                   <Icon name="icon--eye" :width=16 :height=16 class="primary" />
                   <span class="text primary">Details</span>
              </Button>
            </div>
        </CardSection>
    </Card>
</template>

<style scoped>

    .article {
        margin-bottom: 12px;
    }

    .article__body {
        align-items: flex-start;
        justify-content: space-between;
    }

    .article__title {
        font-weight: bold;
        color: inherit;
        text-decoration: none;
    }

    .article__foot {
        display: flex;
        justify-content: space-between;
        padding-top: 24px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eeeeee;
    }

    .info {
        display: flex;
    }

    .keyvals {
        width: auto;
        display: block;
        /* height: 80px; */
        overflow: hidden;
        transition: height 0.25s;
    }

    /* .keyvals.-is-open {
        height: 320px;
    } */

    .keyvals__key {
        /* width: auto;
        display: inline; */
        padding-left: 0;
        margin-right: 16px;
        text-transform: capitalize;
    }

    .keyvals__val a {
        color: #2692f7;
        margin-right: 4px;
        text-decoration: none;
    }

    .phone .icon {
        fill: #2692f7;
        margin-bottom: 3px;
    }

    .more {
        cursor: pointer;
        align-self: flex-end;
    }

    .more .icon {
        fill: #2692f7;
    }

    .caret {
        width: 0;
        height: 0;
        margin-bottom: 2px;
        display: inline-block;
        vertical-align: middle;
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 4px solid #2692f7;
    }

    .caret.-is-open  {
        transform: rotate(180deg);
    }

    .details {
        color: #2692f7;
    }

    .primary,
    .icon.primary {
        fill: #2692f7;
        color: #2692f7;
    }

    .actions .btn:not(:last-of-type) {
        margin-right: 12px;
    }
</style>

<script>
    export default {
        mounted: function() {
            const keyval = this.$refs.leadDetails;
            const height = this.$refs.leadDetails.clientHeight;
            this.height = (height / 4) + 'px';
        },
        props: {
            title: String,
            context: Array,
            score: [String, Number],
            totalCalls: Number,
            totalEmails: Number,
            totalTexts: Number,
            totalInquiries: Number
        },
        data: function () {
            return {
                open: false,
                height: '',
                lead_details: {
                    email: 'jpeters@example.com',
                    phone: '888-555-1234',
                    'last touch': 'Emailed 4 Days Ago',
                    joined: 'Jan 2, 2018',
                    value: '$475k/$9.5k',
                    origin: 'Google.com',
                    'action plan': 'Not yet Assigned! Click here to assign'
                }
            };
        },
        methods: {
            toggle: function (evt) {
                evt.preventDefault();
                this.open = !this.open;
                const height = !this.open ? this.$refs.leadDetails.clientHeight / 4 : this.$refs.leadDetails.clientHeight;
                this.height = height + 'px';
            }
        }
    }
</script>
