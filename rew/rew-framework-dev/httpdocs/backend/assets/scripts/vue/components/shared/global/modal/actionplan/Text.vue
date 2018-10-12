<template>
    <div class="w1/1">
        <text-form :submitHandler="sendText" :instructions="task.info" :defaultContent="context.task_data.message"></text-form>
    </div>
</template>

<script>

    import getLeadLink from 'vue/utils/getLeadLink';
    import LeadTextForm from 'vue/components/leads/actions/TextForm.vue';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';
    import actions from 'vue/actions';
    import store from 'store';

    export default {
        components: {
            'text-form': LeadTextForm
        },
        methods: {
            sendText: async function ({ content, phone_number }) {

                // Require contextual data for request
                const { id, lead_id } = this.context;
                const leadLink = getLeadLink.fromTaskData(this.task);

                // Send text message
                await actions.sendText(
                    lead_id,
                    content,
                    phone_number
                ).then(() => {

                    // Show success message
                    showSuccess([`Text has been sent to ${leadLink}.`], undefined, {
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

            },
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
