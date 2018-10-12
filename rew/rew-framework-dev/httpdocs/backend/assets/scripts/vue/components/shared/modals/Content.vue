<template>
  <section class="modal--content">
    <slot></slot>
  </section>
</template>

<style lang="scss" scoped>
    .modal--content {
        padding: $modal-padding-base;
        position: relative;
        min-height: 7rem;
        max-width: 100%;
        display: flex;
        width: 55rem;

        &.-no-title {
            padding: 2.5rem;
            padding-bottom: 1.5rem;
        }

        &_spacious {
            padding: $modal-padding-lg;
        }

        &_thin {
            padding: $modal-padding-sm;
        }
        svg.loader {
            transform: translate(50%, 50%);
            position: absolute;
            bottom: 50%;
            right: 50%;
        }
        .selectize-dropdown {
            max-height: 90px;
            overflow: hidden;
        }
    }
</style>

<style lang="scss">
    .instruction-note {
        max-height: 5.5rem;
        margin-bottom: 24px;
        overflow: auto;
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0 0 24px;
            font-weight: normal;
        }
        h1 {
            font-size: 48px;
            line-height: 48px;
        }
        h2 {
            font-size: 27px;
            line-height: 48px;
        }
        h3 {
            font-size: 26px;
            line-height: 30px;
        }
        h4 {
            font-size: 22px;
            line-height: 24px;
        }
        h5 {
            font-size: 18px;
            line-height: 24px;
        }
        h6 {
            font-size: 16px;
            line-height: 24px;
        }
    }
</style>