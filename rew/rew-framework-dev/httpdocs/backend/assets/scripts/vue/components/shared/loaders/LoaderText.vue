<template>
  <span class="loader--text">{{ text }}</span>
</template>

<script>
  export default {
      data: function () {
          return {
              messages: [
                  'Shovelling coal into the server',
                  'Waking up the hamsters',
                  'Would you prefer chicken, steak, or tofu'
              ]
          };
      },

      computed: {
          text: function () {
              return this.messages[Math.floor(Math.random() * this.messages.length)] + '...';
          }
      }
  };
</script>