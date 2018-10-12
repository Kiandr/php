<template>
    <form-date mode="range" :defaultRange="date" :label="label" :placeholder="placeholder" @input="onInput"></form-date>
</template>

<script>

    import DateField from './DateRangeField';

    export default {
        extends: DateField,
        props: {
            label: {
                type: String,
                default: ''
            },
            placeholder: {
                type: String,
                default: 'Choose Date Range...'
            },
            start_set: {
                type: [String, Date],
                default: () => new Date()
            },
            end_set: {
                type: [String, Date],
                default: () => new Date()
            }
        },
        methods: {
            onInput: function(value) {
                this.$emit('update:start_set', value[0]);
                this.$emit('update:end_set', value[1]);
            }
        },
        computed: {
            date: function () {
                return [
                    this.start_set,
                    this.end_set
                ];
            }
        }
    };

</script>
