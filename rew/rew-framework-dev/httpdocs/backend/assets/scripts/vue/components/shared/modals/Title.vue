<template>
  <header class="modal--title">
    <slot></slot>
  </header>
</template>

<style lang="scss" scoped>
    .modal--title {
        border-top-right-radius: $modal-border-radius;
        border-top-left-radius: $modal-border-radius;
        font-weight: $font-weight-semibold;
        padding: $modal-padding-base;
        background-color: #edeef1;
        min-height: 3rem;
        font-size: 17px;
        line-height: 1;
        display: flex;
        align-items: center;

        &.-has-tabs {
            padding: 16px;
        }
    }
</style>
