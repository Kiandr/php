<documentation>
    <!-- https://flatpickr.js.org/options/ -->
    <!-- dateField.js -->
    <DatePicker :placeholder="placeholder" :onBlur="localHandler"></DatePicker>
</documentation>

<template>
    <div class="datepicker__wrap">
        <input type="text" ref="input"
           @blur="this.onBlur"
           @focus="this.onFocus"
           :placeholder="this.placeholder"
           class="input input--date w1/1" />
        <a @click="clearDates" class="datepicker__clear" title="Clear selected dates" >
            <icon name="icon--x" :width="12" :height="12" />
        </a>
    </div>
</template>

<script>
    import Flatpickr from 'flatpickr';

    export default {
        mounted() {
            const input = this.$refs.input;
            const opts = {...this.config, ...this.pickerOpts, altFormat: this.dateFormat, onChange: this.onChange};
            this.pickerInstance = new Flatpickr(input, opts);
        },
        data() {
            return {
                pickerInstance: null
            };
        },
        props: {
            config: {
                type: Object,
                default: () => ({
                    'static': true,
                    dateFormat: 'M-j-Y',
                    altInput: true
                })
            },
            onBlur: {
                type: Function,
                default: () => {}
            },
            onFocus: {
                type: Function,
                default: () => {}
            },
            pickerOpts: {
                type: Object,
                default: () => {}
            },
            placeholder: {
                type: String,
                default: ''
            },
            dateFormat: {
                type: String,
                default: 'Y-m-d'
            }
        },
        methods: {
            onChange(selectedDates) {
                const formattedDates = selectedDates.map(date => {
                    return this.pickerInstance.formatDate(date, this.dateFormat);
                });
                this.$emit('input', formattedDates);
            },
            clearDates() {
                this.pickerInstance.clear();
            }
        },
        watch: {
            config: {
                handler(value) {
                    const formattedDates = value.defaultDate.map(date => {
                        if (date) {
                            return this.pickerInstance.parseDate(date, this.dateFormat);
                        }
                        return null;
                    });
                    if (new Date(formattedDates[1]).getDay() !== new Date().getDay()) {
                        this.pickerInstance.setDate(formattedDates, false);
                    }
                },
                deep: true
            }
        },
        beforeDestroy() {
            let picker = this.pickerInstance;
            picker ? picker.destroy() : picker = null;
        }
    };
</script>

<style scoped>
    .datepicker__wrap {
        position: relative;
    }

    .input--date {
        font-size: 100%;
        background-size: 16px 16px;
        background-position: right 10px center;
        background-repeat: no-repeat;
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolygon points='5 9.21 11 9.21 8 12 5 9.21'/%3E%3Cpolygon points='11 6.79 5 6.79 8 4 11 6.79'/%3E%3C/svg%3E");
    }

    .input--date::placeholder {
        font-size: 15px;
        font-style: italic;
    }

    .datepicker__clear {
        position: absolute;
        right: 0;
        top: 1px;
        padding: 4px 8px;
        cursor: pointer;
    }

    .datepicker__clear:hover .icon {
        fill: #222;
    }

    .icon {
        width: 12px !important;
        height: 12px !important;
    }
</style>
