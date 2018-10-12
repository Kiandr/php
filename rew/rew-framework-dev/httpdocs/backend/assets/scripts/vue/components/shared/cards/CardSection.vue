<template>
  <section class="card--section">
    <slot></slot>
  </section>
</template>

<style lang="scss" scoped>
    .card--section {
        padding: .75rem;

        &_spacious {
            padding: 1.5rem;
        }

        &_thin {
            padding: .375rem;
        }
    }
</style>