<template>
  <div class="blankslate--title">
    <slot></slot>
  </div>
</template>

<style lang="scss" scoped>
    :root {
        --blankslate-title-font-weight: 700;
        --blankslate-title-font-size: 1rem;
        --blankslate-title-color: #444d56;
    }

    .blankslate {
        &-title {
            font-weight: var(--blankslate-title-font-weight);
            font-size: var(--blankslate-title-font-size);
            color: var(--blankslate-title-color);
            line-height: 1;
        }
    }
</style>