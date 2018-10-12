<template>
    <div class="w1/1">
        <recommend-form :submitHandler="recommendListing" :instructions="task.info"></recommend-form>
    </div>
</template>

<style lang="scss" scoped>
    .save__listing {
        justify-content: space-between;
        align-items: center;

        label {
            font-size: $font-size-base;
        }
    }
</style>

<style lang="scss">
    @media only screen and (max-width: 500px) {
        .modal_is-open {
            .ui-autocomplete.ui-menu {
                width: calc(100% - 80px) !important;
                left: 40px !important;
            }
        }
    }
</style>

<script>

    import getLeadLink from 'vue/utils/getLeadLink';
    import RecommendForm from 'vue/components/leads/actions/RecommendForm.vue';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';
    import actions from 'vue/actions';
    import store from 'store';

    export default {
        components: {
            'recommend-form': RecommendForm
        },
        data: function () {
            return {
                message: '',
                mls_number: '',
                send_message: true,
                invalid: []
            };
        },
        methods: {
            recommendListing: function ({ mls_number, message, send_message}) {

                // Get task context data
                const { id, lead_id } = this.context;
                const leadLink = getLeadLink.fromTaskData(this.task);

                // Recommend listing
                store.dispatch('modal/loader_show');
                actions.recommendListing(
                    lead_id,
                    mls_number,
                    message,
                    send_message
                ).then(() => {


                    // Dispatch action to close modal
                    store.dispatch('modal/loader_hide');
                    store.dispatch('modal/close');

                    // Show success message
                    showSuccess([`Listing has been recommended to ${leadLink}.`], undefined, {
                        close: true,
                        expires: 10000
                    });

                    // Mark action plan task as completed
                    actions.completeTask(id).then(() => {
                        store.commit('ACTION_PLAN_COMPLETE', { id });
                    }).catch(error => {
                        showErrors([error.message]);
                    });

                // Failed to complete
                }).catch(error => {
                    store.dispatch('modal/loader_hide');
                    showErrors([error.message]);
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
