<template>
  <input type="text" ref="control" class="form-control w1/1" :value="value" @input="emitValue">
</template>

<script>
    export default {
        props: {
            value: String
        },
        mounted() {
            this.$emit('mounted', this.$el);
        },
        methods: {
            emitValue: function () {
                this.$emit('input', this.$refs.control.value);
            }
        }
    };
</script>
