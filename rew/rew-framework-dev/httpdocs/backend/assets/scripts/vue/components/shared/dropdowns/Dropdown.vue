<template>
  <div class="dropdown-wrap" :class="{ '-is-open': open }" v-click-outside="close">
    <button @click.prevent="toggle">
      <slot name="toggle"></slot>
    </button>
    <slot name="menu"></slot>
  </div>
</template>

<style lang="scss" scoped>
    .dropdown-wrap {
        display: inline-block;
        position: relative;
    }

    .dropdown-wrap button {
        background-color: transparent;
        border: 0;
        cursor: pointer;
        width: 100%;

        &:focus {
            outline: none;
            background-color: transparentize($gray-600, .92);
        }
    }
</style>

<script>
    export default {
        data: function () {
            return {
                open: false
            };
        },

        methods: {
            close: function () {
                this.open = false;
            },

            toggle: function () {
                this.open = !this.open;
            }
        }
    };
</script>