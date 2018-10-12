<template>
    <div class="w1/1">
        <email-form :submitHandler="sendEmail" :default-content="context.task_data.message" :default-subject="context.task_data.subject" :instructions="task.info"></email-form>
    </div>
</template>

<script>

    import getLeadLink from 'vue/utils/getLeadLink';
    import LeadEmailForm from 'vue/components/leads/actions/EmailForm.vue';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';
    import actions from 'vue/actions';
    import store from 'store';

    export default {
        components: {
            'email-form': LeadEmailForm
        },
        methods: {
            sendEmail: async function ({ subject, content }) {

                // Require contextual data for request
                const { id, lead_id } = this.context;
                const leadLink = getLeadLink.fromTaskData(this.task);

                // Send lead email
                await actions.sendEmail(
                    lead_id,
                    subject,
                    content
                ).then(() => {

                    // Show success message
                    showSuccess([`Email has been sent to ${leadLink}.`], undefined, {
                        close: true,
                        expires: 10000
                    });

                    // Mark action plan task as completed
                    actions.completeTask(id).then(() => {
                        store.commit('ACTION_PLAN_COMPLETE', { id });
                    }).catch(error => {
                        showErrors([error.message]);
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
            task: function () {
                const { getTaskById } = store.getters;
                const taskId = this.context.id;
                return getTaskById(taskId);
            }
        }
    };
</script>
