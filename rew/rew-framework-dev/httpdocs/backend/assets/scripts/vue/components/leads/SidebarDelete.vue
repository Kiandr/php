<template>
    <div class="flyout--secondary-delete">
        <div @click="deleteLeads">
            <button is="btn">Delete</button>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    .flyout--secondary-delete {
        //
    }
</style>

<script>

    import store from 'store';

    export default {
        methods: {
            deleteLeads: function () {
                // @todo: success/error feedback
                let lead_ids = [];

                this.selected.forEach(lead_id => {
                    lead_ids.push(lead_id);
                });

                store.dispatch('crm/leads/deleteLeads', {
                    lead_ids
                });

            }
        },
        computed: {
            selected: function () {
                return store.getters['crm/leads/getSelected'];
            }
        }
    };
</script>
