<template>
  <div class="blankslate--text">
    <slot></slot>
  </div>
</template>

<style lang="scss" scoped>
    :root {
        --blankslate-text-font-weight: 400;
        --blankslate-text-font-size: .875rem;
        --blankslate-text-color: #959da5;
    }

    .blankslate--text {
        font-weight: var(--blankslate-text-font-weight);
        font-size: var(--blankslate-text-font-size);
        margin-top: .5rem;
        color: var(--blankslate-text-color);
    }
</style>