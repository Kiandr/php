<template>
    <div class="flyout-filter" :class="{'-is-flyout-open' : is_open}">
        <a class="close" @click.prevent="close" href="#">
            <icon name="icon--close-flyout" class="icon--close" :modifiers="['-md']" />
        </a>
        <div>
            <avatar-icons></avatar-icons>
            <header class="flyout-header">
                <h2 class="title">Filter Pages</h2>
            </header>
            <form @submit.prevent="submitFilterPages">
                <div class="flyout-content">
                    <div>
                        <div class="-mar-bottom">
                            <label class="field__label">Page Title</label>
                            <input type="text" v-model="page_title" name="page_title" @input="clearInvalidWarnings" />
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">File Name</label>
                            <input type="text" v-model="file_name" name="file_name" @input="clearInvalidWarnings" />
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">Link Name</label>
                            <input type="text" v-model="link_name" name="link_name" @input="clearInvalidWarnings" />
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
            const { page_title, file_name, link_name } = this.$route.query;
            return {
                page_title: page_title ? page_title : '',
                file_name: file_name ? file_name : '',
                link_name: link_name ? link_name : '',
                invalid: []
            };
        },
        methods: {
            submitFilterPages: function () {
                if (window.innerWidth <= 500) {
                    sessionStorage.removeItem('isOpenFilterPages');
                }
                window.location.href = '?search=&filter=' + this.filterType + this.subdomainPostLink + this.queryStringFilterPages();
            },
            queryStringFilterPages: function () {
                let queryStringPages = '';
                if (this.page_title !== '') {
                    queryStringPages += '&page_title=' + encodeURI(this.page_title.trim());
                }
                if (this.file_name !== '') {
                    queryStringPages += '&file_name=' + encodeURI(this.file_name.trim());
                }
                if (this.link_name !== '') {
                    queryStringPages += '&link_name=' + encodeURI(this.link_name.trim());
                }
                return queryStringPages;
            },
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            close: function () {
                sessionStorage.removeItem('isOpenFilterPages');
                store.dispatch('flyouts/close');
            }
        },
        computed: {
            is_open: function () {
                let isOpen = this.name == store.getters['flyouts/isOpen'];
                let isOpenSession = sessionStorage.getItem('isOpenFilterPages');
                if (this.filterType === 'nav') {
                    isOpenSession = false;
                }
                if (isOpenSession) {
                    store.dispatch(`flyouts/${'open'}`, {
                        flyout: 'Filter'
                    });
                } else if (isOpen) {
                    sessionStorage.setItem('isOpenFilterPages', true);
                }
                isOpen = isOpenSession ? isOpenSession : isOpen;
                return isOpen;
            }
        }
    };
</script>
