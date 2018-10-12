<template>
    <div class="w1/1 pad8 save__search">
        <form @submit.prevent="saveSearch">
            <div class="instruction-note" v-if="task && task.info && task.info.length" v-html="task.info"></div>
            <button is="btn" type="submit" :modifiers="['primary']">Create Saved Search</button>
        </form>
    </div>
</template>

<style lang="scss" scoped>
    .save__search {
        align-self: center;
    }
</style>

<script>

    import store from 'store';

    export default {
        methods: {
            saveSearch: function () {

                // Redirect to create IDX search
                const { context_data } = this.task;
                const { search_url } = context_data;
                if (search_url && search_url.length) {
                    window.location.href = search_url;
                } else {
                    // @TODO: show some sort of error
                    //showErrors([MISSING_SEARCH_URL]);
                }

            }
        },
        computed: {
            context: function () {
                return store.getters['modal/getContext'];
            },
            task: function () {
                const { getTaskById } = store.getters;
                const taskId = this.context.id;
                return getTaskById(taskId);
            }
        }
    };
</script>
