<template>
    <select class="form-select w1/1" v-model="updatedValue" @change="onChange($event.target.value)">
        <option v-if="placeholder" value="">{{ placeholder }}</option>
        <option v-for="option in options" :value="option.value">{{ option.text }}</option>
    </select>
</template>

<script>
    export default {
        props: {
            placeholder: {
                type: [String, Boolean],
                default: '...'
            },
            options: {
                type: Array,
                default: () => []
            },
            value: {
                type: [String, Array],
                default: () => ''
            }
        },
        methods: {
            onChange(value) {
                this.$emit('input', value);
            }
        },
        computed: {
            updatedValue: {
                get() {
                    return this.value;
                },
                set(newValue) {
                    return newValue;
                }
            }
        }
    };
</script>
