<template>
    <div class="w1/1">
        <form @submit.prevent="assignGroup">
            <div class="instruction-note" v-if="task && task.info && task.info.length" v-html="task.info"></div>
            <div class="marB">
                <form-group :class="{ '-is-danger' : invalid.includes('groups') }">
                    <form-selectize multiple v-model="selected" :settings="groupRenderSettings" placeholder="Select groups..." :options="options" @input="clearInvalidWarnings"></form-selectize>
                    <span v-if="invalid.includes('groups')" class="form-error">You need to select at least one group</span>
                </form-group>
            </div>
            <div class="pad8 add__group">
                <button is="btn" type="submit" :modifiers="['primary']">
                    Add To {{ this.selected.length > 1 ? 'Groups' : 'Group' }}
                </button>
            </div>
        </form>
    </div>
</template>

<style lang="scss" scoped>
    .add__group {
        align-self: center;
    }
</style>

<script>

    import getLeadLink from 'vue/utils/getLeadLink';
    import groupRenderSettings from 'common/groupPicker/renderSettings';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';
    import actions from 'vue/actions';
    import store from 'store';

    export default {
        data: function () {
            return {
                invalid: [],
                options: [],
                selected: [],
                groupRenderSettings
            };
        },
        mounted: function () {
            this.getGroups();
        },
        methods: {
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            getGroups: async function () {
                // Get IDs of groups to be assigned
                const { task_data } = this.context;
                const group_ids = task_data.groups.map(group => group.id);
                actions.getGroups().then(data => {
                    this.options = data.groups.map(function(group) {
                        return {value: parseInt(group.id), text: group.name, style: group.style};
                    });
                    this.selected = group_ids;
                // Failed to load groups
                }).catch(error => {
                    store.dispatch('modal/loader_hide');
                    showErrors([error.message]);
                });
            },
            assignGroup: async function () {

                // Require both type of call & call notes
                if (!this.selected.length) {
                    if (!this.selected.length) this.invalid.push('groups');
                    return false;
                }

                // Get IDs of groups to be assigned
                const { context, selected } = this;
                const { id, lead_id } = context;
                const leadLink = getLeadLink.fromTaskData(this.task);

                // Assign to group
                store.dispatch('modal/loader_show');
                actions.assignGroup(
                    lead_id,
                    selected
                ).then(() => {


                    // Dispatch action to close modal
                    store.dispatch('modal/loader_hide');
                    store.dispatch('modal/close');

                    // Show success message
                    showSuccess([`${leadLink} has been assigned to the selected groups.`], undefined, {
                        close: true,
                        expires: 10000
                    });

                    // Mark action plan task as completed
                    actions.completeTask(id).then(() => {
                        store.commit('ACTION_PLAN_COMPLETE', { id });
                    }).catch(error => {
                        showErrors([error.message]);
                    });

                // Failed to assign
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
