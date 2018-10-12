<documentation>
    <!--
      TextForm is used to send outgoing texts.

      Usage:
        <text-form :submitHandler="sendText"></text-form>

      The submitHandler is a callback that returns a promise.

      ** If submitHandler has error catching, it should re-throw to work properly

      Example of expected submitHandler:

      ```
      sendText: function () {
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
    <div v-if="text_available" class="w1/1">
        <form @submit.prevent="handleSubmit">
            <div class="instruction-note" v-if="instructions && instructions.length" v-html="instructions"></div>
            <form-group :class="{ '-is-danger' : invalid.includes('phone_number') }">
                <form-control placeholder="Phone #" v-model="phone_number" @input="clearInvalidWarnings"></form-control>
                <span v-if="invalid.includes('phone_number')" class="form-error">You need to type a valid phone number</span>
            </form-group>

            <div>
                <span v-if="verification_error_flag" class="flag flag--negative">
                    {{ verification_error_flag }}
                </span>
                <span v-if="opt_in" class="flag flag--positive">Opted-In</span>
                <span v-if="!opt_in" class="flag flag--negative">Opted-Out</span>
            </div>

            <form-group :class="{ '-is-danger' : invalid.includes('content') }">
                <form-textarea rows="20" autofocus v-model="content" placeholder="Message Body" @input="clearInvalidWarnings"></form-textarea>
                <span v-if="invalid.includes('content')" class="form-error">You need to type a message body</span>
            </form-group>

            <div class="w1/1 marB">
                <button is="btn" type="submit" :modifiers="['primary']">Send</button>
            </div>

            <div class="overlay -is-full" v-if="error_details">
                <avatar-icons></avatar-icons>
                <svg>
                    <use xlink:href="#security-stop" href="#security-stop"></use>
                </svg>
                <h4>{{ error_details }}</h4>
            </div>
        </form>
    </div>
    <div v-else class="w1/1">
        <form>
            <div class="buyin__content">
                <div class="buyin__image">
                    <img src="/backend/assets/images/illustrations/whoa.png" alt="REW Texting Add-on" />
                </div>
                <div class="buyin__message text text--strong">
                    <p>Oops, You need the Texting Add-on</p><p>to Text Leads from the Backend</p>
                </div>
                <p class="buyin__hint text">Send and receive text messages within REW CRM to improve engagement rates and stay connected.</p>
                <p class="cta text--strong">Contact Your Administrator or Product Consultant</p>
            </div>
        </form>
    </div>
</template>

<style scoped>
    .buyin__content {
        max-width: 70%;
        margin: 0 auto;
        text-align: center;
    }

    .buyin__image {
        max-width: 20%;
        margin: 0 auto;
    }

    .buyin__message {
        line-height: 1.2;
        margin-bottom: 24px;
    }

    .buyin__message p {
        margin: 0;
        font-size: 28px;
    }

    .buyin__hint {
        max-width: 75%;
        margin: 0 auto 24px;
    }

    @media (max-width: 769px) {
        .buyin__message p {
            font-size: 22px;
        }
    }

    @media (max-width: 480px) {
        .buyin__image {
            max-width: 50%;
        }

        .buyin__message,
        .buyin__hint {
            margin-bottom: 16px;
        }

        .buyin__message p {
            font-size: 18px;
        }
    }
</style>

<script>
    import store from 'store';
    import actions from 'vue/actions';

    export default {
        props: {
            defaultContent: {
                type: String,
                default: ''
            },
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
                phone_number: '',
                content: '',
                error_details: '',
                invalid: [],
                loader: false,
                opt_in: false,
                verification_error_flag: '',
            };
        },
        mounted: function () {
            this.verifyTexting();
            if (!this.content || this.content.length < 0) {
                this.content = this.defaultContent || '';
            }
        },
        methods: {
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            handleSubmit: async function () {

                if (!this.phone_number.length) {
                    this.invalid.push('phone_number');
                    return false;
                }

                // Require text message
                if (!this.content.length) {
                    // @TODO: trim & require min length for message
                    if (!this.content.length) this.invalid.push('content');
                    return false;
                }

                // Data for handler
                const formData = {
                    content: this.content,
                    phone_number: this.phone_number
                };

                // Show loader & call submit handler
                store.dispatch('modal/loader_show');
                Promise.resolve(
                    this.submitHandler(formData)
                ).then(() => {


                    // Dispatch action to close modal
                    store.dispatch('modal/loader_hide');
                    store.dispatch('modal/close');

                    // Failed to send
                }).catch(() => {
                    store.dispatch('modal/loader_hide');
                });
            },
            verifyTexting: async function () {
                actions.verifyTexting(this.context.lead_id).then(data => {
                    this.verification = data.lead.verified;
                    if (data.lead.opted_in) {
                        this.opt_in = true;
                    }
                    this.phone_number = data.lead.phone || data.lead.phone_cell || '';
                    if (data.lead.verification_error) {
                        this.verification_error_flag = data.lead.verification_error;
                    }
                }).catch(error => {
                    // error_details will trigger the error message overlay that blocks the form from the UX view
                    if (error.status === 401) {
                        this.error_details = 'You do not have permission to text this lead';
                    }
                });
            }
        },
        computed: {
            context: function () {
                return store.getters['modal/getContext'];
            },
            text_available: function () {
                const user = store.getters['auth/getUser'];
                return user.text_available;
            }
        }
    };
</script>
