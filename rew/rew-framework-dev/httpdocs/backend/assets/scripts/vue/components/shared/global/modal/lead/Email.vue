<template>
    <div class="w1/1">
        <email-form :submitHandler="sendEmail"></email-form>
    </div>
</template>

<script>

    import getLeadLinkFromLeadData from 'vue/utils/getLeadLinkFromLeadData';
    import LeadEmailForm from 'vue/components/leads/actions/EmailForm.vue';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';
    import store from 'store';

    export default {
        components: {
            'email-form': LeadEmailForm
        },
        methods: {
            sendEmail: async function ({ subject, content }) {

                // Require contextual data for request
                const leadId = this.context.lead_id;
                const leadLink = getLeadLinkFromLeadData(this.lead);

                // Send lead email
                store.dispatch('crm/leads/sendEmail', {
                    leadId,
                    subject,
                    content
                }).then(() => {

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
