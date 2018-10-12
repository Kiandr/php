<documentation>
    <!--
      CallForm is used to log phone calls to leads.

      Usage:
        <call-form :submitHandler="trackCall"></call-form>

      The submitHandler is a callback that returns a promise.

      ** If submitHandler has error catching, it should re-throw to work properly

      Example of expected submitHandler:

      ```
      trackCall: function () {
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
            <form-group :class="{ '-is-danger' : invalid.includes('type') }">
                <form-select v-model="type" placeholder="Outcome..." :options="types" @input="clearInvalidWarnings"></form-select>
                <span v-if="invalid.includes('type')" class="form-error">You need to choose a call outcome</span>
            </form-group>

            <form-group :class="{ '-is-danger' : invalid.includes('content') }">
                <form-textarea rows="20" autofocus v-model="content" placeholder="Call Summary" @input="clearInvalidWarnings"></form-textarea>
                <span v-if="invalid.includes('content')" class="form-error">You need to type a call summary</span>
            </form-group>

            <div class="w1/1 marB">
                <button is="btn" type="submit" :modifiers="['primary']">Save</button>
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
            }
        },
        data: function () {
            return {
                content: '',
                invalid: [],
                type: '',
                types: [
                    { value: 'call', text: 'Talked to Lead' },
                    { value: 'attempt', text: 'Attempted' },
                    { value: 'voicemail', text: 'Voicemail' },
                    { value: 'invalid', text: 'Wrong Number' }
                ]
            };
        },
        mounted: function () {
            const { type, content } = this;
            this.type = type || '';
            this.content = content || '';
        },
        methods: {
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            handleSubmit: async function () {

                // Require both type of call & call notes
                if (!this.content.length || !this.type.length) {
                    // @TODO: improve validation to trim first & require min length
                    if (!this.content.length) this.invalid.push('content');
                    if (!this.type.length) this.invalid.push('type');
                    return false;
                }

                // Data for handler
                const formData = {
                    type: this.type,
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
