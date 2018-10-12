<template>
    <svg :class="`${icon.baseClass} ${modifier_classes}`" :width=width :height=height aria-labelledby="title" role="img">
        <title id="title">{{ title }}</title>
        <desc v-if="description">{{ description }}</desc>
        <slot></slot>
        <use v-bind="{'xlink:href':'#' + name}"></use>
    </svg>
</template>

<style lang="scss" scoped>
    .icon {
        vertical-align: middle;
        position: relative;
        fill: #91909a;

        &-invert {
            fill: #fff;
        }
    }
</style>

<script>
    export default {
        props: {
            name: String,
            width: Number,
            height: Number,
            title: String,
            fill: String,
            description: String,
            modifiers: {
                default: () => [],
                type: Array
            }
        },
        data: function () {
            return {
                icon: {
                    baseClass: 'icon'
                }
            };
        },
        computed: {
            modifier_classes: function () {
                for (let i = 0; i < this.modifiers.length; i++) {
                    this.modifiers[i] = this.icon.baseClass.concat('-' + this.modifiers[i]);
                }
                return this.modifiers.join(' ');
            }
        }
    };
</script>