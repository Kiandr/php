<template>
    <section class="accordion__section" :class="{'-is-open': open}">
        <h3 @click="toggleAccordion" @keyup.enter="toggleAccordion" class="accordion__title" tabindex="0">
            <span>{{ title }}</span>
            <div>
                <span v-if="count" class="count">{{ count }}</span>
                <icon name="icon--caret" :width=16 :height=16 class="caret" />
            </div>
        </h3>
        <div class="accordion__content" :style="{height: contentHeight + 'px'}">
            <div @change="resizeContent" ref="contentContainer" class="wrap">
                <slot></slot>
            </div>
        </div>
    </section>
</template>

<style lang="scss" scoped>
    .accordion__title {
        margin: 0;
        cursor: pointer;
        font-size: 17px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-transform: capitalize;
    }

    .accordion__title:focus,
    .accordion__title:hover {
        outline: 0;
        color: #222;
    }

    .accordion__title .count {
        vertical-align: middle;
    }

    .accordion__title .icon {
        vertical-align: middle;
    }

    .accordion__section {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .accordion__section:not(:last-child) {
        border-bottom: 1px solid rgba(204, 204, 204, 0.75);
    }

    .accordion__content {
        height: 0;
        overflow: hidden;
        transition: height 0.25s;
    }

    .accordion__section.-is-open .accordion__content {
        overflow: visible;
    }

    .accordion__content .wrap {
        padding-top: 8px;
        padding-bottom: 8px;
    }

    .count {
        border: 0;
        font-size: 12px;
        border-radius: 3px;
        padding: 2px 8px;
        margin-right: 4px;
        color: #fff;
        background-color: $primary;
        text-shadow: 0 0 1px #222;
    }

    .accordion__section.-is-open .caret {
        transform: rotate(180deg);
    }

    .icon {
        width: 16px !important;
        height: 16px !important;
    }
</style>

<script>
    export default {
        props: {
            title: {
                type: String,
                required: true
            },
            count: {
                type: Number,
                required: false
            },
            defaultHeight: {
                default: 0,
                type: Number,
                required: false
            }
        },
        data: function () {
            return {
                open: false,
                contentHeight: this.defaultHeight
            };
        },
        methods: {
            toggleAccordion: function () {
                this.open = !this.open;
                const height = !this.open ? this.defaultHeight : this.$refs.contentContainer.clientHeight;
                this.contentHeight = height;
            },
            resizeContent: function () {
                this.$nextTick(() => {
                    this.contentHeight = this.$refs.contentContainer.clientHeight;
                });
            }
        }
    };
</script>