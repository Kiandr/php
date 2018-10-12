<template>
    <div class="w1/1">
        <form @submit.prevent="dismissTask">
            <form-group :class="{ '-is-danger' : invalid.includes('reason') }">
                <form-label>Reason/Note</form-label>
                <form-textarea v-model="reason" @input="clearInvalidWarnings" rows="5" autofocus></form-textarea>
                <span v-if="invalid.includes('reason')" class="form-error">You need to add a reason/note</span>
            </form-group>
            <form-label class="boolean marB">
                <input type="checkbox" name="dismiss_followup_tasks" v-model="dismiss_followup_tasks">
                Dismiss Follow-Up tasks
            </form-label>
            <div class="w1/1">
                <button is="btn" :modifiers="['primary']" class="-marR8" type="submit">Dismiss</button>
                <button is="btn" @click.native="close">Cancel</button>
            </div>
        </form>
    </div>
</template>

<script>

    import getLeadLink from 'vue/utils/getLeadLink';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';
    import actions from 'vue/actions';
    import store from 'store';

    export default {
        data() {
            return {
                reason: '',
                invalid: [],
                dismiss_followup_tasks: false
            };
        },

        methods: {
            close: function () {
                store.dispatch('modal/close');
            },
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            dismissTask: function() {
                if (!this.reason) {
                    this.invalid.push('reason');
                    return false;
                }

                // Require contextual data for request
                const { id } = this.context;
                const leadLink = getLeadLink.fromTaskData(this.task);

                store.dispatch('modal/loader_show');
                actions.dismissTask(
                    id,
                    this.reason,
                    this.dismiss_followup_tasks
                ).then(() => {

                    // Action successful
                    store.commit('ACTION_PLAN_DISMISS', { id });

                    // Dispatch action to close modal
                    store.dispatch('modal/loader_hide');
                    store.dispatch('modal/close');

                    // Show success message
                    showSuccess([`Task for ${leadLink} has been dismissed.`], undefined, {
                        close: true,
                        expires: 10000
                    });

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
