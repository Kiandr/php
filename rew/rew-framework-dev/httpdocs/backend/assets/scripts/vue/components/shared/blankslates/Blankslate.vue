<template>
  <div class="blankslate">
    <slot></slot>
  </div>
</template>

<style lang="scss" scoped>
    :root {
        --blankslate-padding: 3rem;
    }

    .blankslate {
        text-align: center;
        padding: var(--blankslate-padding);
    }
</style>