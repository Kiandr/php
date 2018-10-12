<template>
    <select ref="select" multiple class="form-select w1/1" @change="onChange($event.target.value)">
        <option value="">{{ placeholder }}</option>
        <option v-for="option in options" :value="option.value">{{ option.text }}</option>
    </select>
</template>

<script>
    import $ from 'jquery';
    import 'selectize';

    export default {
        props: {
            placeholder: {
                type: String,
                default: '...'
            },
            settings: {
                type: Object,
                default: () => ({
                    plugins: ['remove_button']
                })
            },
            options: {
                type: Array,
                default: () => []
            },
            value: {
                type: Array,
                default: () => []
            }
        },
        mounted : function () {
            $(this.$el).selectize({
                render: {
                    item: (item, escape) => (
                        '<div class="token">'
                            + `<span class="token__thumb thumb thumb--tiny -bg-${escape(item.style)}"></span>`
                            + `<span class="token__label">${escape(item.text)}</span>`
                        + '</div>'
                    ),
                    option: (item, escape) => (
                        '<div class="token w1/1">'
                            + `<span class="token__thumb thumb thumb--tiny -bg-${escape(item.style)}"></span>`
                            + `<span class="token__label">${escape(item.text)}</span>`
                        + '</div>'
                    )
                },
                onInitialize: () => {
                    this.updateValue();
                },
                onChange: value => {
                    this.onChange(value);
                },
                ...this.settings
            });
        },
        watch: {
            options: function () {
                this.updateOptions();
            },
            value: function() {
                this.updateValue();
            }
        },
        methods: {
            onChange(value) {
                this.$emit('input', (value === null ? [] : value));
                if (typeof this.$parent.resizeContent === 'function') {
                    this.$parent.resizeContent();
                }
            },
            updateValue () {
                this.$el.selectize.setValue(this.value, true);
            },
            updateOptions () {
                const optionValues = this.options.map(o => o.value);
                Object.values(this.$el.selectize.options)
                    .filter(option => optionValues.every(v => (v !== option.value)))
                    .forEach(option => this.$el.selectize.removeOption(option.value));
                this.$el.selectize.addOption(this.options);
                this.$el.selectize.refreshOptions(false);
                this.updateValue();
            }
        }
    };
</script>
