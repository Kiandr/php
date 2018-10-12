<template>
    <div class="token">
        <slot></slot>
        <Icon v-if="icon" :name=icon :width=16 :height=16 />
        <span v-if="!icon" class="token__thumb" :class="`${tokenThumb.baseClass} ${modifier_classes}`">
            <img v-if="src" :src=src :alt=alt />
        </span>
        <span class="token__label">{{ label }}</span>
    </div>
</template>

<style scoped>
    .token {
        display: inline-block;
        color: inherit;
        text-decoration: inherit;
        padding: 0 2px;
        border-radius: 2px;
        vertical-align: middle;
        white-space: nowrap;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .token__thumb {
        align-self: center;
        vertical-align: middle;
    }

    .token__label {
        padding-left: 8px;
        padding-right: 8px;
        vertical-align: middle;
    }

    .token__thumb input {
        visibility: hidden;
    }

    .token__thumb input:checked {
        visibility: visible;
    }
</style>

<script>
    export default {
        props: {
            alt: String,
            label: String,
            src: String,
            icon: String,
            modifiers: {
                default: () => [],
                type: Array
            }
        },
        data: function () {
            return {
                tokenThumb: {
                    baseClass: 'thumb'
                }
            };
        },
        computed: {
            modifier_classes: function () {
                for (let i = 0; i < this.modifiers.length; i++) {
                    this.modifiers[i] = this.tokenThumb.baseClass.concat('-' + this.modifiers[i]);
                }
                return this.modifiers.join(' ');
            }
        }
    };
</script>