<template>
    <div class="flyout-filter" :class="{'-is-flyout-open' : is_open}">
        <a class="close" @click.prevent="close" href="#">
            <icon name="icon--close-flyout" class="icon--close" :modifiers="['-md']" />
        </a>
        <div>
            <avatar-icons></avatar-icons>
            <header class="flyout-header">
                <h2 class="title">Filter Agents</h2>
            </header>
            <form @submit.prevent="submitFilterAgents">
                <div class="flyout-content">
                    <div>
                        <div class="-mar-bottom">
                            <label class="field__label">First Name</label>
                            <input type="text" v-model="first_name" name="first_name" @input="clearInvalidWarnings" />
                        </div>
                        <div class="-mar-bottom">
                            <label class="field__label">Last Name</label>
                            <input type="text" v-model="last_name" name="last_name" @input="clearInvalidWarnings" />
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
            name: {
                type: String,
                default: () => ''
            }
        },
        data: function () {
            const { first_name, last_name } = this.$route.query;
            return {
                first_name: first_name ? first_name : '',
                last_name: last_name ? last_name : '',
                invalid: []
            };
        },
        methods: {
            submitFilterAgents: function () {
                if (window.innerWidth <= 500) {
                    sessionStorage.removeItem('isOpenFilterAgents');
                }
                window.location.href = '?search=' + this.queryStringFilterAgents();
            },
            queryStringFilterAgents: function () {
                let queryStringPages = '';
                if (this.first_name !== '') {
                    queryStringPages += '&first_name=' + encodeURI(this.first_name.trim());
                }
                if (this.last_name !== '') {
                    queryStringPages += '&last_name=' + encodeURI(this.last_name.trim());
                }
                return queryStringPages;
            },
            clearInvalidWarnings: function () {
                this.invalid = [];
            },
            close: function () {
                sessionStorage.removeItem('isOpenFilterAgents');
                store.dispatch('flyouts/close');
            }
        },
        computed: {
            is_open: function () {
                let isOpen = this.name == store.getters['flyouts/isOpen'];
                let isOpenSession = sessionStorage.getItem('isOpenFilterAgents');
                if (isOpenSession) {
                    store.dispatch(`flyouts/${'open'}`, {
                        flyout: 'Filter'
                    });
                } else if (isOpen) {
                    sessionStorage.setItem('isOpenFilterAgents', true);
                }
                isOpen = isOpenSession ? isOpenSession : isOpen;
                return isOpen;
            }
        }
    };
</script>