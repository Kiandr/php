<template>
  <div>
    <span>Email</span>
    <hr>
    <pre>{{ selected }}</pre>
  </div>
</template>

<script>
    import store from 'store';

    export default {
        computed: {
            selected: function () {
                return store.getters['crm/leads/getSelected'];
            }
        }
    };
</script>