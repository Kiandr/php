/* <script> */
(function () {
	'use strict';

	// Property Features
	var $features = $('#field-features');
	if ($features.length > 0) {
		var $list = $features.find('.toggleset')
			, $options = $list.find('label')
			, featureOptions = ''
		;
		$options.each(function () {
			var $opt = $(this)
				, $input = $opt.find('input')
				, checked = $input.prop('checked')
				, value = $input.attr('name')
				, title = $opt.text().replace(/^\s+|\s+$/g,'')
			;
			featureOptions += '<option value="' + value + '"' + (checked ? ' selected' : '') + '>' + title + '</option>';
		});
		var $featureSelect = $('<select name="search_features[]" multiple>' + featureOptions + '</select>');
		$list.replaceWith($featureSelect);
		$featureSelect.select2({
			placeholder: 'Select property features',
			formatNoMatches : 'All features selected',
			minimumResultsForSearch : -1,
			matcher : function (term, text, option) {
				return true;
			}
		});
	}

})();