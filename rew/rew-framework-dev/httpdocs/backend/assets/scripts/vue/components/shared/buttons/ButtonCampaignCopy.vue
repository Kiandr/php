<template>
    <a class="bar__action" href="#" @click.prevent="toggleFilter">
        <icon name="icon--copy" class="icon--copy" />
    </a>
</template>

<script>
    import store from 'store';

    export default {
        mounted() {
            document.querySelectorAll('input[name="campaigns[]"]').forEach(
                input => input.addEventListener('change', function() {
                    if(document.querySelectorAll('input[name="campaigns[]"]:checked').length){
                        document.querySelector('#btn-copy').removeAttribute('disabled');
                    } else {
                        document.querySelector('#btn-copy').setAttribute('disabled', 'disabled');
                    }
                })
            );
        },
        methods: {
            toggleFilter: function () {
                if (document.querySelector('input[name="campaigns[]"]:checked')) {
                    const flyoutIsOpen = store.getters['flyouts/isOpen'] === 'Agents';
                    const toggleMethod = flyoutIsOpen ? 'close' : 'open';
                    store.dispatch(`flyouts/${toggleMethod}`, {
                        flyout: 'Agents'
                    });
                }
            }
        }
    };

</script>
