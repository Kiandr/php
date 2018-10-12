<template>
  <div class="dropdown-menu dropdown-menu_sw" :class="modifier_classes">
    <slot></slot>
  </div>
</template>

<style lang="scss" scoped>
    @mixin dropdown-caret($direction: 'se') {
        position: absolute;
        overflow: hidden;
        height: .5rem;
        width: 1rem;

        &::before {
            background: #fff;
            content: '';
            pointer-events: none;
            height: 1rem;
            width: 1rem;
            border: 1px solid transparentize(#1b1f23, .8);
            transform: rotate(45deg);
            position: absolute;
            display: block;
        }

        // North
        @if ($direction == 'n') {
            margin-right: -.5rem;
            bottom: -.5rem;
            right: 50%;
            left: auto;
            top: auto;

            &::before {
                border-bottom-right-radius: 3px;
                bottom: .25rem;
                right: auto;
                left: auto;
                top: auto;
            }
        }

        // West
        @if ($direction == 'w') {
            margin-bottom: -.5rem;
            height: 1rem;
            width: .5rem;
            bottom: 50%;
            right: -.5rem;
            left: auto;
            top: auto;

            &::before {
                border-top-right-radius: 3px;
                bottom: auto;
                right: .25rem;
                left: auto;
                top: auto;
            }
        }

        // South
        @if ($direction == 's') {
            margin-right: -.5rem;
            bottom: auto;
            right: 50%;
            left: auto;
            top: -.5rem;

            &::before {
                border-top-left-radius: 3px;
                bottom: auto;
                right: auto;
                left: auto;
                top: .25rem;
            }
        }

        // East
        @if ($direction == 'e') {
            margin-bottom: -.5rem;
            height: 1rem;
            width: .5rem;
            bottom: 50%;
            right: auto;
            left: -.5rem;
            top: auto;

            &::before {
                border-bottom-right-radius: 3px;
                bottom: auto;
                right: auto;
                left: .25rem;
                top: auto;
            }
        }

        // South East
        @if ($direction == 'se') {
            bottom: auto;
            right: auto;
            left: 1rem;
            top: -.5rem;

            &::before {
                border-top-left-radius: 3px;
                bottom: auto;
                right: auto;
                left: auto;
                top: .25rem;
            }
        }

        // North East
        @if ($direction == 'ne') {
            bottom: -.5rem;
            right: auto;
            left: 1rem;
            top: auto;

            &::before {
                border-bottom-right-radius: 3px;
                bottom: .25rem;
                right: auto;
                left: auto;
                top: auto;
            }
        }

        // North West
        @if ($direction == 'nw') {
            bottom: -.5rem;
            right: 1rem;
            left: auto;
            top: auto;

            &::before {
                border-bottom-right-radius: 3px;
                bottom: .25rem;
                right: auto;
                left: auto;
                top: auto;
            }
        }

        // South West
        @if ($direction == 'sw') {
            bottom: auto;
            right: 1rem;
            left: auto;
            top: -.5rem;

            &::before {
                border-top-left-radius: 3px;
                bottom: auto;
                right: auto;
                left: auto;
                top: .25rem;
            }
        }
    }

    .dropdown-menu {
        @include shadow(1);
        background-color: $white;
        white-space: nowrap;
        border-radius: 3px;
        min-width: 10rem;
        padding-bottom: .375rem;
        padding-top: .375rem;
        position: absolute;
        z-index: 1030;
        will-change: transform, opacity;
        transition: .2s cubic-bezier(.2, 0, .13, 1.25);
        pointer-events: none;
        opacity: 0;

        .-is-open & {
            pointer-events: auto;
            transform: none;
            opacity: 1;
        }

        // South East (Default)
        &,
        &_se,
        &_southeast {
            transform: translateY(1rem);
            bottom: auto;
            right: auto;
            left: 0;
            top: 100%;

            .caret {
                @include dropdown-caret('se');
            }
        }

        // South West
        &_sw,
        &_southwest {
            transform: translateY(1rem);
            bottom: auto;
            right: 0;
            left: auto;
            top: 100%;

            .caret {
                @include dropdown-caret('sw');
            }
        }

        // Start Irregular Positioning

        // North
        &_n,
        &_north {
            transform: translateY(-1rem) translateX(50%);
            bottom: 100%;
            right: 50%;
            left: auto;
            top: auto;

            .-is-open & {
                transform: translateX(50%);
            }

            .caret {
                @include dropdown-caret(n);
            }
        }

        // West
        &_w,
        &_west {
            transform: translateX(-1rem) translateY(50%);
            bottom: 50%;
            right: 100%;
            left: auto;
            top: auto;

            .-is-open & {
                transform: translateY(50%);
            }

            .caret {
                @include dropdown-caret(w);
            }
        }

        // South
        &_s,
        &_south {
            transform: translateY(1rem) translateX(50%);
            bottom: auto;
            right: 50%;
            left: auto;
            top: 100%;

            .-is-open & {
                transform: translateX(50%);
            }

            .caret {
                @include dropdown-caret(s);
            }
        }

        // East
        &_e,
        &_east {
            transform: translateX(1rem) translateY(50%);
            bottom: 50%;
            right: auto;
            left: 100%;
            top: auto;

            .-is-open & {
                transform: translateY(50%);
            }

            .caret {
                @include dropdown-caret(e);
            }
        }

        // North East
        &_ne,
        &_northeast {
            transform: translateY(1rem);
            bottom: 100%;
            right: auto;
            left: 0;
            top: auto;

            .caret {
                @include dropdown-caret(ne);
            }
        }

        // North West
        &_nw,
        &_northwest {
            transform: translateY(1rem);
            bottom: 100%;
            right: 0;
            left: auto;
            top: auto;

            .caret {
                @include dropdown-caret(nw);
            }
        }

        hr {
            margin: .375rem .75rem;
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
                dropdownMenu: {
                    baseClass: 'dropdown-menu'
                }
            };
        },

        computed: {
            modifier_classes: function () {
                for (let i = 0; i < this.modifiers.length; i++) {
                    this.modifiers[i] = this.dropdownMenu.baseClass.concat('_' + this.modifiers[i]);
                }

                return this.modifiers.join(' ');
            }
        }
    };
</script>