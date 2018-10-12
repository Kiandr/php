<template>
    <div class="w1/1">
        <form @submit.prevent="addNote">
            <div class="instruction-note" v-if="task && task.info && task.info.length" v-html="task.info"></div>

            <form-group :class="{ '-is-danger' : invalid.includes('content') }">
                <form-textarea rows="10" autofocus v-model="content" placeholder="Add a note for this task" @input="clearInvalidWarnings"></form-textarea>
                <span v-if="invalid.includes('content')" class="form-error">You need to add a note</span>
            </form-group>

            <div class="w1/1">
                <button is="btn" type="submit" :modifiers="['primary']">Save</button>
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
        data: function () {
            return {
                content: '',
                invalid: []
            };
        },
        methods: {
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            addNote: async function () {

                // Require task notes
                if (!this.content.length) {
                    // @TODO: trim & require min length
                    this.invalid.push('content');
                    return false;
                }

                // Require contextual data for request
                const { id, lead_id } = this.context;
                const leadLink = getLeadLink.fromTaskData(this.task);

                // Create lead note
                store.dispatch('modal/loader_show');
                actions.createNote(
                    lead_id,
                    this.content
                ).then(() => {

                    // Dispatch action to close modal
                    store.dispatch('modal/loader_hide');
                    store.dispatch('modal/close');

                    // Show success message
                    showSuccess([`Custom task for ${leadLink} has been completed.`], undefined, {
                        close: true,
                        expires: 10000
                    });

                    // Mark action plan task as completed
                    actions.completeTask(id).then(() => {
                        store.commit('ACTION_PLAN_COMPLETE', { id });
                    }).catch(error => {
                        showErrors([error.message]);
                    });

                // Failed to save
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
