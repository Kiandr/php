<template>
    <div class="field" :class="{'-has-label' : label}">
        <label v-if="label" class="field__label">{{ label }}</label>
        <date-picker :pickerOpts="{ mode }" :config="config" :placeholder="placeholder" @input="onInput"></date-picker>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                config: {
                    'static': true,
                    dateFormat: 'M-j-Y',
                    altInput: true,
                    defaultDate: this.defaultRange
                }
            };
        },
        props: {
            label: {
                type: String,
                default: ''
            },
            placeholder: {
                type: String,
                default: 'Choose Date...'
            },
            mode: {
                type: [String, Boolean],
                default: 'single',
                validator(value) {
                    return ['single', 'multiple', 'range', false].indexOf(value) !== -1;
                }
            },
            defaultRange: {
                type: Array,
                default: null
            }
        },
        methods: {
            onInput: function(value) {
                this.$emit('input', value);
            }
        },
        watch: {
            defaultRange: function () {
                this.config.defaultDate = this.defaultRange;
            }
        }
    };
</script>

<style scoped>
    /deep/ .flatpickr-calendar {
        top: 45px !important;
    }

    .-has-label /deep/ .flatpickr-calendar {
        top: 145px !important;
    }
</style>
