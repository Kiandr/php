<template>
  <div v-cloak class="collapse__wrap" :class="{'-is-open': open}">
    <div ref="collapse" class="collapse" :style="{height: collapseHeight + 'px'}">
      <div ref="collapseContainer" class="collapse__container">
        <slot></slot>
      </div>
    </div>
    <div class="more">
      <a tabindex="0" @click="toggleHandler" @keyup.enter="toggleHandler">
        <span class="caret" :class="{'-is-open': open}"></span>
        <span v-if="!open">Show More</span>
        <span v-else>Show Less</span>
      </a>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.collapse {
    overflow: hidden;
    transition: height 0.15s;
}

.more {
    color: $primary;
    cursor: pointer;
    align-self: flex-end;
    white-space: nowrap;
}

.more:hover {
    color: darken($primary, 25%);
}

.more a {
    color: inherit;
    padding-left: 4px;
    padding-right: 4px;
}

.more a:focus {
    outline: 1px solid transparentize($primary, 0.5);
}

.more span {
    color: inherit;
}

.caret {
    width: 0;
    height: 0;
    color: inherit;
    margin-bottom: 2px;
    display: inline-block;
    vertical-align: middle;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-top: 4px solid currentColor;
}

.caret.-is-open {
    transform: rotate(180deg);
}

[v-cloak] {
    display: none;
}
</style>

<script>
    export default {
        props: {
            defaultHeight: {
                default: 0,
                type: Number,
                required: true
            },
            onClick: {
                type: Function,
                default: () => {}
            }
        },
        data: function () {
            return {
                open: false,
                collapseHeight: this.defaultHeight
            };
        },
        methods: {
            toggleCollapse: function (evt) {
                evt.preventDefault();
                this.open = !this.open;
                const collapse = this.$refs.collapse;
                const collapseContainer= this.$refs.collapseContainer;
                const height = !this.open ? this.defaultHeight : this.$refs.collapseContainer.clientHeight;
                this.collapseHeight = height;

                /**
                 * Force the style mutation onto the task queue
                 * 100ms delay required for transition between states
                 */
                setTimeout(() => {
                    collapse.setAttribute('style', `height: ${this.open ? collapseContainer.clientHeight + 'px' : this.defaultHeight + 'px'}`);
                }, 100);
                this.collapseHeight = collapseContainer.clientHeight;

                collapse.addEventListener('transitionend', () => {
                    const isOpen = collapse.style.height === collapseContainer.clientHeight;
                    const isClosed = collapse.style.height === this.defaultHeight + 'px' || collapse.style.height < collapseContainer.clientHeight + 'px';
                    if (isClosed) return;
                    if (!isOpen && !isClosed) collapse.style.height = 'auto';
                });
            },
            toggleHandler: function (evt) {
                this.toggleCollapse(evt);
                this.onClick();
            }
        }
    };
</script>
