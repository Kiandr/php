<template>
  <div class="card">
    <slot></slot>
  </div>
</template>

<style lang="scss" scoped>
    :root {
        --card-background-color: #fff;

        --card-border-radius: .25rem;
        --card-border-width: 1px;
        --card-border-style: solid;
        --card-border-color: transparent;

        --card-box-shadow: 0 2px 3px 1px rgba(64, 64, 76, 0.05);
    }

    .card {
        background-color: var(--card-background-color);
        box-shadow: var(--card-box-shadow);
        position: relative;

        border-radius: var(--card-border-radius);
        border-style: var(--card-border-style);
        border-width: var(--card-border-width);
        border-color: var(--card-border-color);

        flex-flow: column;
        display: flex;
        width: 100%;
    }
</style>