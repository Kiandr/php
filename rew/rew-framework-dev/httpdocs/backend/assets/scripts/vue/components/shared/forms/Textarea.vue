<template>
    <textarea class="form-textarea w1/1" ref="textarea" :value="value" @input="emitValue"></textarea>
</template>

<script>
    export default {
        props: {
            value: String
        },

        methods: {
            emitValue: function () {
                this.$emit('input', this.$refs.textarea.value);
            }
        }
    };
</script>