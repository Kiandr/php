<template>
    <div class="flyout-filter" :class="{'-is-flyout-open' : is_open}">
        <a class="close" @click.prevent="close" href="#">
            <icon name="icon--close-flyout" class="icon--close" :modifiers="['-md']" />
        </a>
        <div>
            <avatar-icons></avatar-icons>
            <header class="flyout-header">
                <h2 class="title">Filter Snippets</h2>
            </header>
            <form @submit.prevent="submitFilterSnippets">
                <div class="flyout-content">
                    <div>
                        <div class="-mar-bottom">
                            <label class="field__label">Snippet Name</label>
                            <input type="text" v-model="snippet_name" name="snippet_name" @input="clearInvalidWarnings" />
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flyout-footer">
                        <div class="flyout-cta-fade"></div>
                        <button is="btn" type="submit" :modifiers="['primary']" class="update--btn">
                            <span class="flyout-cta-text">Update Filter</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="filter-flyout--shadow"></div>
    </div>
</template>

<style lang="scss">
    @import '../../../../../styles/flyouts/FilterStyles.scss';
</style>

<script>

    import store from 'store';

    export default {
        props: {
            filterType: {
                type: String,
                default: ''
            },
            subdomainPostLink: {
                type: String,
                required: true
            },
            name: {
                type: String,
                default: () => ''
            }
        },
        data: function () {
            const { snippet_name } = this.$route.query;
            return {
                snippet_name: snippet_name ? snippet_name : '',
                invalid: []
            };
        },
        methods: {
            submitFilterSnippets: function () {
                if (window.innerWidth <= 500) {
                    sessionStorage.removeItem('isOpenFilterSnippets');
                }
                window.location.href = '?search=&filter=' + this.filterType + this.subdomainPostLink + this.queryStringFilterPages();
            },
            queryStringFilterPages: function () {
                let queryStringPages = '';
                if (this.snippet_name !== '') {
                    queryStringPages += '&snippet_name=' + encodeURI(this.snippet_name.trim());
                }
                return queryStringPages;
            },
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            close: function () {
                sessionStorage.removeItem('isOpenFilterSnippets');
                store.dispatch('flyouts/close');
            }
        },
        computed: {
            is_open: function () {
                let isOpen = this.name == store.getters['flyouts/isOpen'];
                let isOpenSession = sessionStorage.getItem('isOpenFilterSnippets');
                if (isOpenSession) {
                    store.dispatch(`flyouts/${'open'}`, {
                        flyout: 'Filter'
                    });
                } else if (isOpen) {
                    sessionStorage.setItem('isOpenFilterSnippets', true);
                }
                isOpen = isOpenSession ? isOpenSession : isOpen;
                return isOpen;
            }
        }
    };
</script>