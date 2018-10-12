<template>
  <div :class="`${navigation.baseClass} ${modifier_classes}`">
    <div class="navigation-title" v-if="title">{{ title }}</div>
    <slot></slot>
  </div>
</template>

<style lang="scss" scoped>
    .navigation {
        display: flex;
        align-items: center;

        &-title {
            font-weight: $font-weight-bold;
            font-size: 17px;
            color: $gray-600;
        }

        &-vertical {
            display: flex;
            flex-direction: column;
        }
    }
</style>

<script>
    export default {
        props: {
            modifiers: {
                default: () => [],
                type: Array
            },

            title: String
        },

        data: function () {
            return {
                navigation: {
                    baseClass: 'navigation'
                }
            };
        },

        computed: {
            modifier_classes: function () {
                for (let i = 0; i < this.modifiers.length; i++) {
                    this.modifiers[i] = this.navigation.baseClass.concat('-' + this.modifiers[i]);
                }

                return this.modifiers.join(' ');
            }
        }
    };
</script>