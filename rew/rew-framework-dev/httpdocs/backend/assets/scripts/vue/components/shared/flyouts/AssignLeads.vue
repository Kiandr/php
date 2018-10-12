<documentation>
    <sidebar :title="Title" :subTitle="Sub-Title" :placeholder="Placeholder" buttonType="primary" :ctaIcon="icon name" :ctaText="Add to Group" :name="Name" :multiple="true" :options="options" :submithandler="handler"></sidebar>
</documentation>

<template>
    <div class="sidebar" :class="{'-is-open' : is_open}">
        <div>
            <header class="sidebar__header">
                <h2 class="sidebar__title text text--strong">{{ title }}</h2>
                <a href="#" class="sidebar__close" @click.prevent="close">
                    <icon name="icon--close" :width="20" :height="20" />
                </a>
            </header>
            <div v-if="subTitle" :class="{'-has-subtitle' : subTitle}" class="sidebar__header">
                <h4 class="sidebar__title text text--mute">{{ subTitle }}</h4>
            </div>
            <div class="sidebar__search">
                <input v-model="searchWord" class="input" type="text" :placeholder="placeholder" />
            </div>
            <span v-if="warning" class="form-error -is-danger">{{ warning }}</span>
            <div class="sidebar__content">
                <div v-for="option in searchOptions">
                    <a href="#" class="sidebar__link" @click.prevent="toggleSelection(option)">
                        <div class="article" :class="{ selected__option : isSelected(option) }">
                            <div class="article__body" >
                                <div v-if="option.image_text" :class="['article__thumb thumb thumb--medium -bg-' + option.style ]">
                                    <img v-if="option.image" :src="option.image" />
                                    <span v-else class="thumb__label">{{ option.image_text }}</span>
                                </div>
                                <div v-else :class="['article__thumb thumb thumb--large -bg-' + option.style ]"></div>
                                <div class="article__content">
                                    <span class="thumb__title text text--strong">{{ option.text }}</span>
                                    <div v-if="option.lead_count" class="text text--mute">{{ option.lead_count }}</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="sidebar__footer">
            <button is="btn" @click.native="assignLeads()" :modifiers="[buttonType]" class="w1/1">
                <icon v-if="ctaIcon" :name="ctaIcon" class="icon-invert" :width="16" :height="16" />
                <span class="sidebar__cta-text">{{ ctaText }}</span>
            </button>
            <span v-if="invalid" class="form-error -is-danger">{{ invalid }}</span>
        </div>
        <div class="sidebar__shadow"></div>
    </div>
</template>

<style lang="scss" scoped>
    .sidebar {
        position: fixed;
        right: 0;
        top: 0;
        z-index: 1040;
        width: 320px;
        height: 100vh;
        background-color: #fff;
        padding: 24px 16px;
        transition: transform 210ms;
        transform: translate3d(100%, 0, 0);

        @media (max-width: 500px) {
            width: 100%;
            min-width: 100%;
        }
    }

    .sidebar.-is-open {
        transform: none;
    }

    .sidebar__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 22px;
    }

    .sidebar__title {
        flex-grow: 1;
        margin-top: 0;
        margin-bottom: 0;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .sidebar__close {
        flex-grow: 0;
        padding: 8px;
    }

    .sidebar__close:hover,
    .sidebar__close:focus {
        outline: 0;
        background-color: rgba(236, 238, 240, 0.6);
    }

    .sidebar__close .icon {
        width: 20px !important;
        height: 20px !important;
    }

    .sidebar__search {
        margin-bottom: 18px;
    }

    .sidebar__search .input {
        border: 0;
        width: 100%;
        font-size: 16px;
        padding: 10px 12px;
        border-radius: 2px;
        background-color: #eceef0;
    }

    .sidebar__search .input::placeholder {
        color: #555;
        font-size: 16px;
        font-style: italic;
        padding-bottom: 3px;
    }

    .sidebar__content {
        height: 100%;
        min-height: calc(100vh - 200px);
        max-height: calc(100vh - 200px);
        margin-left: -5px;
        margin-right: -5px;
        overflow-y: hidden;

        .-has-subtitle ~ & {
            min-height: calc(100vh - 240px);
            max-height: calc(100vh - 240px);
        }

        @media(max-width: 1023px) {
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media(min-width: 1024px) {
            &:hover,
            &:focus {
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    }

    @media (max-width: 500px) {
        .sidebar__content {
            min-height: calc(100vh - 265px);
            max-height: calc(100vh - 265px);
        }
    }

    @supports(-webkit-overflow-scrolling: touch) {
        @media (max-height: 820px) {
            .sidebar__content {
                min-height: calc(100vh - 295px);
                max-height: calc(100vh - 295px);
            }
        }
    }

    @media (max-height: 568px) {
        .sidebar__content {
            min-height: calc(100vh - 280px);
            max-height: calc(100vh - 280px);
        }
    }

    .sidebar__link {
        color: inherit;
        text-decoration: none;
    }

    .sidebar__content .article {
        margin-bottom: 12px;
        padding: 8px;
        border-radius: 2px;
    }

    .sidebar__content .selected__option,
    .sidebar__content .article:hover,
    .sidebar__content .article:active {
        color: #fff;
        background-color: #1a8ffc;
    }

    .sidebar__content .thumb--large {
        width: 50px;
        height: 50px;
    }

    .sidebar__content .thumb__title {
        max-width: 220px;
        color: inherit;
        text-decoration: none;
        display: inline-block;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .sidebar__footer {
        position: relative;
        button {
            z-index: 9999;
            position: relative;
        }
    }

    .sidebar__footer .icon {
        width: 16px !important;
        height: 16px !important;
    }

    .sidebar__cta-text {
        font-size: 16px;
        vertical-align: middle;
        text-shadow: 0 1px 0 rgba(#222, 0.35%);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: antialiased;
    }

    .sidebar__cta-fade {
        color: #fff;
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1005;
        padding-left: 10px;
        padding-bottom: 10px;
        margin-bottom: 35px;
        background: linear-gradient(rgba(255, 255, 255, 0.65), #fff);
    }

    .sidebar__shadow {
        box-shadow: inset 2px 0 3px 0 rgba(64, 64, 76, 0.2);
        pointer-events: none;
        position: absolute;
        z-index: 1000;
        width: 3px;
        bottom: 0;
        left: 0;
        top: 0;
    }

    /deep/ .form-error {
        position: relative;
        z-index: 10;
        display: inline-block;
        max-width: 450px;
        padding: 5px 8px;
        margin: -6px 0 0;
        font-size: 13px;
        font-weight: normal;
        border-style: solid;
        border-width: 1px;
        border-radius: 3px;
        color: #911;
        background-color: #fcdede;
        border-color: #d2b2b2;

        &::before,
        &::after {
            position: absolute;
            bottom: 100%;
            left: 10px;
            z-index: 15;
            width: 0;
            height: 0;
            pointer-events: none;
            content: " ";
            border: solid transparent;
        }

        &::before {
            border-bottom-color: #d2b2b2;
            margin-left: -1px;
            border-width: 6px;
        }

        &::after {
            border-bottom-color: #fcdede;
            border-width: 5px;
        }
    }
</style>

<script>

    import store from 'store';

    export default {
        props: {
            title: {
                type: String,
                default: () => 'Sidebar Title'
            },
            subTitle: {
                type: String,
                default: () => ''
            },
            placeholder: {
                type: String,
                default: () => 'Type to filter...'
            },
            buttonType: {
                type: String,
                default: 'primary' // [primary|success|warning|danger]
            },
            ctaText: {
                type: String,
                default: () => ''
            },
            ctaIcon: {
                type: String,
                default: () => ''
            },
            name: {
                type: String,
                default: () => ''
            },
            options: {
                type: Array,
                default: () => []
            },
            submitHandler: {
                type: Function,
                required: true
            },
            multiple: {
                type: Boolean,
                default: true
            }
        },
        data: function () {
            return {
                selectedOptions:  [],
                searchWord: '',
                invalid: '',
                warning: ''
            };
        },
        methods: {
            close: function () {
                this.invalid = '';
                this.searchWord = '';
                this.selectedOptions = [];
                this.warning = '';
                store.dispatch('flyouts/close');
            },

            toggleSelection: function (option) {
                this.invalid = '';
                if (this.selectedOptions.indexOf(option) > -1) {
                    let index = this.selectedOptions.indexOf(option);
                    this.selectedOptions.splice(index, 1);
                    this.warning = '';
                } else {
                    if(this.multiple) {
                        this.selectedOptions.push(option);
                    } else {
                        this.selectedOptions = [];
                        this.selectedOptions.push(option);
                    }
                }
            },

            isSelected: function (option) {
                if (this.selectedOptions.indexOf(option) > -1) {
                    return true;
                }
            },

            assignLeads: function () {

                // Require at least one option selected
                if ((this.selectedOptions.length < 1 || this.selectedOptions == undefined)) {
                    this.invalid = 'No ' + this.name + ' Selected';
                    return false;
                }
                let lead_id = this.context.lead_id || null;

                const assignData = {
                    selectedOptions: this.selectedOptions,
                    lead_id: lead_id
                };



                if(!(this.selectedOptions.length < 1 || this.selectedOptions == undefined)) {
                    Promise.resolve(
                        this.submitHandler(assignData)
                    ).then(() => {
                        store.dispatch('flyouts/close');
                        this.selectedOptions = [];
                    });
                }
            }
        },
        computed: {
            is_open: function () {
                return this.name == store.getters['flyouts/isOpen'];
            },
            searchOptions() {
                return this.options.filter(option => {
                    return (option.text.toLowerCase().indexOf(this.searchWord.toLowerCase()) > -1) || this.isSelected(option.value);
                });
            },
            context: function () {
                return store.getters['flyouts/getContext'];
            }
        }
    };
</script>