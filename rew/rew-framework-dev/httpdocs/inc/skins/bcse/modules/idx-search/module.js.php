<?php

// Load default "module.js.php"
$javascript = $this->locateFile('module.js.php', __FILE__);
if (!empty($javascript)) {
	require_once $javascript;
}

// Require JavaScript Code
$this->addJavascript($_SERVER['DOCUMENT_ROOT'] . '/inc/js/idx/search_tags.js');

?>
/* <script> */
(function () {
	'use strict';

	// Search Form
	var $form = $('#<?=$this->getUID() ; ?>');

	// Toggle drop down display
	$form.on('click', '.dropdown-title', function () {
		$(this).closest('li').toggleClass('active').siblings('li').removeClass('active');
	});

	// Close drop down display
	$form.on('click', '.dd-toggle, .extra-submit', function () {
		$(this).closest('li').removeClass('active');
	});

	// Toggle advanced search panels
	var $advanced = $form.find('.advanced-options');
	$form.find('.show-advanced').on('click', function (e) {
		e.preventDefault();

		// Toggle anchor text
		var $this = $(this)
			, $icon = $this.find('.inner-icon i')
			, $text = $this.find('.inner-text')
			, text = $this.data('text') || ''
		;
		if (text.length < 1) {
			text = $text.text();
			$this.data('text', text);
		}

		// Hide advanced options
		if ($advanced.hasClass('hidden')) {
			$advanced.removeClass('hidden').addClass('expanded');
			$icon.attr('class', 'icon-chevron-up');
			$text.text('Less Options');

		// Show advanced
		} else {
			$advanced.addClass('hidden').removeClass('expanded');
			$icon.attr('class', 'icon-chevron-down');
			$text.text(text);

		}
	});

	// Location Check
	$form.on('toggleLocations', function () {

		// Map Search Criteria
		var mapSearch = $form.triggerHandler('checkMap');

	    // Quick Search Fields
		$form.find(':input.location').prop('disabled', mapSearch);

	}).trigger('toggleLocations');

	// Switch current IDX feed
	var $feeds = $('.feed-switcher').on('click', 'a[data-feed]', function () {
		var $feed = $(this), feed = $feed.data('feed');
		reloadPropertyTypes(feed);
		$form.find('input[name="idx"]').val(feed);
		$form.find('input[name="feed"]').val(feed);
		$form.find('input.autocomplete').Autocomplete('refresh');
		$feed.parent().addClass('current').siblings().removeClass('current');
	});

	// Reload search panels for IDX feed
	var reloadPropertyTypes = function (feed) {
		return $.ajax({
			url: '<?=Settings::getInstance()->SETTINGS['URL_IDX_AJAX']; ?>html.php?module=<?=$this->getID() ; ?>',
			dataType: 'json',
			data: {
				feed: feed,
				options: {
					ajax: true
				}
			}
		}).done(function (data) {
			if (data && data.panels && data.panels.length > 0) {
				$.each(data.panels, function (i, panel) {
					$('#field-' + panel.id).html(panel.html);
				});
			}
		});
	};

})();
/* </script> */