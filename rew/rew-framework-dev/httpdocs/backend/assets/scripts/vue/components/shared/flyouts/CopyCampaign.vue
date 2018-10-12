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
                <a href="#" class="sidebar__link" @click.prevent="toggleSelection(all)">
                    <div class="article" :class="{ selected__option : isSelected(all) }">
                        <div class="article__body" >
                            <div :class="['article__thumb thumb thumb--medium -bg-a']">
                                <span class="thumb__label">All</span>
                            </div>
                            <div class="article__content">
                                <span class="thumb__title text text--strong">Copy to all agents</span>
                            </div>
                        </div>
                    </div>
                </a>
                <div v-for="option in searchOptions">
                    <a href="#" class="sidebar__link" @click.prevent="toggleSelection(option.value)">
                        <div class="article" :class="{ selected__option : isSelected(option.value) }">
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
            <button is="btn" @click.native="copyCampaigns" :modifiers="[buttonType]" class="w1/1">
                <icon v-if="ctaIcon" :name="ctaIcon" class="icon-invert" :width="16" :height="16" />
                <span class="sidebar__cta-text">{{ ctaText }}</span>
            </button>
            <span v-if="invalid" class="form-error -is-danger">{{ invalid }}</span>
        </div>
        <div class="sidebar__shadow"></div>
    </div>
</template>

<style lang="scss">
    @import '../../../../../styles/flyouts/AssignStyles.scss';
</style>

<script>

    import store from 'store';
    import showErrors from 'utils/showErrors';
    import actions from '../../../actions';

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
            agents: {
                type: Array,
                default: () => []
            },
            submitHandler: {
                type: Function,
                default: () => {}
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
                options: [],
                invalid: '',
                warning: '',
                all: 'all'
            };
        },
        mounted() {
            this.options = actions.getAgents().then(data => {
                const options = data.agents.map(agent => ({
                    value: agent.id,
                    text: agent.first_name + ' ' + agent.last_name,
                    style: agent.last_name[0].toLowerCase(),
                    image: agent.image,
                    image_text: agent.first_name[0] + '' + agent.last_name[0]
                })
                );
                this.options = options;
            });
        },
        methods: {
            close: function () {
                this.invalid = '';
                this.searchWord = '';
                this.selectedOptions = [];
                this.warning = '';
                store.dispatch('flyouts/close');
            },

            toggleSelection: function (id) {
                this.invalid = '';
                if (this.selectedOptions.indexOf(id) > -1) {
                    let index = this.selectedOptions.indexOf(id);
                    this.selectedOptions.splice(index, 1);
                    this.warning = '';
                } else {
                    if(this.multiple) {
                        this.selectedOptions.push(id);
                    } else {
                        this.selectedOptions = [];
                        this.selectedOptions.push(id);
                    }
                }
            },

            isSelected: function (option) {
                if (this.selectedOptions.indexOf(option) > -1) {
                    return true;
                }
            },

            copyCampaigns: function () {

                // Require at least one option selected
                if ((this.selectedOptions.length < 1 || this.selectedOptions == undefined)) {
                    this.invalid = 'No ' + this.name + ' Selected';
                    return false;
                }

                if(!(this.selectedOptions.length < 1 || this.selectedOptions == undefined)) {
                    for (var agent in this.selectedOptions)
                    {
                        this.copyCampaign(agent);
                    }

                    store.dispatch('flyouts/close');
                    this.selectedOptions = [];
                }
            },
            copyCampaign: async function (agent) {
                const agent_id = this.selectedOptions[agent];
                const $inputs = $('input[name="campaigns[]"]');
                const $checked = $inputs.filter(':checked');
                const campaigns = $checked.map((i, el) => el.value).get();
                await actions.copyCampaigns(
                    agent_id,
                    campaigns
                ).then(function() {
                    // Show success message
                    window.location = '?success';
                }).catch(error => {
                    showErrors([error.message]);
                    throw error; // Re-throw
                });
            }
        },
        computed: {
            is_open: function () {
                return this.name == store.getters['flyouts/isOpen'];
            },
            searchOptions() {
                if(typeof this.options.filter !== 'function') {
                    return [];
                }
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