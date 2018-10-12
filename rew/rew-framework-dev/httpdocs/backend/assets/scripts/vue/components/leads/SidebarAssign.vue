<template>
  <div class="flyout--secondary-assign">
    <!--//-->
  </div>
</template>

<style lang="scss" scoped>
    .flyout--secondary-assign {
        //
    }
</style>

<script>
  import actions from 'vue/actions';
  export default {
      data: function () {
          {
              [];
          }
      },

      mounted: function () {
          this.getAgents();
      },

      methods: {
          getAgents: async function () {
              let agents = await actions.getAgents();

              this.agents.data = agents;
          }
      }
  };
</script>