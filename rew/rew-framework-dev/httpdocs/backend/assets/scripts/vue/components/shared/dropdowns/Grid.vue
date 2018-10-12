<template>
  <div class="dropdown-grid">
    <slot></slot>
  </div>
</template>

<style lang="scss" scoped>
    :root {
        --dropdown-grid-columns: 2;
        --dropdown-grid-column-gap: 1rem;
    }

    .dropdown-grid {
        grid-template-columns: repeat(var(--dropdown-grid-columns), 1fr);
        grid-column-gap: var(--dropdown-grid-column-gap);
        padding-right: .375rem;
        padding-left: .375rem;
        display: grid;

        /deep/ .dropdown-item {
            border-radius: .125rem;
        }
    }
</style>