<template>
    <div class="field">
        <label class="field__label"> {{ title }} <span class="range__label">{{ range }}</span></label>
        <div class="slider hidden">
            <select v-model="minimum" class="min" :name="name + '_min'">
                <option :value="0" >No Minimum</option>
                <option v-for="n in max_limit" :value="n">{{ n }}</option>
                <option :value="max_limit + 1">More than {{ max_limit }}</option>
            </select>
            <select v-model="maximum" class="max" :name="name + '_max'">
                <option :value="0" >No Maximum</option>
                <option v-for="n in max_limit" :value="n">{{ n }}</option>
                <option :value="max_limit + 1">More than {{ max_limit }}</option>
            </select>
        </div>
        <div ref="slider"></div>
    </div>
</template>

<style land="scss" scoped>
    .range__label {
        position: absolute;
        font-size: 10px;
        right: 0;
    }
</style>

<script>
    import $ from 'jquery';

    export default {
        data() {
            return {
                minimum: parseInt(this.min_value),
                maximum: parseInt(this.max_value),
                maximumLimit: this.max_limit
            };
        },
        props: {
            name: {
                type: String,
                required: true
            },
            title: {
                type: String,
                required: true
            },
            min_value: {
                type: [Number, String],
                default: 0
            },
            max_value: {
                type: [Number, String],
                default: 0
            },
            max_limit: {
                type: Number,
                default: 25
            }
        },
        mounted: function() {
            const el = this.$refs.slider;
            $(el).slider({
                min: 1,
                max: this.max_limit + 2,
                range: true,
                values: [this.minimum + 1, this.maximum + 1],
                slide: (event, ui) => {
                    this.setValues(ui.values);
                },
                change: (event, ui) => {
                    this.setValues(ui.values);
                }
            });
        },
        methods: {
            setValues (values) {
                this.minimum = values[0] - 1;
                this.maximum = values[1] - 1;
                this.$emit('update:min_value', this.minimum);
                if (this.maximum === this.max_limit + 1) {
                    this.$emit('update:max_value', undefined);
                } else {
                    this.$emit('update:max_value', this.maximum);
                }
            }
        },
        computed: {
            range: function () {
                if (this.minimum === this.maximum) {
                    if (this.minimum === 0 && this.maximum === 0) {
                        return '';
                    } else if (this.maximum === this.maximumLimit + 1) {
                        return 'More than ' + this.maximumLimit;
                    } else {
                        return this.minimum;
                    }
                } else if (this.maximum === this.maximumLimit + 1) {
                    return this.minimum + ' to ' + this.maximumLimit + '+';
                } else if (this.minimum === 0) {
                    return 'Under ' + this.maximum;
                } else {
                    return this.minimum + ' to ' + this.maximum;
                }
            }
        },
        watch: {
            min_value: function(value) {
                const el = this.$refs.slider;
                $(el).slider('values', 0, value);
            },
            max_value: function(value) {
                const el = this.$refs.slider;
                $(el).slider('values', 1, value);
            }
        }
    };
</script>
