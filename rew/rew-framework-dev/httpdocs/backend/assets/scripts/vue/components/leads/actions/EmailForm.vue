<documentation>
    <!--
      EmailForm is used to send outgoing emails.

      Usage:
        <email-form :submitHandler="sendEmail"></email-form>

      The submitHandler is a callback that returns a promise.

      ** If submitHandler has error catching, it should re-throw to work properly

      Example of expected submitHandler:

      ```
      sendEmail: function () {
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
            <form-group :class="{ '-is-danger' : invalid.includes('subject') }">
                <form-control placeholder="Subject" v-model="subject" @input="clearInvalidWarnings"></form-control>
                <span v-if="invalid.includes('subject')" class="form-error">You need to type a subject</span>
            </form-group>

            <form-group :class="{ '-is-danger' : invalid.includes('content') }">
                <form-wysiwyg id="email-content" class="email" rows="15" v-model="content" @input="clearInvalidWarnings" placeholder="Email Body" autofocus></form-wysiwyg>
                <span v-if="invalid.includes('content')" class="form-error">You need to type an email body</span>
            </form-group>

            <div class="group__send w1/1 marB">
                <button is="btn" type="submit" :modifiers="['primary']">Send</button>
            </div>
        </form>
    </div>
</template>

<script>
    import store from 'store';

    export default {
        props: {
            instructions: {
                type: String,
                default: ''
            },
            submitHandler: {
                type: Function,
                required: true
            },
            defaultContent: {
                type: String,
                default: ''
            },
            defaultSubject: {
                type: String,
                default: ''
            }
        },
        data: function () {
            return {
                invalid: [],
                subject: '',
                content: ''
            };
        },
        mounted: function () {
            const { defaultSubject, defaultContent } = this;
            this.subject = defaultSubject || '';
            this.content = defaultContent || '';
        },
        methods: {
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            handleSubmit: async function () {

                // Require both a subject & message to be provided
                if (!this.subject.length || !this.content.length) {
                    // @TODO: improve validation to trim first & require min length
                    if (!this.subject.length) this.invalid.push('subject');
                    if (!this.content.length) this.invalid.push('content');
                    return false;
                }

                // Data for handler
                const formData = {
                    subject: this.subject,
                    content: this.content
                };

                // Show loader & call submit handler
                store.dispatch('modal/loader_show');
                Promise.resolve(
                    this.submitHandler(formData)
                ).then(() => {

                    // Dispatch action to close modal
                    store.dispatch('modal/loader_hide');
                    store.dispatch('modal/close');

                // Handler failed
                }).catch(() => {
                    store.dispatch('modal/loader_hide');

                });

            }
        }
    };
</script>
