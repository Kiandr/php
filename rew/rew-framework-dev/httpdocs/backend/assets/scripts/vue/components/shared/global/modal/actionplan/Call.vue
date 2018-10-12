<template>
    <div class="w1/1">
        <call-form :submitHandler="trackCall" :instructions="task.info"></call-form>
    </div>
</template>

<script>

    import getLeadLink from 'vue/utils/getLeadLink';
    import LeadCallForm from 'vue/components/leads/actions/CallForm.vue';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';
    import actions from 'vue/actions';
    import store from 'store';

    export default {
        components: {
            'call-form': LeadCallForm
        },
        methods: {
            trackCall: async function ({ type, content }) {

                // Require contextual data for request
                const { id, lead_id } = this.context;
                const leadLink = getLeadLink.fromTaskData(this.task);

                // Track phone call
                await actions.trackCall(
                    lead_id,
                    type,
                    content
                ).then(() => {

                    // Show success message
                    showSuccess([`Call to ${leadLink} has been tracked.`], undefined, {
                        close: true,
                        expires: 10000
                    });

                    // Mark action plan task as completed
                    actions.completeTask(id).then(() => {
                        store.commit('ACTION_PLAN_COMPLETE', { id });
                    }).catch(error => {
                        showErrors([error.message]);
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
            task: function () {
                const { getTaskById } = store.getters;
                const taskId = this.context.id;
                return getTaskById(taskId);
            }
        }
    };
</script>
