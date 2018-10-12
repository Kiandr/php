<documentation>
  <!--
    Buttons are used for actions, like in forms, while textual hyperlinks are used for destinations, or moving from one page to another.

    Usage:
      <button is="btn">Button</button>

    Buttons are meant to be attached to the default HTML tag.
    You CAN use <btn> but that's considered bad practice and heavily discouraged.

    The buttons also accept modifiers for backgrounds and text types, and some visual properties too
      visual........[ flat | ghost ]
      color.........[ text-primary | text-success | text-warning | text-danger ]
      background....[ primary | success | warning | danger ]
  -->
</documentation>

<template>
    <button type="button" :class="`${button.baseClass} ${modifier_classes}`">
        <slot></slot>
    </button>
</template>

<style lang="scss" scoped>
    :root {
        --button-font-family: $button-font-family;
        --button-font-size: $button-font-size;
        --button-padding: $button-padding;
        --button-font-weight: $button-font-weight;
        --button-border-radius: $button-border-radius;
        --button-border: $border;

        --button-background-color: $button-background-color;
        --button-border-color: $button-border-color;
        --button-color: $button-text-color;
    }

    .btn {
        font-family: var(--button-font-family);
        font-size: var(--button-font-size);
        padding: var(--button-padding);
        line-height: 1;

        font-weight: var(--button-font-weight);
        display: inline-block;
        text-align: center;
        user-select: none;
        cursor: pointer;

        border-radius: var(--button-border-radius);

        border: 0;

        // Color Variations
        background-color: var(--button-background-color);
        border-color: var(--button-border-color);
        color: var(--button-color);

        // Transitions & Hardware Acceleration
        will-change: transform, background-color;
        touch-action: manipulation;
        transition: 150ms;

        // Browser Engine Defaults
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        -webkit-font-smoothing: antialiased;
        appearance: none;

        &:hover,
        &.hover {
            background-color: darken($button-background-color, 2.5%);
            border-color: darken($button-background-color, 2.5%);
        }

        &:active,
        &.active {
            box-shadow: inset transparentize($gray-600, .9) 0 1px 1px 1px;
            background-color: darken($button-background-color, 5%);
            border-color: darken($button-background-color, 5%);
        }

        &:focus,
        &.focus {
            outline: 0;
        }

        &:disabled,
        &[disabled],
        &.disabled {
            background-color: transparent;
            border-color: transparent;
            cursor: not-allowed;
            opacity: .5;
        }

        &,
        &:hover,
        &.hover,
        &:active,
        &.active,
        &:disabled,
        &[disabled],
        &.disabled,
        &:focus,
        &.focus {
            color: var(--button-color);
            text-decoration: none;
        }

        &-flat,
        &-ghost {
            background-color: transparent;
            border-color: transparent;
        }

        @each $index, $color in $colors {
            &-#{$index} {
                background-color: $color;
                border-color: $color;
                color: _text-set($color);

                &:hover,
                &.hover,
                &:active,
                &.active,
                &:disabled,
                &[disabled],
                &.disabled,
                &:focus,
                &.focus {
                    background-color: lighten($color, 3%) !important;
                    border-color: lighten($color, 3%) !important;
                    color: _text-set($color) !important;
                }
            }

            &-text-#{$index} {
                background-color: transparent;
                border-color: transparent;
                color: $color;

                &:hover,
                &.hover,
                &:active,
                &.active,
                &:disabled,
                &[disabled],
                &.disabled,
                &:focus,
                &.focus {
                    background-color: transparentize($color, .92) !important;
                    border-color: transparentize($color, .92) !important;
                    color: $color !important;
                }
            }
        }

        .text {
            vertical-align: bottom;
        }
    }
</style>

<script>
    export default {
        props: {
            modifiers: {
                default: () => [],
                type: Array
            }
        },
        data: function () {
            return {
                button: {
                    baseClass: 'btn'
                }
            };
        },
        computed: {
            modifier_classes: function () {
                for (let i = 0; i < this.modifiers.length; i++) {
                    this.modifiers[i] = this.button.baseClass.concat('-' + this.modifiers[i]);
                }
                return this.modifiers.join(' ');
            }
        }
    };
</script>