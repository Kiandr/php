<template>
    <div class="sidebar" :class="{'-is-open' : is_open}">
        <div>
            <header class="sidebar__header">
                <h2 class="sidebar__title text text--strong">Delete</h2>
                <a href="#" class="sidebar__close" @click.prevent="close">
                    <icon name="icon--close" :width="20" :height="20" />
                </a>
            </header>
            <div class="sidebar__content">
                <img src="/backend/assets/images/illustrations/whoa.png" alt="Delete Lead" />
                <p>Whoa...Delete is</p>
                <p class="-mar-bottom">Permanent, are you sure?</p>
                <p class="sidebar__hint -mar-bottom" v-if="selected.length === 1"><span v-for="lead in selected">Once <span class="text--strong">{{lead.first_name}} {{lead.last_name}}</span> is gone, <span class="text--strong">{{lead.first_name}} {{lead.last_name}}</span> is gone for good.</span></p>
                <p class="sidebar__hint -mar-bottom" v-if="selected.length === 2">Once <span class="text--strong">{{selected[0].first_name}} {{selected[0].last_name}}</span> and <span class="text--strong">{{selected[1].first_name}} {{selected[1].last_name}}</span> are gone, they're gone for good.</p>
                <p class="sidebar__hint -mar-bottom" v-if="selected.length > 2">Once <span class="text--strong">{{selected[0].first_name}} {{selected[0].last_name}}</span> & <span class="text--strong">{{selected.length - 1}}</span> additional leads are gone, they're gone for good.</p>
            </div>
        </div>
        <div class="sidebar__footer">
            <button is="btn" class="btn--delete w1/1 -mar-bottom" @click.native="delete_leads">
                <span class="sidebar__cta-text">Yes, Delete Anyway</span>
            </button>
            <button is="btn" :modifiers="['warning']" @click.native="close" class="w1/1">
                <icon name="icon--close" class="icon-invert" :width="16" :height="16" />
                <span class="sidebar__cta-text">Cancel</span>
            </button>
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

    .sidebar__content {
        height: 100vh;
        max-height: calc(100vh - 160px);
        margin-left: -5px;
        margin-right: -5px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    @media (max-width: 500px) {
        .sidebar__content {
            max-height: calc(100vh - 218px);
        }
    }

    .sidebar__content p {
        margin: 0;
        text-align: center;
    }

    .sidebar__content img {
        display: block;
        width: 40%;
        max-width: 40%;
        margin: 0 auto;
        flex-shrink: 0;
    }

    .sidebar__hint {
        font-size: 14px;
        opacity: 0.75;
        margin-left: 20px;
        margin-right: 20px;
    }

    @media (max-width: 731px) and (orientation: landscape) {
        .sidebar {
            overflow-y: auto;
        }

        .sidebar__hint {
            display: none;
        }
    }

    @media (orientation: landscape) {
        .sidebar__content img {
            width: 30%;
            max-width: 30%;
        }
    }

    .sidebar__footer .icon {
        width: 16px !important;
        height: 16px !important;
    }

    .sidebar__cta-text {
        font-size: 16px;
        vertical-align: middle;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: antialiased;
    }

    .sidebar__footer .btn--delete {
        color: #fff;
        background-color: #f93;
        border-color: #f93;
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

    .-mar-bottom {
        margin-bottom: 12px !important;
    }
</style>

<script>
    import store from 'store';
    import showSuccess from 'utils/showSuccess';
    import showErrors from 'utils/showErrors';

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
        computed: {
            is_open: function () {
                return this.name == store.getters['flyouts/isOpen'];
            },
            selected: function () {
                const selectedLeads = store.getters['crm/leads/getSelected'];
                var leads = store.state.crm.leads.results;
                var leadInfo = [];

                for(var i = 0; i < selectedLeads.length; i++){
                    for(var j = 0; j < leads.length; j++){
                        if (selectedLeads[i] === leads[j]['id']){
                            leadInfo.push(leads[j]);
                        }
                    }
                }

                return leadInfo;
            }
        },
        methods: {
            close: function () {
                this.invalid = '';
                this.selectedOptions = [];
                this.warning = '';
                store.dispatch('flyouts/close');
            },
            delete_leads: function () {
                var success = true;
                let lead_ids = [];
                this.selected.forEach(lead => {
                    lead_ids.push(lead.id);
                });
                store.dispatch('crm/leads/deleteLeads', {
                    lead_ids
                }).catch(() => {
                    success = false;
                });

                store.dispatch('flyouts/close');

                if (success) {
                    showSuccess(['The selected lead(s) have been deleted.'], undefined, {
                        close: true,
                        expires: 10000
                    });
                } else {
                    showErrors(['There was an error. The selected lead(s) were not deleted.']);
                }
            }
        }
    };
</script>