<documentation>
  <!--
      Thumbnails are a simple - yet incredibly smart - utility component used all over.
      They provide:
        - a plethora of colors (found in the global variables SASS map)
        - Text options to be fed into the name attribute as an array
        - Ability to pass just the image and the alt and have it print out an <img> tag

      Additionally:
        - Pass a string or integer to the [score] property and a percentage meter will appear as well
  -->
</documentation>

<template>
    <div class="thumb-wrap">
        <div v-if="score" :class="`${thumb.baseClass}-score ${color_class}`">
            <i :class="`${thumb.baseClass}-score-bar`" :style="`width: ${score}%`"></i>
        </div>

        <img v-if="src" :src=src :alt=alt :class="`${thumb.baseClass} ${color_class} ${modifier_classes}`" />

        <div v-else-if="context.length" :class="`${thumb.baseClass} ${color_class} ${modifier_classes}`" class="thumb-text-center">{{ text }}</div>

        <div v-else-if="!src && !context.length" :class="`${thumb.baseClass} ${modifier_classes}`" class="thumb-r thumb-text-center">
            <slot>
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" xml:space="preserve" width="24" height="24">
                    <path data-color="color-2" fill="currentColor" d="M15.461,14.023C14.424,14.632,13.258,15,12,15s-2.424-0.368-3.461-0.977 C4.894,14.262,2,17.296,2,21v1.73l0.695,0.223C2.829,22.995,6.038,24,12,24s9.171-1.005,9.305-1.047L22,22.73V21 C22,17.296,19.106,14.262,15.461,14.023z"></path>
                    <path data-color="color-1" fill="currentColor" d="M12,13c3.379,0,6-3.763,6-7c0-3.309-2.691-6-6-6S6,2.691,6,6C6,9.237,8.621,13,12,13z"></path>
                </svg>
            </slot>
        </div>
    </div>
</template>

<style lang="scss">
    :root {
        --thumb-background-color: $thumb-background-color;
        --thumb-border-radius: $thumb-border-radius;
        --thumb-font-size: $thumb-font-size;
        --thumb-size: $thumb-size;
        --thumb-xsmall-size-multiplier: .5;
        --thumb-small-size-multiplier: .675;
        --thumb-large-size-multiplier: 1.5;
    }

    .thumb {
        background-color: var(--thumb-background-color);
        border-radius: var(--thumb-border-radius);
        height: var(--thumb-size);
        width: var(--thumb-size);
        display: inline-flex;
        font-weight: $font-weight-medium;
        font-size: var(--thumb-font-size);
        line-height: 1;

        &-wrap {
            display: inline-block;
            position: relative;
        }

        &-score {
            background-color: $white;
            position: absolute;
            bottom: .25rem;
            right: .25rem;
            left: .25rem;
            z-index: 1;

            border-radius: .31rem;
            height: .31rem;

            &-bar {
                background-color: currentColor;
                margin-top: 1px;
                display: block;

                border-radius: .19rem;
                height: .19rem;
            }
        }

        @each $index, $colors in $thumbs {
            $background: map_get($colors, 'background');
            $color: map_get($colors, 'color');

            &-#{$index} {
                background-color: $background;
                color: $color;
            }
        }

        &-text-center {
            text-transform: uppercase;
            justify-content: center;
            display: inline-flex;
            align-items: center;
        }

        &-xs,
        &-xsmall {
            font-size: $font-size-xs;
            height: calc(var(--thumb-size) * var(--thumb-xsmall-size-multiplier));
            width: calc(var(--thumb-size) * var(--thumb-xsmall-size-multiplier));
            font-weight: $font-weight-bold;

            & svg {
                width: calc(var(--thumb-size) * var(--thumb-xsmall-size-multiplier) / 2);
                height: calc(var(--thumb-size) * var(--thumb-xsmall-size-multiplier) / 2);
            }
        }

        &-sm,
        &-small {
            font-size: $font-size-sm;
            height: calc(var(--thumb-size) * var(--thumb-small-size-multiplier));
            width: calc(var(--thumb-size) * var(--thumb-small-size-multiplier));
            font-weight: $font-weight-semibold;

            & svg {
                width: calc(var(--thumb-size) * var(--thumb-small-size-multiplier) / 2);
                height: calc(var(--thumb-size) * var(--thumb-small-size-multiplier) / 2);
            }
        }

        &-lg,
        &-large {
            font-size: $font-size-lg;
            height: calc(var(--thumb-size) * var(--thumb-large-size-multiplier));
            width: calc(var(--thumb-size) * var(--thumb-large-size-multiplier));

            & svg {
                width: calc(var(--thumb-size) * var(--thumb-large-size-multiplier) / 2);
                height: calc(var(--thumb-size) * var(--thumb-large-size-multiplier) / 2);
            }
        }
    }
</style>

<script>
    export default {
        props: {
            alt: String,
            context: {
                default: () => [],
                type: Array
            },
            modifiers: {
                default: () => [],
                type: Array
            },
            score: [String, Number],
            src: String
        },
        data: function () {
            return {
                thumb: {
                    baseClass: 'thumb'
                }
            };
        },
        computed: {
            color_class: function () {
                return (this.context.length ? this.thumb.baseClass.concat('-' + this.context[0].charAt(0).toLowerCase()) : this.thumb.baseClass.concat('-default'));
            },

            modifier_classes: function () {
                for (let i = 0; i < this.modifiers.length; i++) {
                    this.modifiers[i] = this.thumb.baseClass.concat('-' + this.modifiers[i]);
                }

                return this.modifiers.join(' ');
            },

            text: function () {
                let text = '';

                for (let i = 0; i < this.context.length; i++) {
                    text = text.concat(this.context[i].charAt(0).toUpperCase());
                }

                return text;
            }
        }
    };
</script>