<template>
    <a class="bar__action" @click.prevent="toggleFilter" href="#">
        <icon name="icon--search" class="icon--search" />
    </a>
</template>

<script>
    import store from 'store';

    export default {
        methods: {
            toggleFilter: function () {
                const flyoutIsOpen = store.getters['flyouts/isOpen'] === 'Filter';
                const toggleMethod = flyoutIsOpen ? 'close' : 'open';
                store.dispatch(`flyouts/${toggleMethod}`, {
                    flyout: 'Filter'
                });
            }
        }
    };
</script>