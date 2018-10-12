<template>
  <a v-if="href" :href="href" class="dropdown-item">
    <slot></slot>
  </a>

  <button v-else-if="type === 'button' || type === 'submit'" :type="type" class="dropdown-item">
    <slot></slot>
  </button>

  <router-link v-else-if="to"  :to="{name: to}" class="dropdown-item">
    <slot></slot>
  </router-link>

  <div v-else="" class="dropdown-item">
    <slot></slot>
  </div>
</template>

<style lang="scss" scoped>
    :root {
        --dropdown-item-font-weight: $font-weight-bold;
        --dropdown-item-font-size: $font-size-base;
        --dropdown-item-padding: (1rem * .45) 1rem;
    }

    .dropdown-item {
        font-weight: var(--dropdown-item-font-weight);
        font-size: var(--dropdown-item-font-size);
        padding: var(--dropdown-item-padding);
        border-radius: 0;
        text-align: left;
        cursor: pointer;
        line-height: 1;
        display: block;
        color: $black;
        background: 0;
        width: 100%;
        outline: 0;
        border: 0;

        transition: none;

        &:hover,
        &.hover {
            color: $blue;
        }

        &_yellow,
        &_warning {
            color: #e39f48;

            &:hover,
            &.hover {
                background-color: #e39f48;
                color: #fff;
            }
        }

        &_red,
        &_danger {
            color: #e25950;

            &:hover,
            &.hover {
                background-color: #e25950;
                color: #fff;
            }
        }
    }
</style>

<script>
    export default {
        props: {
            href: String,
            modifiers: {
                default: () => [],
                type: Array
            },
            to: String,
            type: String
        },

        data: function () {
            return {
                dropdownItem: {
                    baseClass: 'dropdown-item'
                }
            };
        },

        computed: {
            modifier_classes: function () {
                for (let i = 0; i < this.modifiers.length; i++) {
                    this.modifiers[i] = this.dropdownItem.baseClass.concat('_' + this.modifiers[i]);
                }

                return this.modifiers.join(' ');
            }
        }
    };
</script>