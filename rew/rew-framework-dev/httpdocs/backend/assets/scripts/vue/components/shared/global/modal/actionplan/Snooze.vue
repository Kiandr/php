<template>
    <div class="w1/1">
        <form @submit.prevent="snoozeTask">
            <form-group :class="{ '-is-danger' : invalid.includes('reason') }">
                <form-label>Reason/Note</form-label>
                <form-textarea v-model="reason" @input="clearInvalidWarnings" rows="3" autofocus></form-textarea>
                <span v-if="invalid.includes('reason')" class="form-error">You need to add a reason/note</span>
            </form-group>
            <div class="marB">
                <form-group :class="{ '-is-danger': invalid.includes('duration')}">
                    <form-label>Duration<em class="required">*</em></form-label>
                    <div class="input-wrap">
                        <input v-model="duration" @input="clearInvalidWarnings" name="duration" type="number" value="1" class="w1/3" />
                        <form-select v-model="unit" :placeholder="false" :options="units" @input="clearInvalidWarnings" class="w1/1"></form-select>
                    </div>
                    <span v-if="invalid.includes('duration')" class="form-error">You need to include a duration</span>
                </form-group>
            </div>
            <div class="w1/1">
                <button is="btn" :modifiers="['primary']" class="-marR8" type="submit">Snooze</button>
                <button is="btn" @click.native="close">Cancel</button>
            </div>
        </form>
    </div>
</template>

<style scoped>
    .input-wrap {
        display: flex;
    }

    .input-wrap input {
        display: flex;
        flex-basis: 30%;
        max-width: 30%;
        border-right: 0;
    }

</style>

<script>

    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';
    import actions from 'vue/actions';
    import store from 'store';

    export default {
        data() {
            return {
                reason: '',
                duration: 0,
                invalid: [],
                unit: 'minutes',
                units: [
                    { value: 'minutes', text: 'Minutes' },
                    { value: 'hours', text: 'Hours' },
                    { value: 'days', text: 'Days' },
                    { value: 'weeks', text: 'Weeks' }
                ]
            };
        },
        methods: {
            close() {
                store.dispatch('modal/close');
            },
            clearInvalidWarnings() {
                this.invalid = [];
            },
            snoozeTask() {
                if (!this.reason.length || !this.duration || !this.unit) {
                    if (!this.reason.length) this.invalid.push('reason');
                    if (!this.duration) this.invalid.push('duration');
                    if (!this.unit) this.invalid.push('unit');
                    return false;
                }

                // Require contextual data for request
                const { id } = this.context;

                // Snooze task for set duration
                store.dispatch('modal/loader_show');
                actions.snoozeTask(
                    id,
                    this.reason,
                    this.duration,
                    this.unit
                ).then(data => {

                    // Load latest action plans task data
                    store.dispatch('loadActionPlanTasks');


                    // Dispatch action to close modal
                    store.dispatch('modal/loader_hide');
                    store.dispatch('modal/close');

                    // Show success message
                    const { success } = data;
                    if (success && success.message) {
                        showSuccess([success.message]);
                    }

                }).catch(error => {
                    store.dispatch('modal/loader_hide');
                    showErrors([error.message]);
                });
            }
        },
        computed: {
            context: function () {
                return store.getters['modal/getContext'];
            }
        }
    };
</script>
