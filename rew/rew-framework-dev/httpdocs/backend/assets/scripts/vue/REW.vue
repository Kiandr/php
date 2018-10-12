<documentation>
  <!--//-->
</documentation>

<template>
  <div v-if="authUser">
    <router-view></router-view>
  </div>
</template>

<style lang="scss" scoped>
    //
</style>

<script>

  import store from 'store';

  export default {
      mounted: function () {
          store.dispatch('auth/authenticateUser');
      },
      computed: {
          authUser: function () {
              return store.getters['auth/getUser'];
          }
      }
  };
</script>