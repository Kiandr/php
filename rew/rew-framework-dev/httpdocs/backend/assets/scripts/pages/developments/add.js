import 'selectize';

// IDX builder panels
import 'common/idx-builder';

// Tags & keywords
$('select[name="tags[]"]').selectize({
    create: true,
    plugins: [
        { name: 'remove_button' },
        { name: 'drag_drop' }
    ]
});

// If not enabled, disable featured toggle
$('input[name="is_enabled"]').on('change', function () {
    const disabled = this.checked && this.value === 'N';
    $('input[name="is_featured"]').prop('disabled', disabled);
});

// Handle IDX feed change by reloading form
$('select[name="feed"]').on('change', function () {
    var $form = $(this).closest('form');
    var action = $form.attr('action') || '';
    action += action.indexOf('?') === -1 ? '?reload' : '&reload';
    $form.attr('action', action).trigger('submit');
});

// Toggle search builder vs snippet
var $builder = $('#criteria-builder');
var $snippet = $('#criteria-snippet');
$('input[name="search_criteria"]').on('change', function () {
    var $this = $(this), value = $this.val();
    switch (value) {
    case 'builder':
        $builder.removeClass('hidden');
        $snippet.addClass('hidden');
        break;
    case 'snippet':
        $snippet.removeClass('hidden');
        $builder.addClass('hidden');
        break;
    case 'disabled':
        $snippet.addClass('hidden');
        $builder.addClass('hidden');
        break;
    }
});

// URL slug preview
$('input[data-preview]').each(function () {
    var $this = $(this), preview = $this.data('preview');
    $(preview).on('keyup', function () {
        $(preview).trigger('preview');
    }).on('preview', function () {
        var value = $this.data('preview-value');
        $this.val(typeof value === 'string'
            ? value.replace('*', this.value)
            : this.value
        );
    }).trigger('preview');
});

// Bind input changes
$('input[data-bind]').each(function () {
    var $this = $(this), bind = $this.data('bind');
    if ($this.val().toLowerCase() === $(bind).val().toLowerCase()) {

        // Sync values of input
        $this.on('keyup.bind', function () {
            $(bind).val(this.value)
                .data('bind', this.value)
                .trigger('keyup');
        });

        // Disable auto-bind on value change
        $(bind).on('change', function () {
            $(this).off('keyup.bind');
            $this.off('keyup.bind');
        });

    }
});
