<template>
    <div class="w1/1">
        <text-form :submitHandler="sendText"></text-form>
    </div>
</template>

<script>

    import getLeadLinkFromLeadData from 'vue/utils/getLeadLinkFromLeadData';
    import LeadTextForm from 'vue/components/leads/actions/TextForm.vue';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';
    import store from 'store';

    export default {
        components: {
            'text-form': LeadTextForm
        },
        methods: {
            sendText: async function ({ content, phone_number }) {

                // Require contextual data for request
                const leadId = this.context.lead_id;
                const leadLink = getLeadLinkFromLeadData(this.lead);

                // Send text message
                store.dispatch('crm/leads/sendText', {
                    leadId,
                    content,
                    phone_number
                }).then(() => {

                    // Show success message
                    showSuccess([`Text has been sent to ${leadLink}.`], undefined, {
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
