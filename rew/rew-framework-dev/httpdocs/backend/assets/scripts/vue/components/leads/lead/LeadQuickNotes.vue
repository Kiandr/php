<template>
    <div class="quicknote">
        <a href="#" v-if="!editing" @click="activateInput()" :title="`${inputValue ? 'Click to Edit' : 'Click to Add'}`" :class="{'empty-note' : !inputValue}">
            {{ inputValue.length ? inputValue : placeholder }}
        </a>
        <input
            ref="qnInput"
            type="text"
            v-if="editing"
            :value="inputValue"
            :placeholder="placeholder"
            @blur="submitInput()"
            @keyup.enter="submitInput()"
            @focus="moveCursorToEnd()"
            class="w1/1"
        >
    </div>
</template>

<style scoped>
    .quicknote a {
        display: block;
        color: #525252;
        opacity: 0.85;
        text-decoration: none;
    }

    input::placeholder,
    .empty-note {
        font-style: italic;
    }

    input::-ms-clear {
        display: none;
    }
</style>

<script>

    import store from 'store';
    import getLeadLinkFromLeadData from 'vue/utils/getLeadLinkFromLeadData';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';

    export default {
        props: {
            lead: {
                type: Object,
                required: true
            }
        },
        data: function() {
            return {
                editing: false,
                lead_id: null,
                inputValue: '',
                placeholder: 'Add a Quick Note'
            };
        },
        methods: {
            activateInput: function () {
                this.editing = true;
                this.$nextTick(() => {
                    this.$refs.qnInput.focus();
                });
            },
            deactivateInput: function () {
                this.editing = false;
            },
            submitInput: function () {
                this.updateNote();
                this.deactivateInput();
            },
            updateNote: async function () {
                const currentValue = this.$refs.qnInput.value;
                const isDifferent = currentValue === this.inputValue;
                this.inputValue = currentValue;
                if (isDifferent) return;
                store.dispatch('crm/leads/updateLeadNotes', {
                    lead_id: this.lead.id,
                    notes: this.inputValue
                }).then(() => {
                    const leadLink = getLeadLinkFromLeadData(this.lead);
                    showSuccess([`Quick notes for ${leadLink} have been saved.`], undefined, {
                        close: true,
                        expires: 10000
                    });
                }).catch((error) => {
                    showErrors([error.message]);
                });
            },
            moveCursorToEnd: function() {
                if (typeof this.$refs.qnInput.selectionStart === 'number') {
                    this.$refs.qnInput.selectionStart = this.$refs.qnInput.selectionEnd = this.$refs.qnInput.value.length;
                }
            }
        },
        mounted: function() {
            this.inputValue = this.lead.notes || '';
        }
    };
</script>
