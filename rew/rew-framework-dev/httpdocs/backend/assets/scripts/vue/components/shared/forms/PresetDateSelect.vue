<documentation>
    <!-- https://flatpickr.js.org/options/ -->
    <!-- PresetDateSelect -->
    <PresetDateSelect :start_set.sync="active_start" :end_set.sync="active_end" placeholder="Select Dates..."></PresetDateSelect>
</documentation>

<template>
    <dropdown>
        <div slot="toggle">
            <input class="input preset--options" type="text" :value="this.value" ref="input" @click.prevent="onOpen()" :placeholder="placeholder"/>
        </div>
        <dropdown-menu slot="menu">
            <dropdown-item v-for='option in presets' :key="option.value" @click.native="openPresets(option.value)">{{ option.text }}</dropdown-item>
            <dropdown-item @click.native="onOpenCustom">{{ custom.text }}</dropdown-item>
        </dropdown-menu>
    </dropdown>
</template>

<style lang="scss">
    .preset--options {
        font-size: 100%;
        background-size: 16px 16px;
        background-position: right 10px center;
        background-repeat: no-repeat;
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolygon points='5 9.21 11 9.21 8 12 5 9.21'/%3E%3Cpolygon points='11 6.79 5 6.79 8 4 11 6.79'/%3E%3C/svg%3E");
        width: 100%;
    }

    .flatpickr-wrapper {
        display: block;
    }

    .flatpickr-calendar {
        top: 100% !important;
        left: 0 !important;
    }

    .flatpickr-calendar,
    .flatpickr-days,
    .dayContainer {
        width: 250px;
        min-width: 250px;
        max-width: 250px;
    }

    .dropdown-menu {
        width: 100%;
    }
</style>

<script>
    import $ from 'jquery';
    import moment from 'moment';
    import Flatpickr from 'flatpickr';
    import 'flatpickr/dist/flatpickr.css';

    export default {
        props: {
            placeholder: {
                type: [String, Boolean],
                default: '...'
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
        data() {
            return {
                presets: [
                    {text: 'No Preference', value: 0},
                    {text: 'Today', value: 1},
                    {text: 'Yesterday', value: 2},
                    {text: 'This Week', value: 3},
                    {text: 'Last Week', value: 4},
                    {text: 'Last 7 Days', value: 5},
                    {text: 'Last 30 Days', value: 6},
                    {text: 'Last 90 Days', value: 7}
                ],
                custom: {
                    value: 8,
                    text: 'Custom Range...'
                },
                pickerInstance: null,
                dateFormat: 'Y-M-D',
                value: (function (start_set, end_set) {
                    if (start_set === end_set) {
                        return start_set;
                    }

                    if (start_set && end_set) {
                        return start_set + ' to ' + end_set;
                    }

                    if (start_set) {
                        return start_set;
                    }

                    return '';
                }(this.start_set, this.end_set))
            };
        },
        mounted() {
            const input = this.$refs.input;
            const opts = {
                mode: 'range',
                clickOpens: false,
                altFormat: this.dateFormat,
                onChange: this.onCustom,
                appendTo: $('.dropdown-wrap')[0]
            };
            this.pickerInstance = new Flatpickr(input, opts);
        },
        methods: {
            onOpen() {
                // Opening preset joined date filters
                if(this.pickerInstance != null) {
                    this.pickerInstance.close();
                }
            },
            openPresets(value) {
                // On submit preset joined date filter
                let formattedDates = '';
                let today = moment().format(this.dateFormat);
                switch (value) {
                case 0:
                    formattedDates = [''];
                    break;
                case 1:
                    formattedDates = [
                        today
                    ];
                    break;
                case 2:
                    formattedDates = [
                        moment().add(-1, 'days').format(this.dateFormat)
                    ];
                    break;
                case 3:
                    formattedDates = [
                        moment().startOf('week').format(this.dateFormat),
                        today
                    ];
                    break;
                case 4:
                    formattedDates = [
                        moment().add(-1, 'weeks').startOf('week').format(this.dateFormat),
                        moment().add(-1, 'weeks').endOf('week').format(this.dateFormat)
                    ];
                    break;
                case 5:
                    formattedDates = [
                        moment().add(-6, 'days').format(this.dateFormat),
                        today
                    ];
                    break;
                case 6:
                    formattedDates = [
                        moment().add(-30, 'days').format(this.dateFormat),
                        today
                    ];
                    break;
                case 7:
                    formattedDates = [
                        moment().add(-90, 'days').format(this.dateFormat),
                        today
                    ];
                    break;
                }

                if(formattedDates.length < 2) {
                    this.$emit('update:start_set', formattedDates[0]);
                    this.$emit('update:end_set', formattedDates[0]);
                    this.value = formattedDates[0];
                } else {
                    this.$emit('update:start_set', formattedDates[0]);
                    this.$emit('update:end_set', formattedDates[1]);
                    this.value = formattedDates[0] + ' to ' + formattedDates[1];
                }
                this.pickerInstance.close();
            },
            onOpenCustom() {
                // Opening flatpicker for custom range
                if(this.pickerInstance != null) {
                    this.pickerInstance.open();
                    this.pickerInstance.clear();
                }
            },
            onCustom(selectedDates) {
                // On submit custom range selection
                const formattedDates = selectedDates.map(date => {
                    return this.pickerInstance.formatDate(date, 'Y-n-j');
                });
                this.$emit('update:start_set', formattedDates[0]);
                this.$emit('update:end_set', formattedDates[1]);
                if(formattedDates[0]){
                    this.value = formattedDates[0];
                }
                if(formattedDates[1]){
                    this.value += ' to ' + formattedDates[1];
                }
            },
            beforeDestroy() {
                let picker = this.pickerInstance;
                picker ? picker.destroy() : picker = null;
            }
        },
        watch: {
            value: function(newValue) {
                this.value = newValue;
            },
            start_set: function(value) {
                this.start_set = value;
                if (this.start_set === this.end_set) this.value = value;
            },
            end_set: function(value) {
                this.end_set = value;
            }
        }
    };
</script>
