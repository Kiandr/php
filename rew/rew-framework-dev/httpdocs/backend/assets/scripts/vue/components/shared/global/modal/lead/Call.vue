<template>
    <div class="w1/1">
        <call-form :submitHandler="trackCall"></call-form>
    </div>
</template>

<script>

    import getLeadLinkFromLeadData from 'vue/utils/getLeadLinkFromLeadData';
    import LeadCallForm from 'vue/components/leads/actions/CallForm.vue';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';
    import store from 'store';

    export default {
        components: {
            'call-form': LeadCallForm
        },
        methods: {
            trackCall: async function ({ type, content }) {

                // Require contextual data for request
                const leadId = this.context.lead_id;
                const leadLink = getLeadLinkFromLeadData(this.lead);

                // Track phone call
                store.dispatch('crm/leads/trackCall', {
                    leadId,
                    type,
                    content
                }).then(() => {


                    // Show success message
                    showSuccess([`Call to ${leadLink} has been tracked.`], undefined, {
                        close: true,
                        expires: 10000
                    });

                    // Failed to track
                }).catch(error => {
                    showErrors([error.message]);
                    throw error; // Re-throw
                });

            }
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
        }
    };
</script>
