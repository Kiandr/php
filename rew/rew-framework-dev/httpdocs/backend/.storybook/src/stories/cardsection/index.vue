<template>
    <section class="card__section">
        <slot></slot>
    </section>
</template>

<style lang="scss" scoped>
    .card__section {
        padding: .75rem;

        &_spacious {
            padding: 1.5rem;
        }

        &_thin {
            padding: .375rem;
        }
    }
</style>