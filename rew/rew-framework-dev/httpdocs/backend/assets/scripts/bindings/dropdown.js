import Drop from 'tether-drop';

// Bindings for [data-drop] elements
$('[data-drop]').each(function () {
    const $this = $(this);
    const content = $this.data('drop');
    const $content = $(content);
    if ($content.length > 0) {

        // Drop configuration options
        const options = $this.data('drop-options') || {};

        // Initialize drop menu
        const drop = new Drop({
            target: this,
            content: '',
            ...options
        });

        // Set drop content HTML
        // this is done in open cb
        // to keep previous bindings
        drop.on('open', function () {
            $content.removeClass('hidden');
            this.content.appendChild($content.get(0));
            this.position();
        });

    }
});