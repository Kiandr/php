import URLS from 'constants/urls';
import 'plugins/rew_manager';
import filterInput from '../../utils/filterInput';

// Listing link preview
const $placeholder = $('#link-placeholder');
$('#listing-link').on('keyup.slugify', function () {
    const value = this.value || '[listing-link]';
    $placeholder.val(URLS.listing.replace('{{value}}', value));
}).trigger('keyup.slugify');

// Location Manager
const $citySelect = $('select[name="city"]');
const $cityManager = $('#manage-listing-cities');
$cityManager.rew_manager({
    type: 'listingLocation',
    title: 'Manage Cities',
    optionText : 'City',
    options: $citySelect,
    extraParams: function () {
        return 'state=' + $('#select-locations').val();
    }
});

// Listing Type Manager
$('#manage-listing-types').rew_manager({
    type: 'listingType',
    title: 'Manage Listing Types',
    optionText: 'Type',
    options: $('#select-listing-types')
});

// Listing Status Manager
$('#manage-listing-status').rew_manager({
    type: 'listingStatus',
    title: 'Manage Listing Statuses',
    optionText: 'Status',
    options: $('#select-listing-statuses')
});

// Listing Feature Manager
const $features = $('#feature-list');
$('#manage-listing-features').rew_manager({
    type: 'listingFeature',
    title: 'Manage Listing Features',
    optionText: 'Feature',
    options: $features.data('options'),
    onAdd: function (event, option) {
        const value = $('<span>').text(option.value).html();
        $features.append('<label class="toggle toggle--stacked">'
            + '<input type="checkbox" name="features[]" value="' + value + '" checked="checked"> '
            + '<span class="toggle__label">' + option.title + '</span>'
            + '</label>');
    },
    onRemove: function (event, option) {
        $features.find('input[value="' + option.value + '"]').parent().remove();
    }
});

// Change city list based on state/province
$('#select-locations').on('change', function () {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
            ajax: true,
            loadCities: true,
            state: $(this).val()
        },
        success: function (json) {
            const html = '<option value="">Select a location</option>';
            const selected = $citySelect.val();
            const options = json.options;
            const select = options.map(function (option) {
                return `<option value="${option.local}"
                    ${option.local == selected ? ' selected' : ''}
                    ${option.user != 'Y' ? ' data-required="true"' : ''}
                >${option.local}</option>`;
            });
            $citySelect.html(html + select);
            $cityManager.rew_manager('option', 'options', $citySelect);
            $cityManager.rew_manager('refresh');
        }
    });
});

const yearbuiltInput = document.getElementById('yearbuilt');
if (yearbuiltInput) yearbuiltInput.addEventListener('keypress', filterInput, false);