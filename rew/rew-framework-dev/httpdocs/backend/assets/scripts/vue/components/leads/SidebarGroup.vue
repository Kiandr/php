<template>
  <div class="flyout--secondary-groups">
    <div class="group--item" v-for="group in groups.data" :key="group.id">{{ group.name }}</div>
  </div>
</template>

<style lang="scss" scoped>
    .flyout--secondary-groups {
        .group--item {
            //
        }
    }
</style>

<script>

    import actions from 'vue/actions';
    import store from 'store';

    export default {
        data: function () {
            return {
                groups: {
                    data: []
                }
            };
        },

        mounted: function () {
            // @todo: this should dispatch action from store
            // & use a getter to access the available groups
            // we only want to ever load all groups once
            this.getGroups();
        },

        methods: {
            getGroups: async function () {
                let groups = await actions.getGroups();

                this.groups.data = groups;
            }
        },

        computed: {
            selected: function () {
                return store.getters['crm/leads/getSelected'];
            }
        }
    };
</script>